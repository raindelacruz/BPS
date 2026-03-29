<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Services\PrerequisiteService;

class WorkflowValidationController extends BaseController
{
    private PrerequisiteService $prerequisites;

    public function __construct()
    {
        $this->prerequisites = new PrerequisiteService();
    }

    public function eligibleBids(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $type = strtolower(trim((string) ($_GET['type'] ?? '')));
        $records = $this->prerequisites->eligibleParentBids($type);

        $payload = array_map(static function (array $notice): array {
            return [
                'id' => (int) $notice['id'],
                'title' => $notice['title'],
                'reference_code' => $notice['reference_code'],
                'region' => $notice['region'],
                'procurement_type' => $notice['procurement_type'],
                'status' => $notice['status'],
            ];
        }, $records);

        $this->json([
            'success' => true,
            'type' => $type,
            'eligible_bids' => $payload,
        ]);
    }
}
