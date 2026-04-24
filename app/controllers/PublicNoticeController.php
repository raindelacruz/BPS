<?php

namespace App\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\ResponseHelper;
use App\Models\ProcurementDocument;
use App\Services\ProcurementPostingService;
use App\Services\SmallValueProcurementService;

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

        $isSvp = (($workflow['parent']['procurement_mode'] ?? $workflow['parent']['mode_of_procurement'] ?? '') === SmallValueProcurementService::MODE);
        $rootType = $isSvp ? ProcurementDocument::TYPE_RFQ : ProcurementDocument::TYPE_BID_NOTICE;

        $this->view('public/view', [
            'title' => $workflow['parent']['procurement_title'],
            'bid' => $workflow['parent'],
            'relatedNotices' => array_values(array_filter(
                $workflow['timeline'],
                static fn (array $document): bool => ($document['document_type'] ?? '') !== $rootType
            )),
            'rootDocument' => $workflow['documents'][$rootType][0] ?? null,
        ], 'public');
    }

    public function file(array $params = []): void
    {
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        $isSvp = $workflow && (($workflow['parent']['procurement_mode'] ?? $workflow['parent']['mode_of_procurement'] ?? '') === SmallValueProcurementService::MODE);
        $rootType = $isSvp ? ProcurementDocument::TYPE_RFQ : ProcurementDocument::TYPE_BID_NOTICE;
        $rootDocument = $workflow['documents'][$rootType][0] ?? null;

        if (!$workflow || !$rootDocument || !$this->posting->isPubliclyVisible($workflow['parent'])) {
            ResponseHelper::abort(404, 'Public notice file not found.');
        }

        $this->streamPdf((string) $rootDocument['file_path'], [
            'source' => 'public_root_notice',
            'parent_procurement_id' => (int) ($workflow['parent']['id'] ?? 0),
            'document_type' => $rootType,
            'document_id' => (int) ($rootDocument['id'] ?? 0),
            'reference_number' => (string) ($workflow['parent']['reference_number'] ?? ''),
        ]);
    }

    public function documentFile(array $params = []): void
    {
        $type = trim((string) ($params['type'] ?? ''));
        $document = (new ProcurementDocument())->findById($type, (int) ($params['id'] ?? 0));
        $workflow = $document ? $this->posting->findParentWithWorkflow((int) $document['parent_procurement_id']) : null;
        if (!$document || !$workflow || !$this->posting->isPubliclyVisible($workflow['parent'])) {
            ResponseHelper::abort(404, 'Public notice file not found.');
        }

        $this->streamPdf((string) $document['file_path'], [
            'source' => 'public_related_document',
            'parent_procurement_id' => (int) ($document['parent_procurement_id'] ?? 0),
            'document_type' => $type,
            'document_id' => (int) ($document['id'] ?? 0),
        ]);
    }

    private function streamPdf(string $relativePath, array $context = []): void
    {
        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        if (!is_file($absolutePath)) {
            LogHelper::error('Public notice PDF file is missing.', array_merge($context, [
                'relative_path' => $relativePath,
                'absolute_path' => $absolutePath,
                'request_uri' => (string) ($_SERVER['REQUEST_URI'] ?? ''),
            ]));
            ResponseHelper::abort(404, 'Public notice file not found.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($absolutePath) . '"');
        header('Content-Length: ' . (string) filesize($absolutePath));
        readfile($absolutePath);
        exit;
    }
}
