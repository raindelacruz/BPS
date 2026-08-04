<?php

namespace App\Controllers;

use App\Helpers\FormStateHelper;
use App\Helpers\RegionBranchHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
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
        $filters = [
            'search' => trim((string) ($_GET['search'] ?? '')),
            'region' => trim((string) ($_GET['region'] ?? '')),
            'role' => trim((string) ($_GET['role'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
        ];
        $editingUserId = (int) ($_GET['view'] ?? ($_GET['edit'] ?? 0));
        $editState = $editingUserId > 0
            ? FormStateHelper::consume('user-edit-' . $editingUserId)
            : ['errors' => [], 'old' => []];

        $this->view('user/index', [
            'title' => 'User Management',
            'users' => $this->userManagement->listUsers($filters),
            'currentUser' => $currentUser,
            'roles' => ['author', 'admin'],
            'statuses' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'regions' => RegionBranchHelper::regions(),
            'filters' => $filters,
            'editingUserId' => $editingUserId,
            'editState' => $editState,
        ]);
    }

    public function update(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');
        $targetId = (int) ($params['id'] ?? 0);
        $old = [
            'username' => trim((string) ($_POST['username'] ?? '')),
            'region' => trim((string) ($_POST['region'] ?? '')),
            'branch' => trim((string) ($_POST['branch'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'role' => trim((string) ($_POST['role'] ?? '')),
            'status' => trim((string) ($_POST['status'] ?? '')),
            'password' => '',
            'password_confirmation' => '',
        ];
        $returnFilters = $this->userFilterQueryFromPost();
        $redirectPath = 'users?' . http_build_query(array_merge($returnFilters, ['view' => $targetId]));
        $this->enforceCsrfOrRedirect($redirectPath, 'user-edit-' . $targetId, $old);

        $result = $this->userManagement->updateUser($targetId, $_POST, SecurityHelper::currentUser() ?? []);

        if (!$result['success']) {
            $this->redirectWithValidation($redirectPath, 'user-edit-' . $targetId, $result['errors'], $old);
        }

        if ((int) (SecurityHelper::currentUser()['id'] ?? 0) === $targetId) {
            $this->authService->refreshSessionUser($targetId);
        }

        SessionHelper::flash('success', 'User updated successfully.');
        $this->redirect('users' . ($returnFilters === [] ? '' : '?' . http_build_query($returnFilters)));
    }

    public function toggleActive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');
        $targetId = (int) ($params['id'] ?? 0);

        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect('users');
        }

        $result = $this->userManagement->toggleActiveState($targetId, SecurityHelper::currentUser() ?? []);

        if (!$result['success']) {
            SessionHelper::flash('error', implode(' ', ValidationHelper::all($result['errors'])));
        } else {
            SessionHelper::flash('success', 'User status updated successfully.');
        }

        $this->redirect('users');
    }

    public function destroy(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');
        $targetId = (int) ($params['id'] ?? 0);

        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect('users');
        }

        $result = $this->userManagement->deleteUser($targetId, SecurityHelper::currentUser() ?? []);

        if (!$result['success']) {
            SessionHelper::flash('error', implode(' ', ValidationHelper::all($result['errors'])));
        } else {
            SessionHelper::flash('success', 'User deleted successfully.');
        }

        $this->redirect('users');
    }

    private function userFilterQueryFromPost(): array
    {
        $filters = [
            'search' => trim((string) ($_POST['_filter_search'] ?? '')),
            'region' => trim((string) ($_POST['_filter_region'] ?? '')),
            'role' => trim((string) ($_POST['_filter_role'] ?? '')),
            'status' => trim((string) ($_POST['_filter_status'] ?? '')),
        ];

        return array_filter($filters, static fn (string $value): bool => $value !== '');
    }
}
