<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Notice;
use App\Services\PublicNoticeQueryService;

class PublicNoticeController extends BaseController
{
    private PublicNoticeQueryService $queryService;

    public function __construct()
    {
        $this->queryService = new PublicNoticeQueryService(new Notice());
    }

    public function index(array $params = []): void
    {
        $search = trim((string) ($_GET['search'] ?? ''));
        $region = trim((string) ($_GET['region'] ?? ''));
        $procurementType = trim((string) ($_GET['procurement_type'] ?? ''));

        $this->view('public/index', [
            'title' => 'Public Notices',
            'filters' => [
                'search' => $search,
                'region' => $region,
                'procurement_type' => $procurementType,
            ],
            'bids' => $this->queryService->listPublicBids($search, $region, $procurementType),
        ], 'public');
    }

    public function show(array $params = []): void
    {
        $bid = $this->queryService->publicBidById((int) ($params['id'] ?? 0));

        if (!$bid) {
            ResponseHelper::abort(404, 'Public notice not found.');
        }

        $this->view('public/view', [
            'title' => $bid['title'],
            'bid' => $bid,
            'relatedNotices' => array_values(array_filter(
                $this->queryService->publicWorkflowSet((string) $bid['reference_code']),
                static fn (array $notice): bool => ($notice['type'] ?? null) !== 'bid'
            )),
        ], 'public');
    }

    public function file(array $params = []): void
    {
        $bid = $this->queryService->publicVisibleNoticeById((int) ($params['id'] ?? 0));

        if (!$bid || empty($bid['file_path'])) {
            ResponseHelper::abort(404, 'Public notice file not found.');
        }

        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string) $bid['file_path']);

        if (!is_file($absolutePath)) {
            ResponseHelper::abort(404, 'Public notice file not found.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($absolutePath) . '"');
        header('Content-Length: ' . (string) filesize($absolutePath));
        readfile($absolutePath);
        exit;
    }
}
