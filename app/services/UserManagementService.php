<?php

namespace App\Services;

use App\Helpers\LogHelper;
use App\Helpers\RegionBranchHelper;
use App\Helpers\ValidationHelper;
use App\Models\EmailChangeRequest;
use App\Models\Notice;
use App\Models\User;
use Bootstrap\Database;
use DateInterval;
use DateTimeImmutable;
use Throwable;

class UserManagementService extends BaseService
{
    public function __construct(
        private readonly ?User $users = null,
        private readonly ?Notice $notices = null,
        private readonly ?EmailChangeRequest $emailChangeRequests = null,
        private readonly ?EmailService $emailService = null
    ) {
    }

    public function listUsers(): array
    {
        return ($this->users ?? new User())->all();
    }

    public function updateUser(int $targetUserId, array $input, array $currentUser): array
    {
        $users = $this->users ?? new User();
        $emailChangeRequests = $this->emailChangeRequests ?? new EmailChangeRequest();
        $emailService = $this->emailService ?? new EmailService();
        $target = $users->findById($targetUserId);

        if (!$target) {
            return ['success' => false, 'errors' => ['_global' => ['User not found.']]];
        }

        $data = [
            'username' => trim((string) ($input['username'] ?? '')),
            'firstname' => trim((string) ($input['firstname'] ?? '')),
            'middle_initial' => strtoupper(substr(trim((string) ($input['middle_initial'] ?? '')), 0, 1)),
            'lastname' => trim((string) ($input['lastname'] ?? '')),
            'region' => trim((string) ($input['region'] ?? '')),
            'branch' => trim((string) ($input['branch'] ?? '')),
            'role' => trim((string) ($input['role'] ?? '')),
            'email' => strtolower(trim((string) ($input['email'] ?? ''))),
        ];

        $errors = [];

        foreach (['username', 'firstname', 'lastname', 'region', 'branch', 'role', 'email'] as $field) {
            if ($data[$field] === '') {
                ValidationHelper::addError($errors, $field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        if ($data['region'] !== '' && !RegionBranchHelper::isValidRegion($data['region'])) {
            ValidationHelper::addError($errors, 'region', 'Region is invalid.');
        }

        if (
            $data['region'] !== ''
            && $data['branch'] !== ''
            && !RegionBranchHelper::branchBelongsToRegion($data['region'], $data['branch'])
        ) {
            ValidationHelper::addError($errors, 'branch', 'Branch does not match the selected region.');
        }

        if (!in_array($data['role'], ['admin', 'author'], true)) {
            ValidationHelper::addError($errors, 'role', 'Role is invalid.');
        }

        if ($data['middle_initial'] !== '' && !preg_match('/^[A-Z]$/', $data['middle_initial'])) {
            ValidationHelper::addError($errors, 'middle_initial', 'Middle initial must be a single letter.');
        }

        if (!preg_match('/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.gov\.ph$/i', $data['email'])) {
            ValidationHelper::addError($errors, 'email', 'Email must use a valid .gov.ph address.');
        }

        if ($users->usernameExistsForOther($data['username'], $targetUserId)) {
            ValidationHelper::addError($errors, 'username', 'Username is already in use.');
        }

        if ($users->emailExistsForOther($data['email'], $targetUserId)) {
            ValidationHelper::addError($errors, 'email', 'Email is already in use.');
        }

        if ((int) $currentUser['id'] === $targetUserId && $data['role'] !== ($target['role'] ?? null)) {
            ValidationHelper::addError($errors, 'role', 'You cannot change your own role.');
        }

        if (ValidationHelper::hasErrors($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $emailChanged = $data['email'] !== (string) $target['email'];
            $userUpdate = array_merge($target, $data, [
                'email' => $emailChanged ? (string) $target['email'] : $data['email'],
            ]);

            $users->updateById($targetUserId, $userUpdate);

            if ($emailChanged) {
                $emailChangeRequests->cancelPendingForUser($targetUserId);

                $expiresAt = (new DateTimeImmutable())->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');
                $token = bin2hex(random_bytes(32));

                $requestId = $emailChangeRequests->create([
                    'user_id' => $targetUserId,
                    'current_email' => $target['email'],
                    'new_email' => $data['email'],
                    'token' => $token,
                    'expires_at' => $expiresAt,
                ]);

                $emailService->sendEmailChangeNotice(
                    $data['email'],
                    trim($data['firstname'] . ' ' . $data['lastname']),
                    $token,
                    new DateTimeImmutable($expiresAt)
                );
            }

            $connection->commit();

            return ['success' => true, 'errors' => []];
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            LogHelper::error('User update failed.', [
                'target_user_id' => $targetUserId,
                'current_user_id' => (int) ($currentUser['id'] ?? 0),
            ], $throwable);

            return ['success' => false, 'errors' => ['_global' => ['User could not be updated.']]];
        }
    }

    public function toggleActiveState(int $targetUserId, array $currentUser): array
    {
        $users = $this->users ?? new User();
        $target = $users->findById($targetUserId);

        if (!$target) {
            return ['success' => false, 'errors' => ['_global' => ['User not found.']]];
        }

        if ((int) $currentUser['id'] === $targetUserId) {
            return ['success' => false, 'errors' => ['_global' => ['You cannot deactivate your own account.']]];
        }

        $nextState = (int) ($target['is_active'] ?? 0) !== 1;
        $users->updateActiveState($targetUserId, $nextState);

        return ['success' => true, 'errors' => []];
    }

    public function deleteUser(int $targetUserId, array $currentUser): array
    {
        $users = $this->users ?? new User();
        $notices = $this->notices ?? new Notice();
        $target = $users->findById($targetUserId);

        if (!$target) {
            return ['success' => false, 'errors' => ['_global' => ['User not found.']]];
        }

        if ((int) $currentUser['id'] === $targetUserId) {
            return ['success' => false, 'errors' => ['_global' => ['You cannot delete your own account.']]];
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $notices->reassignUploader($targetUserId, (int) $currentUser['id']);
            $users->deleteById($targetUserId);
            $connection->commit();

            return ['success' => true, 'errors' => []];
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            LogHelper::error('User deletion failed.', [
                'target_user_id' => $targetUserId,
                'current_user_id' => (int) ($currentUser['id'] ?? 0),
            ], $throwable);

            return ['success' => false, 'errors' => ['_global' => ['User could not be deleted.']]];
        }
    }

    public function verifyEmailChangeToken(string $token): array
    {
        $users = $this->users ?? new User();
        $requests = $this->emailChangeRequests ?? new EmailChangeRequest();
        $request = $requests->findPendingByToken($token);

        if (!$request) {
            return ['success' => false, 'errors' => ['_global' => ['Email change token is invalid or expired.']]];
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $users->updateEmailById((int) $request['user_id'], (string) $request['new_email']);
            $requests->markCompleted((int) $request['id']);
            $connection->commit();

            return ['success' => true, 'errors' => []];
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            LogHelper::error('Email change verification failed.', [
                'request_id' => (int) ($request['id'] ?? 0),
                'user_id' => (int) ($request['user_id'] ?? 0),
            ], $throwable);

            return ['success' => false, 'errors' => ['_global' => ['Email change could not be completed.']]];
        }
    }
}
