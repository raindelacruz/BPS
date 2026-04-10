<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\ProcurementDocument;
use App\Services\ProcurementPostingService;

class PublicNoticeController extends BaseController
{
    private ProcurementPostingService $posting;

    public function __construct()
    {
        $this->posting = new ProcurementPostingService();
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
            'bids' => $this->posting->publicList($search, $region, $procurementType),
        ], 'public');
    }

    public function show(array $params = []): void
    {
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        if (!$workflow || !$this->posting->isPubliclyVisible($workflow['parent'])) {
            ResponseHelper::abort(404, 'Public notice not found.');
        }

        $this->view('public/view', [
            'title' => $workflow['parent']['procurement_title'],
            'bid' => $workflow['parent'],
            'relatedNotices' => array_values(array_filter(
                $workflow['timeline'],
                static fn (array $document): bool => ($document['document_type'] ?? '') !== ProcurementDocument::TYPE_BID_NOTICE
            )),
            'bidNotice' => $workflow['documents'][ProcurementDocument::TYPE_BID_NOTICE][0] ?? null,
        ], 'public');
    }

    public function file(array $params = []): void
    {
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        $bidNotice = $workflow['documents'][ProcurementDocument::TYPE_BID_NOTICE][0] ?? null;

        if (!$workflow || !$bidNotice || !$this->posting->isPubliclyVisible($workflow['parent'])) {
            ResponseHelper::abort(404, 'Public notice file not found.');
        }

        $this->streamPdf((string) $bidNotice['file_path']);
    }

    public function documentFile(array $params = []): void
    {
        $type = trim((string) ($params['type'] ?? ''));
        $document = (new ProcurementDocument())->findById($type, (int) ($params['id'] ?? 0));
        $workflow = $document ? $this->posting->findParentWithWorkflow((int) $document['parent_procurement_id']) : null;
        if (!$document || !$workflow || !$this->posting->isPubliclyVisible($workflow['parent'])) {
            ResponseHelper::abort(404, 'Public notice file not found.');
        }

        $this->streamPdf((string) $document['file_path']);
    }

    private function streamPdf(string $relativePath): void
    {
        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
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
