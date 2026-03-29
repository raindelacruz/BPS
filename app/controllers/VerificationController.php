<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Services\AuthService;
use App\Services\UserManagementService;

class VerificationController extends BaseController
{
    private AuthService $authService;

    private UserManagementService $userManagement;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userManagement = new UserManagementService();
    }

    public function showVerify(array $params = []): void
    {
        SecurityHelper::requireGuest();

        $this->view('auth/verify', [
            'title' => 'Verify Account',
            'errors' => [],
            'old' => [
                'email' => $_GET['email'] ?? '',
                'code' => '',
            ],
        ]);
    }

    public function verify(array $params = []): void
    {
        SecurityHelper::requireGuest();

        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            $this->json([
                'success' => false,
                'message' => 'Invalid CSRF token.',
            ], 419);
        }

        $result = $this->authService->verifyRegistration($_POST);

        if ($result['success']) {
            $this->redirect('login');
        }

        $this->view('auth/verify', [
            'title' => 'Verify Account',
            'errors' => $result['errors'],
            'old' => [
                'email' => $_POST['email'] ?? '',
                'code' => $_POST['code'] ?? '',
            ],
        ]);
    }

    public function verifyEmailChange(array $params = []): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($token === '') {
            ResponseHelper::abort(400, 'Verification token is required.');
        }

        $result = $this->userManagement->verifyEmailChangeToken($token);

        if ($result['success']) {
            SessionHelper::flash('success', 'Email change verified successfully.');
            ResponseHelper::redirect('login');
        }

        ResponseHelper::abort(400, implode(' ', $result['errors']));
    }
}
