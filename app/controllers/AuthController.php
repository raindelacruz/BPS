<?php

namespace App\Controllers;

use App\Helpers\RegionBranchHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $state = $this->formState('login', ['username' => '']);

        $this->view('auth/login', [
            'title' => 'Login',
            'errors' => $state['errors'],
            'old' => $state['old'],
        ]);
    }

    public function login(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $old = [
            'username' => trim((string) ($_POST['username'] ?? '')),
        ];

        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            $this->authService->recordCsrfFailure($old['username']);
            $errors = [];
            ValidationHelper::addError($errors, '_global', 'Your session expired. Please try again.');
            $this->redirectWithValidation('login', 'login', $errors, $old, 'Your session expired. Please submit the form again.');
        }

        $result = $this->authService->attemptLogin($_POST);

        if ($result['success']) {
            $this->redirect('dashboard');
        }

        $this->redirectWithValidation('login', 'login', $result['errors'], $old);
    }

    public function showRegister(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $state = $this->formState('register', $this->registrationDefaults());

        $this->view('auth/register', [
            'title' => 'Register',
            'errors' => $state['errors'],
            'old' => $state['old'],
            'regions' => RegionBranchHelper::regions(),
        ]);
    }

    public function register(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $old = array_merge($this->registrationDefaults(), [
            'username' => trim((string) ($_POST['username'] ?? '')),
            'firstname' => trim((string) ($_POST['firstname'] ?? '')),
            'middle_initial' => trim((string) ($_POST['middle_initial'] ?? '')),
            'lastname' => trim((string) ($_POST['lastname'] ?? '')),
            'region' => trim((string) ($_POST['region'] ?? '')),
            'branch' => trim((string) ($_POST['branch'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
        ]);
        $this->enforceCsrfOrRedirect('register', 'register', $old);

        $result = $this->authService->register($_POST);

        if ($result['success']) {
            $this->redirect('login');
        }

        $this->redirectWithValidation('register', 'register', $result['errors'], $old);
    }

    public function logout(array $params = []): void
    {
        SecurityHelper::requireAuth();
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            $this->redirectWithError('dashboard', 'Your session expired. Please sign in again if needed.');
        }

        $this->authService->logout();
        $this->redirect('login');
    }

    public function showForgotPassword(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $state = $this->formState('forgot-password', ['email' => '']);

        $this->view('auth/forgot-password', [
            'title' => 'Forgot Password',
            'errors' => $state['errors'],
            'old' => $state['old'],
        ]);
    }

    public function sendPasswordReset(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $old = [
            'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
        ];
        $this->enforceCsrfOrRedirect('password/forgot', 'forgot-password', $old);

        $result = $this->authService->requestPasswordReset($_POST);

        if ($result['success']) {
            $this->redirect('login');
        }

        $this->redirectWithValidation('password/forgot', 'forgot-password', $result['errors'], $old);
    }

    public function showResetPassword(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $token = trim((string) ($_GET['token'] ?? ''));
        $state = $this->formState('reset-password', [
            'token' => $token,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $this->view('auth/reset-password', [
            'title' => 'Reset Password',
            'errors' => $state['errors'],
            'old' => $state['old'],
        ]);
    }

    public function resetPassword(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $old = [
            'token' => trim((string) ($_POST['token'] ?? '')),
            'password' => '',
            'password_confirmation' => '',
        ];
        $this->enforceCsrfOrRedirect('password/reset?token=' . urlencode($old['token']), 'reset-password', $old);

        $result = $this->authService->resetPassword($_POST);

        if ($result['success']) {
            $this->redirect('login');
        }

        $this->redirectWithValidation('password/reset?token=' . urlencode($old['token']), 'reset-password', $result['errors'], $old);
    }

    private function registrationDefaults(): array
    {
        return [
            'username' => '',
            'firstname' => '',
            'middle_initial' => '',
            'lastname' => '',
            'region' => '',
            'branch' => '',
            'email' => '',
        ];
    }
}
