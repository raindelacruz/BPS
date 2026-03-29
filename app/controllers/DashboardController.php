<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Services\DashboardQueryService;

class DashboardController extends BaseController
{
    private DashboardQueryService $dashboardQuery;

    public function __construct()
    {
        $this->dashboardQuery = new DashboardQueryService();
    }

    public function index(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $currentUser = SecurityHelper::currentUser() ?? [];

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'message' => 'Dashboard access is role-based, and notice statuses are recalculated on load.',
            'currentUser' => $currentUser,
            'overview' => $this->dashboardQuery->overview($currentUser),
        ]);
    }
}
