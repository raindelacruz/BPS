<?php

namespace App\Services;

use App\Helpers\LogHelper;
use App\Helpers\RegionBranchHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Models\LoginAttemptLog;
use App\Models\PasswordReset;
use App\Models\User;
use Bootstrap\Database;
use DateInterval;
use DateTimeImmutable;
use Throwable;

class AuthService extends BaseService
{
    private User $users;

    private LoginAttemptLog $loginLogs;

    private PasswordReset $passwordResets;

    private EmailService $emailService;

    public function __construct(
        ?User $users = null,
        ?LoginAttemptLog $loginLogs = null,
        ?PasswordReset $passwordResets = null,
        ?EmailService $emailService = null
    )
    {
        $this->users = $users ?? new User();
        $this->loginLogs = $loginLogs ?? new LoginAttemptLog();
        $this->passwordResets = $passwordResets ?? new PasswordReset();
        $this->emailService = $emailService ?? new EmailService();
    }

    public function register(array $input): array
    {
        $data = $this->normalizeRegistrationInput($input);
        $errors = $this->validateRegistration($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

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
                'verification_code' => null,
                'token_expiry' => null,
                'is_verified' => 1,
                'is_active' => 0,
            ]);

            $connection->commit();

            SessionHelper::flash('success', 'Registration complete. Your account is awaiting activation.');

            return [
                'success' => true,
                'errors' => [],
                'user_id' => $userId,
            ];
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            LogHelper::error('Registration failed.', [
                'username' => $data['username'],
                'email' => $data['email'],
            ], $throwable);

            return [
                'success' => false,
                'errors' => ['_global' => ['Registration failed. Please try again.']],
            ];
        }
    }

    public function verifyRegistration(array $input): array
    {
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $code = trim((string) ($input['code'] ?? ''));
        $errors = [];

        if ($email === '') {
            ValidationHelper::addError($errors, 'email', 'Email is required.');
        }

        if (!preg_match('/^\d{6}$/', $code)) {
            ValidationHelper::addError($errors, 'code', 'Verification code must be 6 digits.');
        }

        if (ValidationHelper::hasErrors($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->users->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'errors' => ['email' => ['No account matches the provided email address.']]];
        }

        if ((int) $user['is_verified'] === 1) {
            return ['success' => false, 'errors' => ['email' => ['This account is already verified.']]];
        }

        if (($user['verification_code'] ?? null) !== $code) {
            return ['success' => false, 'errors' => ['code' => ['Verification code is invalid.']]];
        }

        $expiry = $user['token_expiry'] ?? null;

        if (!$expiry || strtotime($expiry) < time()) {
            return ['success' => false, 'errors' => ['code' => ['Verification code has expired.']]];
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
            ValidationHelper::addError($errors, 'username', 'Username is required.');
        }

        if ($password === '') {
            ValidationHelper::addError($errors, 'password', 'Password is required.');
        }

        if (ValidationHelper::hasErrors($errors)) {
            $this->recordLoginEvent([
                'username_entered' => $username,
                'event_type' => 'login_attempt',
                'outcome' => 'failure',
                'failure_reason' => $username === '' && $password === ''
                    ? 'missing_fields'
                    : ($username === '' ? 'missing_username' : 'missing_password'),
                'message' => 'Login form was submitted with required fields missing.',
                'context' => ['error_fields' => array_keys($errors)],
            ]);

            return ['success' => false, 'errors' => $errors];
        }

        $rateLimit = $this->loginRateLimitGuard($username);
        if ($rateLimit !== null) {
            return $rateLimit;
        }

        $user = $this->users->findByUsername($username);

        if (!$user) {
            $this->recordLoginEvent([
                'username_entered' => $username,
                'event_type' => 'login_attempt',
                'outcome' => 'failure',
                'failure_reason' => 'username_not_found',
                'message' => 'No user account matches the submitted username.',
            ]);

            return ['success' => false, 'errors' => ['_global' => ['Invalid credentials.']]];
        }

        if (!password_verify($password, (string) $user['password'])) {
            $this->recordLoginEvent([
                'user_id' => (int) $user['id'],
                'username_entered' => $username,
                'event_type' => 'login_attempt',
                'outcome' => 'failure',
                'failure_reason' => 'invalid_password',
                'message' => 'Password verification failed for an existing user.',
            ]);

            return ['success' => false, 'errors' => ['_global' => ['Invalid credentials.']]];
        }

        if ((int) $user['is_active'] !== 1) {
            $this->recordLoginEvent([
                'user_id' => (int) $user['id'],
                'username_entered' => $username,
                'event_type' => 'login_attempt',
                'outcome' => 'failure',
                'failure_reason' => 'inactive_account',
                'message' => 'Credentials were valid, but the account is inactive.',
                'context' => [
                    'is_verified' => (int) ($user['is_verified'] ?? 0),
                    'role' => $user['role'] ?? null,
                ],
            ]);

            return ['success' => false, 'errors' => ['_global' => ['Your account is not active yet. Please wait for account activation.']]];
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

        $this->recordLoginEvent([
            'user_id' => (int) $user['id'],
            'username_entered' => $username,
            'event_type' => 'login_attempt',
            'outcome' => 'success',
            'message' => 'User logged in successfully.',
        ]);

        return ['success' => true, 'errors' => []];
    }

    public function logout(): void
    {
        $user = SessionHelper::get('auth_user');

        if (is_array($user)) {
            $this->recordLoginEvent([
                'user_id' => (int) ($user['id'] ?? 0),
                'username_entered' => $user['username'] ?? null,
                'event_type' => 'logout',
                'outcome' => 'success',
                'message' => 'User logged out.',
            ]);
        }

        SessionHelper::destroy();
    }

    public function recordCsrfFailure(string $username = ''): void
    {
        $this->recordLoginEvent([
            'username_entered' => trim($username),
            'event_type' => 'csrf_failure',
            'outcome' => 'failure',
            'failure_reason' => 'csrf_failed',
            'message' => 'Login form submission failed CSRF validation or the session expired.',
        ]);
    }

    private function recordLoginEvent(array $data): void
    {
        try {
            $context = $data['context'] ?? null;

            $this->loginLogs->create([
                'user_id' => empty($data['user_id']) ? null : (int) $data['user_id'],
                'username_entered' => $data['username_entered'] ?? null,
                'event_type' => $data['event_type'],
                'outcome' => $data['outcome'],
                'failure_reason' => $data['failure_reason'] ?? null,
                'message' => $data['message'] ?? null,
                'ip_address' => $this->clientIpAddress(),
                'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255) ?: null,
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? null,
                'request_uri' => substr((string) ($_SERVER['REQUEST_URI'] ?? ''), 0, 255) ?: null,
                'context' => $context === null ? null : json_encode($context, JSON_UNESCAPED_SLASHES),
            ]);
        } catch (Throwable $throwable) {
            LogHelper::error('Login attempt could not be recorded.', [
                'event_type' => $data['event_type'] ?? null,
                'outcome' => $data['outcome'] ?? null,
                'failure_reason' => $data['failure_reason'] ?? null,
                'username_entered' => $data['username_entered'] ?? null,
            ], $throwable);
        }
    }

    private function loginRateLimitGuard(string $username): ?array
    {
        $maxAttempts = max(1, (int) app('app.login_max_attempts', 5));
        $decayMinutes = max(1, (int) app('app.login_decay_minutes', 15));
        $ipAddress = $this->clientIpAddress();
        $recentFailures = $this->loginLogs->countRecentFailures($username, $ipAddress, $decayMinutes);

        if ($recentFailures < $maxAttempts) {
            return null;
        }

        $this->recordLoginEvent([
            'username_entered' => $username,
            'event_type' => 'login_attempt',
            'outcome' => 'failure',
            'failure_reason' => 'rate_limited',
            'message' => 'Login attempt blocked by rate limit.',
            'context' => [
                'window_minutes' => $decayMinutes,
                'max_attempts' => $maxAttempts,
            ],
        ]);

        return [
            'success' => false,
            'errors' => [
                '_global' => ['Too many failed login attempts. Please wait before trying again.'],
            ],
        ];
    }

    private function clientIpAddress(): ?string
    {
        $candidates = [
            $_SERVER['HTTP_CLIENT_IP'] ?? '',
            $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
            $_SERVER['REMOTE_ADDR'] ?? '',
        ];

        foreach ($candidates as $candidate) {
            $ip = trim(explode(',', (string) $candidate)[0]);

            if ($ip !== '') {
                return substr($ip, 0, 45);
            }
        }

        return null;
    }

    public function changePassword(int $userId, array $input): array
    {
        $newPassword = (string) ($input['password'] ?? '');
        $confirmation = (string) ($input['password_confirmation'] ?? '');
        $errors = [];

        if ($newPassword === '') {
            ValidationHelper::addError($errors, 'password', 'New password is required.');
        }

        if ($confirmation === '') {
            ValidationHelper::addError($errors, 'password_confirmation', 'Password confirmation is required.');
        }

        if ($newPassword !== $confirmation) {
            ValidationHelper::addError($errors, 'password_confirmation', 'Password confirmation does not match.');
        }

        $user = $this->users->findById($userId);

        if (!$user) {
            ValidationHelper::addError($errors, '_global', 'User not found.');
        }

        if (ValidationHelper::hasErrors($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $updated = $this->users->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));

        if (!$updated) {
            return ['success' => false, 'errors' => ['_global' => ['Password could not be updated.']]];
        }

        SessionHelper::flash('success', 'Password updated successfully.');

        return ['success' => true, 'errors' => []];
    }

    public function requestPasswordReset(array $input): array
    {
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $errors = [];

        if ($email === '') {
            ValidationHelper::addError($errors, 'email', 'Email is required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ValidationHelper::addError($errors, 'email', 'Enter a valid email address.');
        }

        if (ValidationHelper::hasErrors($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->users->findByEmail($email);

        if ($user && (int) ($user['is_active'] ?? 0) === 1) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = (new DateTimeImmutable())->add(new DateInterval('PT30M'));
            $created = $this->passwordResets->create(
                (int) $user['id'],
                hash('sha256', $token),
                $expiresAt->format('Y-m-d H:i:s')
            );

            if ($created) {
                $this->emailService->sendPasswordReset(
                    (string) $user['email'],
                    trim((string) ($user['firstname'] ?? '') . ' ' . (string) ($user['lastname'] ?? '')),
                    $token,
                    $expiresAt
                );
            }
        }

        SessionHelper::flash('success', 'If an active account matches that email, a password reset link has been sent.');

        return ['success' => true, 'errors' => []];
    }

    public function resetPassword(array $input): array
    {
        $token = trim((string) ($input['token'] ?? ''));
        $newPassword = (string) ($input['password'] ?? '');
        $confirmation = (string) ($input['password_confirmation'] ?? '');
        $errors = [];

        if ($token === '') {
            ValidationHelper::addError($errors, '_global', 'Password reset link is invalid or expired.');
        }

        if ($newPassword === '') {
            ValidationHelper::addError($errors, 'password', 'New password is required.');
        }

        if ($confirmation === '') {
            ValidationHelper::addError($errors, 'password_confirmation', 'Password confirmation is required.');
        }

        if ($newPassword !== $confirmation) {
            ValidationHelper::addError($errors, 'password_confirmation', 'Password confirmation does not match.');
        }

        $resetRequest = $token === ''
            ? null
            : $this->passwordResets->findValidByTokenHash(hash('sha256', $token));

        if (!$resetRequest) {
            ValidationHelper::addError($errors, '_global', 'Password reset link is invalid or expired.');
        }

        if (ValidationHelper::hasErrors($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $userId = (int) $resetRequest['user_id'];
        $updated = $this->users->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));

        if (!$updated) {
            return ['success' => false, 'errors' => ['_global' => ['Password could not be updated.']]];
        }

        $this->passwordResets->markUsed((int) $resetRequest['id']);
        SessionHelper::flash('success', 'Password reset successfully. You may now log in.');

        return ['success' => true, 'errors' => []];
    }

    public function updateProfile(int $userId, array $input): array
    {
        $user = $this->users->findById($userId);

        if (!$user) {
            return ['success' => false, 'errors' => ['_global' => ['Profile not found.']]];
        }

        $data = [
            'email' => strtolower(trim((string) ($input['email'] ?? ''))),
            'region' => trim((string) ($input['region'] ?? '')),
            'branch' => trim((string) ($input['branch'] ?? '')),
        ];

        $errors = [];

        foreach (['email', 'region', 'branch'] as $field) {
            if ($data[$field] === '') {
                ValidationHelper::addError($errors, $field, ucfirst($field) . ' is required.');
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

        if (!preg_match('/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.gov\.ph$/i', $data['email'])) {
            ValidationHelper::addError($errors, 'email', 'Email must use a valid .gov.ph address.');
        }

        if ($this->users->emailExistsForOther($data['email'], $userId)) {
            ValidationHelper::addError($errors, 'email', 'Email is already in use.');
        }

        if (ValidationHelper::hasErrors($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'data' => array_merge($user, $data),
            ];
        }

        $updated = $this->users->updateProfileById($userId, $data);

        if (!$updated) {
            return [
                'success' => false,
                'errors' => ['_global' => ['Profile could not be updated.']],
                'data' => array_merge($user, $data),
            ];
        }

        $this->refreshSessionUser($userId);
        SessionHelper::flash('success', 'Profile updated successfully.');

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

        if ($data['middle_initial'] !== '' && !preg_match('/^[A-Z]$/', $data['middle_initial'])) {
            ValidationHelper::addError($errors, 'middle_initial', 'Middle initial must be a single letter.');
        }

        if (!preg_match('/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.gov\.ph$/i', $data['email'])) {
            ValidationHelper::addError($errors, 'email', 'Email must use a valid .gov.ph address.');
        }

        if ($data['password'] !== $data['password_confirmation']) {
            ValidationHelper::addError($errors, 'password_confirmation', 'Password confirmation does not match.');
        }

        if ($this->users->usernameExists($data['username'])) {
            ValidationHelper::addError($errors, 'username', 'Username is already in use.');
        }

        if ($this->users->emailExists($data['email'])) {
            ValidationHelper::addError($errors, 'email', 'Email is already in use.');
        }

        return $errors;
    }
}
