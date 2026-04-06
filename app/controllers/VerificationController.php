<?php

namespace App\Controllers;

use App\Helpers\ValidationHelper;
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
        $state = $this->formState('verify', [
            'email' => $_GET['email'] ?? '',
            'code' => '',
        ]);

        $this->view('auth/verify', [
            'title' => 'Verify Account',
            'errors' => $state['errors'],
            'old' => $state['old'],
        ]);
    }

    public function verify(array $params = []): void
    {
        SecurityHelper::requireGuest();
        $old = [
            'email' => trim((string) ($_POST['email'] ?? '')),
            'code' => trim((string) ($_POST['code'] ?? '')),
        ];

        $this->enforceCsrfOrRedirect('verify', 'verify', $old);

        $result = $this->authService->verifyRegistration($_POST);

        if ($result['success']) {
            $this->redirect('login');
        }

        $this->redirectWithValidation('verify', 'verify', $result['errors'], $old);
    }

    public function verifyEmailChange(array $params = []): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($token === '') {
            SessionHelper::flash('error', 'Verification token is required.');
            ResponseHelper::redirect('login');
        }

        $result = $this->userManagement->verifyEmailChangeToken($token);

        if ($result['success']) {
            SessionHelper::flash('success', 'Email change verified successfully.');
            ResponseHelper::redirect('login');
        }

        SessionHelper::flash('error', implode(' ', ValidationHelper::all($result['errors'])));
        ResponseHelper::redirect('login');
    }
}
