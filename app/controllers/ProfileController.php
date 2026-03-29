<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Models\User;
use App\Services\AuthService;

class ProfileController extends BaseController
{
    private User $users;

    private AuthService $authService;

    public function __construct()
    {
        $this->users = new User();
        $this->authService = new AuthService($this->users);
    }

    public function show(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $currentUser = SecurityHelper::currentUser();
        $user = $this->users->findById((int) ($currentUser['id'] ?? 0));

        if (!$user) {
            ResponseHelper::abort(404, 'Profile not found.');
        }

        $this->view('profile/account', [
            'title' => 'My Account',
            'user' => $user,
            'errors' => [],
        ]);
    }

    public function updatePassword(array $params = []): void
    {
        SecurityHelper::requireAuth();

        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            ResponseHelper::abort(419, 'Invalid CSRF token.');
        }

        $currentUser = SecurityHelper::currentUser();
        $result = $this->authService->changePassword((int) ($currentUser['id'] ?? 0), $_POST);

        if (!$result['success']) {
            $user = $this->users->findById((int) ($currentUser['id'] ?? 0));
            $this->view('profile/account', [
                'title' => 'My Account',
                'user' => $user,
                'errors' => $result['errors'],
            ]);
            return;
        }

        $this->redirect('profile');
    }
}
