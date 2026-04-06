<?php

namespace App\Controllers;

use App\Helpers\RegionBranchHelper;
use App\Helpers\SecurityHelper;
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
        $this->enforceCsrfOrRedirect('login', 'login', $old);

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
