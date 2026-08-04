<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Services\ProcurementPostingService;

class WorkflowValidationController extends BaseController
{
    private ProcurementPostingService $posting;

    public function __construct()
    {
        $this->posting = new ProcurementPostingService();
    }

    public function eligibleBids(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $type = strtolower(trim((string) ($_GET['type'] ?? '')));
        $records = $this->posting->eligibleParents($type, SecurityHelper::currentUser() ?? []);

        $payload = array_map(static function (array $notice): array {
            return [
                'id' => (int) $notice['id'],
                'title' => $notice['procurement_title'],
                'reference_code' => $notice['reference_number'],
                'region' => $notice['region'],
                'procurement_type' => $notice['procurement_mode'] ?? $notice['mode_of_procurement'],
                'posting_status' => $notice['posting_status'],
                'workflow_stage' => $notice['current_stage'],
            ];
        }, $records);

        $this->json([
            'success' => true,
            'type' => $type,
            'eligible_bids' => $payload,
        ]);
    }
}
