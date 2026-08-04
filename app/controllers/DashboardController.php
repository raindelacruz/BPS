<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Services\DashboardQueryService;
use App\Services\ProcurementPostingService;
use App\Services\SmallValueProcurementService;

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
            'message' => 'Summary cards and one consolidated procurement table for quick monitoring.',
            'currentUser' => $currentUser,
            'overview' => $this->dashboardQuery->overview($currentUser),
            'procurements' => $this->dashboardQuery->procurementTable($currentUser),
        ]);
    }

    public function competitiveBidding(array $params = []): void
    {
        $this->renderModuleDashboard(ProcurementPostingService::COMPETITIVE_BIDDING_MODE, 'Competitive Bidding Dashboard');
    }

    public function svp(array $params = []): void
    {
        $this->renderModuleDashboard(SmallValueProcurementService::MODE, 'SVP Dashboard');
    }

    private function renderModuleDashboard(string $mode, string $title): void
    {
        SecurityHelper::requireAuth();
        $currentUser = SecurityHelper::currentUser() ?? [];

        $this->view('dashboard/module', [
            'title' => $title,
            'currentUser' => $currentUser,
            'overview' => $this->dashboardQuery->overview($currentUser, $mode),
        ]);
    }
}
