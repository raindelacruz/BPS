<?php

namespace App\Controllers;

use App\Helpers\RegionBranchHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Services\AuthService;
use App\Services\UserManagementService;

class UserController extends BaseController
{
    private UserManagementService $userManagement;

    private AuthService $authService;

    public function __construct()
    {
        $this->userManagement = new UserManagementService();
        $this->authService = new AuthService();
    }

    public function index(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');

        $currentUser = SecurityHelper::currentUser();
        $this->view('user/index', [
            'title' => 'User Management',
            'users' => $this->userManagement->listUsers(),
            'currentUser' => $currentUser,
            'roles' => ['author', 'admin'],
            'regions' => RegionBranchHelper::regions(),
        ]);
    }

    public function update(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');
        $this->enforceCsrf();

        $targetId = (int) ($params['id'] ?? 0);
        $result = $this->userManagement->updateUser($targetId, $_POST, SecurityHelper::currentUser() ?? []);

        if (!$result['success']) {
            SessionHelper::flash('error', implode(' ', $result['errors']));
            $this->redirect('users');
        }

        if ((int) (SecurityHelper::currentUser()['id'] ?? 0) === $targetId) {
            $this->authService->refreshSessionUser($targetId);
        }

        SessionHelper::flash('success', 'User updated successfully.');
        $this->redirect('users');
    }

    public function toggleActive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');
        $this->enforceCsrf();

        $result = $this->userManagement->toggleActiveState((int) ($params['id'] ?? 0), SecurityHelper::currentUser() ?? []);

        if (!$result['success']) {
            SessionHelper::flash('error', implode(' ', $result['errors']));
        } else {
            SessionHelper::flash('success', 'User status updated successfully.');
        }

        $this->redirect('users');
    }

    public function destroy(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');
        $this->enforceCsrf();

        $result = $this->userManagement->deleteUser((int) ($params['id'] ?? 0), SecurityHelper::currentUser() ?? []);

        if (!$result['success']) {
            SessionHelper::flash('error', implode(' ', $result['errors']));
        } else {
            SessionHelper::flash('success', 'User deleted and notices reassigned successfully.');
        }

        $this->redirect('users');
    }

    private function enforceCsrf(): void
    {
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            ResponseHelper::abort(419, 'Invalid CSRF token.');
        }
    }
}
