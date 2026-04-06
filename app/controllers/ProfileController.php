<?php

namespace App\Controllers;

use App\Helpers\RegionBranchHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Services\AuthService;
use App\Models\User;

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

        $profileState = $this->formState('profile-details', [
            'email' => $user['email'] ?? '',
            'region' => $user['region'] ?? '',
            'branch' => $user['branch'] ?? '',
        ]);
        $passwordState = $this->formState('profile-password', [
            'password' => '',
            'password_confirmation' => '',
        ]);

        $this->view('profile/account', [
            'title' => 'My Account',
            'user' => $user,
            'errors' => [],
            'profileErrors' => $profileState['errors'],
            'profileOld' => $profileState['old'],
            'passwordErrors' => $passwordState['errors'],
            'passwordOld' => $passwordState['old'],
            'regions' => RegionBranchHelper::regions(),
        ]);
    }

    public function update(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $old = [
            'email' => trim((string) ($_POST['email'] ?? '')),
            'region' => trim((string) ($_POST['region'] ?? '')),
            'branch' => trim((string) ($_POST['branch'] ?? '')),
        ];
        $this->enforceCsrfOrRedirect('profile', 'profile-details', $old);

        $currentUser = SecurityHelper::currentUser();
        $result = $this->authService->updateProfile((int) ($currentUser['id'] ?? 0), $_POST);

        if (!$result['success']) {
            $data = $result['data'] ?? [];
            $this->redirectWithValidation('profile', 'profile-details', $result['errors'], [
                'email' => $data['email'] ?? $old['email'],
                'region' => $data['region'] ?? $old['region'],
                'branch' => $data['branch'] ?? $old['branch'],
            ]);
            return;
        }

        $this->redirect('profile');
    }

    public function updatePassword(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $old = [
            'password' => '',
            'password_confirmation' => '',
        ];
        $this->enforceCsrfOrRedirect('profile', 'profile-password', $old);

        $currentUser = SecurityHelper::currentUser();
        $result = $this->authService->changePassword((int) ($currentUser['id'] ?? 0), $_POST);

        if (!$result['success']) {
            $this->redirectWithValidation('profile', 'profile-password', $result['errors'], $old);
            return;
        }

        $this->redirect('profile');
    }
}
