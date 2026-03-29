<?php

namespace App\Services;

use App\Helpers\RegionBranchHelper;
use App\Helpers\SessionHelper;
use App\Models\User;
use Bootstrap\Database;
use DateInterval;
use DateTimeImmutable;
use Throwable;

class AuthService extends BaseService
{
    private User $users;

    private EmailService $emailService;

    public function __construct(?User $users = null, ?EmailService $emailService = null)
    {
        $this->users = $users ?? new User();
        $this->emailService = $emailService ?? new EmailService();
    }

    public function register(array $input): array
    {
        $data = $this->normalizeRegistrationInput($input);
        $errors = $this->validateRegistration($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = (new DateTimeImmutable())->add(
            new DateInterval('PT' . max(1, (int) app('app.verification_code_expiry_minutes', 15)) . 'M')
        );

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $userId = $this->users->create([
                'username' => $data['username'],
                'firstname' => $data['firstname'],
                'middle_initial' => $data['middle_initial'],
                'lastname' => $data['lastname'],
                'region' => $data['region'],
                'branch' => $data['branch'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => 'author',
                'email' => $data['email'],
                'verification_code' => $code,
                'token_expiry' => $expiresAt->format('Y-m-d H:i:s'),
                'is_verified' => 0,
                'is_active' => 0,
            ]);

            $displayName = trim($data['firstname'] . ' ' . $data['lastname']);
            $sent = $this->emailService->sendRegistrationVerification(
                $data['email'],
                $displayName,
                $code,
                $expiresAt
            );

            if (!$sent) {
                throw new \RuntimeException('Verification email could not be sent.');
            }

            $connection->commit();

            SessionHelper::flash('success', 'Registration complete. Check your verification code to activate your account.');

            return [
                'success' => true,
                'errors' => [],
                'user_id' => $userId,
            ];
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            return [
                'success' => false,
                'errors' => ['Registration failed. Please try again.'],
            ];
        }
    }

    public function verifyRegistration(array $input): array
    {
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $code = trim((string) ($input['code'] ?? ''));
        $errors = [];

        if ($email === '') {
            $errors[] = 'Email is required.';
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            $errors[] = 'Verification code must be 6 digits.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->users->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'errors' => ['No account matches the provided email address.']];
        }

        if ((int) $user['is_verified'] === 1) {
            return ['success' => false, 'errors' => ['This account is already verified.']];
        }

        if (($user['verification_code'] ?? null) !== $code) {
            return ['success' => false, 'errors' => ['Verification code is invalid.']];
        }

        $expiry = $user['token_expiry'] ?? null;

        if (!$expiry || strtotime($expiry) < time()) {
            return ['success' => false, 'errors' => ['Verification code has expired.']];
        }

        $this->users->markVerified((int) $user['id']);
        SessionHelper::flash('success', 'Account verified. You may now log in.');

        return ['success' => true, 'errors' => []];
    }

    public function attemptLogin(array $input): array
    {
        $username = trim((string) ($input['username'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $errors = [];

        if ($username === '') {
            $errors[] = 'Username is required.';
        }

        if ($password === '') {
            $errors[] = 'Password is required.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->users->findByUsername($username);

        if (!$user || !password_verify($password, (string) $user['password'])) {
            return ['success' => false, 'errors' => ['Invalid credentials.']];
        }

        if ((int) $user['is_active'] !== 1) {
            return ['success' => false, 'errors' => ['Your account is not active yet. Please complete verification first.']];
        }

        SessionHelper::put('auth_user', [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'region' => $user['region'],
            'branch' => $user['branch'],
            'role' => $user['role'],
            'email' => $user['email'],
        ]);

        SessionHelper::flash('success', 'Welcome back.');

        return ['success' => true, 'errors' => []];
    }

    public function logout(): void
    {
        SessionHelper::destroy();
    }

    public function changePassword(int $userId, array $input): array
    {
        $currentPassword = (string) ($input['current_password'] ?? '');
        $newPassword = (string) ($input['password'] ?? '');
        $confirmation = (string) ($input['password_confirmation'] ?? '');
        $errors = [];

        if ($currentPassword === '') {
            $errors[] = 'Current password is required.';
        }

        if ($newPassword === '') {
            $errors[] = 'New password is required.';
        }

        if ($confirmation === '') {
            $errors[] = 'Password confirmation is required.';
        }

        if ($newPassword !== $confirmation) {
            $errors[] = 'Password confirmation does not match.';
        }

        $user = $this->users->findById($userId);

        if (!$user || !password_verify($currentPassword, (string) ($user['password'] ?? ''))) {
            $errors[] = 'Current password is incorrect.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $updated = $this->users->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));

        if (!$updated) {
            return ['success' => false, 'errors' => ['Password could not be updated.']];
        }

        SessionHelper::flash('success', 'Password updated successfully.');

        return ['success' => true, 'errors' => []];
    }

    public function refreshSessionUser(int $userId): void
    {
        $user = $this->users->findById($userId);

        if (!$user) {
            return;
        }

        SessionHelper::put('auth_user', [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'region' => $user['region'],
            'branch' => $user['branch'],
            'role' => $user['role'],
            'email' => $user['email'],
        ]);
    }

    private function normalizeRegistrationInput(array $input): array
    {
        return [
            'username' => trim((string) ($input['username'] ?? '')),
            'firstname' => trim((string) ($input['firstname'] ?? '')),
            'middle_initial' => strtoupper(substr(trim((string) ($input['middle_initial'] ?? '')), 0, 1)),
            'lastname' => trim((string) ($input['lastname'] ?? '')),
            'region' => trim((string) ($input['region'] ?? '')),
            'branch' => trim((string) ($input['branch'] ?? '')),
            'email' => strtolower(trim((string) ($input['email'] ?? ''))),
            'password' => (string) ($input['password'] ?? ''),
            'password_confirmation' => (string) ($input['password_confirmation'] ?? ''),
        ];
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];

        foreach (['username', 'firstname', 'lastname', 'region', 'branch', 'email', 'password', 'password_confirmation'] as $field) {
            if ($data[$field] === '') {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        if ($data['region'] !== '' && !RegionBranchHelper::isValidRegion($data['region'])) {
            $errors[] = 'Region is invalid.';
        }

        if (
            $data['region'] !== ''
            && $data['branch'] !== ''
            && !RegionBranchHelper::branchBelongsToRegion($data['region'], $data['branch'])
        ) {
            $errors[] = 'Branch does not match the selected region.';
        }

        if ($data['middle_initial'] !== '' && !preg_match('/^[A-Z]$/', $data['middle_initial'])) {
            $errors[] = 'Middle initial must be a single letter.';
        }

        if (!preg_match('/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.gov\.ph$/i', $data['email'])) {
            $errors[] = 'Email must use a valid .gov.ph address.';
        }

        if ($data['password'] !== $data['password_confirmation']) {
            $errors[] = 'Password confirmation does not match.';
        }

        if ($this->users->usernameExists($data['username'])) {
            $errors[] = 'Username is already in use.';
        }

        if ($this->users->emailExists($data['email'])) {
            $errors[] = 'Email is already in use.';
        }

        return $errors;
    }
}
