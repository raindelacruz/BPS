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

        $this->view('auth/login', [
            'title' => 'Login',
            'errors' => [],
            'old' => ['username' => ''],
        ]);
    }

    public function login(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $this->enforceCsrf();

        $result = $this->authService->attemptLogin($_POST);

        if ($result['success']) {
            $this->redirect('dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'errors' => $result['errors'],
            'old' => [
                'username' => $_POST['username'] ?? '',
            ],
        ]);
    }

    public function showRegister(array $params = []): void
    {
        SecurityHelper::requireGuest();

        $this->view('auth/register', [
            'title' => 'Register',
            'errors' => [],
            'old' => $this->registrationDefaults(),
            'regions' => RegionBranchHelper::regions(),
        ]);
    }

    public function register(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $this->enforceCsrf();

        $result = $this->authService->register($_POST);

        if ($result['success']) {
            $this->redirect('login');
        }

        $this->view('auth/register', [
            'title' => 'Register',
            'errors' => $result['errors'],
            'old' => array_merge($this->registrationDefaults(), [
                'username' => $_POST['username'] ?? '',
                'firstname' => $_POST['firstname'] ?? '',
                'middle_initial' => $_POST['middle_initial'] ?? '',
                'lastname' => $_POST['lastname'] ?? '',
                'region' => $_POST['region'] ?? '',
                'branch' => $_POST['branch'] ?? '',
                'email' => $_POST['email'] ?? '',
            ]),
            'regions' => RegionBranchHelper::regions(),
        ]);
    }

    public function logout(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->enforceCsrf();

        $this->authService->logout();
        $this->redirect('login');
    }

    private function enforceCsrf(): void
    {
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            $this->json([
                'success' => false,
                'message' => 'Invalid CSRF token.',
            ], 419);
        }
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
