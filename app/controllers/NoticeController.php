<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Models\ProcurementDocument;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\ProcurementPostingService;
use App\Services\SmallValueProcurementService;
use Bootstrap\Database;
use Throwable;

class NoticeController extends BaseController
{
    private ProcurementPostingService $posting;

    private FileUploadService $uploads;

    private User $users;

    public function __construct()
    {
        $this->posting = new ProcurementPostingService();
        $this->uploads = new FileUploadService();
        $this->users = new User();
    }

    public function index(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $records = $this->posting->listForUser($user);

        $scheduled = array_values(array_filter($records, static fn (array $record): bool => ($record['posting_status'] ?? '') === ProcurementPostingService::POSTING_STATUS_SCHEDULED));
        $archived = array_values(array_filter($records, static fn (array $record): bool => ($record['posting_status'] ?? '') === ProcurementPostingService::POSTING_STATUS_ARCHIVED));

        $this->view('notice/pending-list', [
            'title' => 'Procurement Postings',
            'notices' => $records,
            'scheduledNotices' => $scheduled,
            'archivedNotices' => $archived,
            'currentUser' => $user,
            'documentTypes' => $this->posting->documentTypes(),
        ]);
    }

    public function create(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->view('notice/create', [
            'title' => 'New Procurement',
        ]);
    }

    public function createCompetitiveBidding(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $state = $this->formState('notice-create-competitive-bidding', $this->competitiveBiddingDefaults($user));

        $this->view('notice/create_competitive_bidding', [
            'title' => 'Create Competitive Bidding Posting',
            'errors' => $state['errors'],
            'old' => $state['old'],
            'assignedRegion' => $user['region'] ?? '',
            'assignedBranch' => $user['branch'] ?? '',
        ]);
    }

    public function createSvp(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $state = $this->formState('notice-create-svp', $this->svpDefaults($user));

        $this->view('notice/create_svp', [
            'title' => 'Create Small Value Procurement Record',
            'errors' => $state['errors'],
            'old' => $state['old'],
            'assignedRegion' => $user['region'] ?? '',
            'assignedBranch' => $user['branch'] ?? '',
        ]);
    }

    public function createRelated(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $defaults = [
            'type' => trim((string) ($_GET['type'] ?? '')),
            'parent_procurement_id' => (int) ($_GET['parent_id'] ?? $_GET['bid_id'] ?? 0),
            'title' => '',
            'posted_at' => '',
            'description' => '',
        ];
        $state = $this->formState('notice-related-create', $defaults);
        $selectedType = trim((string) ($state['old']['type'] ?? ''));
        $selectedParentId = (int) ($state['old']['parent_procurement_id'] ?? 0);
        $eligibleParents = $selectedType !== '' ? $this->posting->eligibleParents($selectedType, $user) : [];

        if ($selectedParentId > 0) {
            $eligibleIds = array_map(static fn (array $parent): int => (int) $parent['id'], $eligibleParents);
            if (!in_array($selectedParentId, $eligibleIds, true)) {
                $selectedParentId = 0;
            }
        }

        $this->view('notice/related-create', [
            'title' => 'Post Procurement Document',
            'errors' => $state['errors'],
            'old' => array_merge($state['old'], [
                'type' => $selectedType,
                'parent_procurement_id' => $selectedParentId,
            ]),
            'relatedTypes' => $this->posting->relatedDocumentTypes(),
            'eligibleParents' => $eligibleParents,
            'selectedType' => $selectedType,
            'assignedBranch' => $user['branch'] ?? '',
        ]);
    }

    public function store(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $mode = trim((string) ($_POST['procurement_mode'] ?? $_POST['mode_of_procurement'] ?? ''));
        if ($mode === SmallValueProcurementService::MODE) {
            $this->storeSvp($params);
            return;
        }

        if ($mode !== ProcurementPostingService::COMPETITIVE_BIDDING_MODE) {
            SessionHelper::flash('error', 'Choose the procurement mode first.');
            $this->redirect('procurements/create');
        }

        $this->storeCompetitiveBidding($params);
    }

    public function storeCompetitiveBidding(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $old = array_merge($this->competitiveBiddingDefaults($user), [
            'procurement_title' => trim((string) ($_POST['procurement_title'] ?? '')),
            'reference_number' => trim((string) ($_POST['reference_number'] ?? '')),
            'abc' => trim((string) ($_POST['abc'] ?? '')),
            'posting_date' => trim((string) ($_POST['posting_date'] ?? '')),
            'bid_submission_deadline' => trim((string) ($_POST['bid_submission_deadline'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'category' => trim((string) ($_POST['category'] ?? '')),
            'end_user_unit' => trim((string) ($_POST['end_user_unit'] ?? '')),
            'procurement_mode' => ProcurementPostingService::COMPETITIVE_BIDDING_MODE,
            'mode_of_procurement' => ProcurementPostingService::COMPETITIVE_BIDDING_MODE,
        ]);
        $redirectPath = 'procurements/create/competitive-bidding';
        $formKey = 'notice-create-competitive-bidding';
        $this->enforceCsrfOrRedirect($redirectPath, $formKey, $old);
        $validation = $this->posting->validateCompetitiveBiddingInput($_POST);
        $fileErrors = $this->validatePdfUpload($_FILES['notice_pdf'] ?? null, true);
        $errors = $validation['errors'];
        foreach ($fileErrors as $field => $messages) {
            foreach ($messages as $message) {
                ValidationHelper::addError($errors, $field, $message);
            }
        }

        if (ValidationHelper::hasErrors($errors)) {
            $this->redirectWithValidation($redirectPath, $formKey, $errors, array_merge($this->competitiveBiddingDefaults($user), $validation['data']));
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $filePath = null;

        try {
            $filePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            $parentId = $this->posting->createParent($validation['data'], $user, $filePath);
            $connection->commit();

            SessionHelper::flash('success', 'Competitive Bidding record created successfully.');
            $this->redirect($this->competitiveBiddingWorkflowPath($parentId));
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->uploads->delete($filePath);
            $this->handleFormException(
                $throwable,
                'Competitive Bidding record creation failed.',
                $redirectPath,
                'Competitive Bidding record could not be created.',
                $formKey,
                array_merge($this->competitiveBiddingDefaults($user), $validation['data']),
                ['user_id' => (int) ($user['id'] ?? 0)]
            );
        }
    }

    public function storeSvp(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $old = array_merge($this->svpDefaults($user), [
            'procurement_title' => trim((string) ($_POST['procurement_title'] ?? '')),
            'reference_number' => trim((string) ($_POST['reference_number'] ?? '')),
            'abc' => trim((string) ($_POST['abc'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'category' => trim((string) ($_POST['category'] ?? '')),
            'end_user_unit' => trim((string) ($_POST['end_user_unit'] ?? '')),
            'procurement_mode' => SmallValueProcurementService::MODE,
            'mode_of_procurement' => SmallValueProcurementService::MODE,
        ]);
        $redirectPath = 'procurements/create/svp';
        $formKey = 'notice-create-svp';
        $this->enforceCsrfOrRedirect($redirectPath, $formKey, $old);
        $validation = $this->posting->validateSvpInput($_POST);

        if (ValidationHelper::hasErrors($validation['errors'])) {
            $this->redirectWithValidation($redirectPath, $formKey, $validation['errors'], array_merge($this->svpDefaults($user), $validation['data']));
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $parentId = $this->posting->createParent($validation['data'], $user, '');
            $connection->commit();

            SessionHelper::flash('success', 'Small Value Procurement record created successfully.');
            $this->redirect($this->svpWorkflowPath($parentId));
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->handleFormException(
                $throwable,
                'SVP record creation failed.',
                $redirectPath,
                'Small Value Procurement record could not be created.',
                $formKey,
                array_merge($this->svpDefaults($user), $validation['data']),
                ['user_id' => (int) ($user['id'] ?? 0)]
            );
        }
    }

    public function storeRelated(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $old = [
            'type' => trim((string) ($_POST['type'] ?? '')),
            'parent_procurement_id' => (int) ($_POST['parent_procurement_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'posted_at' => trim((string) ($_POST['posted_at'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ];
        $csrfRedirect = 'notices/related/create' . ($old['type'] !== '' ? '?type=' . urlencode((string) $old['type']) : '');
        $this->enforceCsrfOrRedirect($csrfRedirect, 'notice-related-create', $old);
        $validation = $this->posting->validateDocumentInput($_POST);
        $fileErrors = $this->validatePdfUpload($_FILES['notice_pdf'] ?? null, true);
        $type = $validation['data']['type'] ?? '';
        $redirectPath = 'notices/related/create' . ($type !== '' ? '?type=' . urlencode($type) : '');
        $errors = $validation['errors'];
        foreach ($fileErrors as $field => $messages) {
            foreach ($messages as $message) {
                ValidationHelper::addError($errors, $field, $message);
            }
        }

        if (ValidationHelper::hasErrors($errors)) {
            $this->redirectWithValidation($redirectPath, 'notice-related-create', $errors, $validation['data']);
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $filePath = null;

        try {
            $filePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            $result = $this->posting->createRelatedDocument(
                $type,
                (int) $validation['data']['parent_procurement_id'],
                $validation['data'],
                $user,
                $filePath
            );

            if (!$result['allowed']) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                $this->uploads->delete($filePath);
                $errors = [];
                foreach ($result['errors'] as $message) {
                    ValidationHelper::addError($errors, '_global', $message);
                }
                $this->redirectWithValidation($redirectPath, 'notice-related-create', $errors, $validation['data']);
            }

            $connection->commit();
            SessionHelper::flash('success', ProcurementDocument::label($type) . ' posted successfully.');
            $this->redirect('notices/' . (int) $validation['data']['parent_procurement_id']);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->uploads->delete($filePath);
            $this->handleFormException(
                $throwable,
                'Procurement document posting failed.',
                $redirectPath,
                'Document could not be posted.',
                'notice-related-create',
                $validation['data'],
                [
                    'user_id' => (int) ($user['id'] ?? 0),
                    'type' => $type,
                    'parent_procurement_id' => (int) ($validation['data']['parent_procurement_id'] ?? 0),
                ]
            );
        }
    }

    public function show(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
        }

        $this->redirect($this->workflowPathForParent($workflow['parent']));
    }

    public function showCompetitiveBidding(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
        }
        if (($workflow['parent']['procurement_mode'] ?? $workflow['parent']['mode_of_procurement'] ?? '') !== ProcurementPostingService::COMPETITIVE_BIDDING_MODE) {
            $this->redirect($this->workflowPathForParent($workflow['parent']));
        }

        $this->view('notice/competitive_bidding_view', [
            'title' => $workflow['parent']['procurement_title'],
            'parent' => $workflow['parent'],
            'documents' => $workflow['documents'],
            'timeline' => $workflow['timeline'],
            'actions' => $workflow['actions'],
            'activityLogs' => $workflow['activityLogs'],
            'currentUser' => $this->currentUser(),
        ]);
    }

    public function showSvp(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
        }
        if (($workflow['parent']['procurement_mode'] ?? $workflow['parent']['mode_of_procurement'] ?? '') !== SmallValueProcurementService::MODE) {
            $this->redirect($this->workflowPathForParent($workflow['parent']));
        }

        $this->view('notice/svp_view', [
            'title' => $workflow['parent']['procurement_title'],
            'parent' => $workflow['parent'],
            'documents' => $workflow['documents'],
            'timeline' => $workflow['timeline'],
            'actions' => $workflow['actions'],
            'activityLogs' => $workflow['activityLogs'],
            'currentUser' => $this->currentUser(),
            'svp' => $workflow['svp'] ?? null,
        ]);
    }

    public function file(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
        }

        if (($workflow['parent']['procurement_mode'] ?? $workflow['parent']['mode_of_procurement'] ?? '') === SmallValueProcurementService::MODE) {
            ResponseHelper::abort(404, 'SVP records do not use a root Bid Notice file.');
        }

        $bidNotice = $workflow['documents'][ProcurementDocument::TYPE_BID_NOTICE][0] ?? null;
        if (!$bidNotice) {
            ResponseHelper::abort(404, 'Bid notice file not found.');
        }

        $this->streamPdf((string) $bidNotice['file_path']);
    }

    public function documentFile(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $type = trim((string) ($params['type'] ?? ''));
        $document = (new ProcurementDocument())->findById($type, (int) ($params['id'] ?? 0));
        if (!$document) {
            ResponseHelper::abort(404, 'Document not found.');
        }

        $this->streamPdf((string) $document['file_path']);
    }

    public function saveSvpRfq(array $params = []): void
    {
        $this->handleSvpDocumentPost((int) ($params['id'] ?? 0), ProcurementDocument::TYPE_RFQ, 'RFQ posted successfully.');
    }

    public function issueSvpRfq(array $params = []): void
    {
        ResponseHelper::abort(410, 'Legacy SVP RFQ issue actions are disabled. Post the RFQ document once through the SVP workflow.');
    }

    public function saveSvpPosting(array $params = []): void
    {
        ResponseHelper::abort(410, 'Legacy SVP posting-compliance actions are disabled in the document-based SVP workflow.');
    }

    public function addSvpSupplier(array $params = []): void
    {
        ResponseHelper::abort(410, 'Legacy supplier actions are disabled in the document-based SVP workflow.');
    }

    public function inviteSvpSupplier(array $params = []): void
    {
        ResponseHelper::abort(410, 'Legacy supplier actions are disabled in the document-based SVP workflow.');
    }

    public function addSvpQuotation(array $params = []): void
    {
        ResponseHelper::abort(410, 'Legacy quotation actions are disabled in the document-based SVP workflow.');
    }

    public function setSvpQuotationResponsiveness(array $params = []): void
    {
        ResponseHelper::abort(410, 'Legacy quotation actions are disabled in the document-based SVP workflow.');
    }

    public function closeSvpQuotationReceipt(array $params = []): void
    {
        ResponseHelper::abort(410, 'Legacy quotation actions are disabled in the document-based SVP workflow.');
    }

    public function saveSvpEvaluation(array $params = []): void
    {
        $this->handleSvpDocumentPost((int) ($params['id'] ?? 0), ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS, 'Abstract of Quotations posted successfully.');
    }

    public function saveSvpCanvass(array $params = []): void
    {
        $this->handleSvpDocumentPost((int) ($params['id'] ?? 0), ProcurementDocument::TYPE_CANVASS, 'Canvass posted successfully.');
    }

    public function addSvpAward(array $params = []): void
    {
        $this->handleSvpDocumentPost((int) ($params['id'] ?? 0), ProcurementDocument::TYPE_AWARD, 'Award posted successfully.');
    }

    public function addSvpContract(array $params = []): void
    {
        $this->handleSvpDocumentPost((int) ($params['id'] ?? 0), ProcurementDocument::TYPE_CONTRACT_OR_PO, 'Contract / Purchase Order posted successfully.');
    }

    public function addSvpNtp(array $params = []): void
    {
        ResponseHelper::abort(410, 'SVP Notice to Proceed actions are removed. NGPA-compliant SVP uses RFQ, Abstract or Canvass, Award, and optional Contract or Purchase Order only.');
    }

    public function completeSvp(array $params = []): void
    {
        ResponseHelper::abort(410, 'Manual completion is disabled. SVP status is computed from posted documents.');
    }

    public function destroy(array $params = []): void
    {
        ResponseHelper::abort(403, 'Deleting procurement posting records is disabled in this posting module.');
    }

    private function streamPdf(string $relativePath): void
    {
        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        if (!is_file($absolutePath)) {
            ResponseHelper::abort(404, 'Document file not found.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($absolutePath) . '"');
        header('Content-Length: ' . (string) filesize($absolutePath));
        readfile($absolutePath);
        exit;
    }

    private function validatePdfUpload(?array $file, bool $required): array
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $required ? ['notice_pdf' => ['PDF file is required.']] : [];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['notice_pdf' => ['PDF upload failed.']];
        }

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $mimeType = strtolower((string) ($file['type'] ?? ''));

        if ($extension !== 'pdf' && $mimeType !== 'application/pdf') {
            return ['notice_pdf' => ['Only PDF uploads are allowed.']];
        }

        return [];
    }

    private function currentUser(): array
    {
        $currentUser = SecurityHelper::currentUser();
        $user = $currentUser ? $this->users->findById((int) $currentUser['id']) : null;
        if (!$user) {
            ResponseHelper::abort(403, 'Authenticated user context is invalid.');
        }

        return $user;
    }

    private function competitiveBiddingDefaults(array $user): array
    {
        return [
            'procurement_title' => '',
            'reference_number' => '',
            'abc' => '',
            'procurement_mode' => ProcurementPostingService::COMPETITIVE_BIDDING_MODE,
            'mode_of_procurement' => ProcurementPostingService::COMPETITIVE_BIDDING_MODE,
            'posting_date' => '',
            'bid_submission_deadline' => '',
            'description' => '',
            'category' => '',
            'end_user_unit' => '',
            'assigned_region' => $user['region'] ?? '',
            'assigned_branch' => $user['branch'] ?? '',
        ];
    }

    private function svpDefaults(array $user): array
    {
        return [
            'procurement_title' => '',
            'reference_number' => '',
            'abc' => '',
            'procurement_mode' => SmallValueProcurementService::MODE,
            'mode_of_procurement' => SmallValueProcurementService::MODE,
            'description' => '',
            'category' => '',
            'end_user_unit' => '',
            'assigned_region' => $user['region'] ?? '',
            'assigned_branch' => $user['branch'] ?? '',
        ];
    }

    private function workflowPathForParent(array $parent): string
    {
        return (($parent['procurement_mode'] ?? $parent['mode_of_procurement'] ?? '') === SmallValueProcurementService::MODE)
            ? $this->svpWorkflowPath((int) $parent['id'])
            : $this->competitiveBiddingWorkflowPath((int) $parent['id']);
    }

    private function competitiveBiddingWorkflowPath(int $parentId): string
    {
        return 'procurements/' . $parentId . '/workflow/competitive-bidding';
    }

    private function svpWorkflowPath(int $parentId): string
    {
        return 'procurements/' . $parentId . '/workflow/svp';
    }

    private function handleSvpDocumentPost(int $parentId, string $type, string $successMessage): void
    {
        SecurityHelper::requireAuth();
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect($this->svpWorkflowPath($parentId));
        }

        $user = $this->currentUser();
        $workflow = $this->posting->findParentWithWorkflow($parentId);
        if (!$workflow || (($workflow['parent']['procurement_mode'] ?? $workflow['parent']['mode_of_procurement'] ?? '') !== SmallValueProcurementService::MODE)) {
            ResponseHelper::abort(404, 'SVP procurement record not found.');
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $filePath = null;

        try {
            $validation = $this->posting->validateSvpDocumentInput($_POST, $type);
            $fileErrors = $this->validatePdfUpload($_FILES['notice_pdf'] ?? null, true);
            foreach ($fileErrors as $messages) {
                foreach ($messages as $message) {
                    ValidationHelper::addError($validation['errors'], '_global', $message);
                }
            }
            if (ValidationHelper::hasErrors($validation['errors'])) {
                SessionHelper::flash('error', ValidationHelper::all($validation['errors'])[0]);
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                $this->redirect($this->svpWorkflowPath($parentId));
            }

            $filePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            $result = $this->posting->createSvpDocument($type, $parentId, $validation['data'], $user, $filePath);
            if (!$result['allowed']) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                $this->uploads->delete($filePath);
                SessionHelper::flash('error', $result['errors'][0] ?? 'SVP document could not be posted.');
                $this->redirect($this->svpWorkflowPath($parentId));
            }

            $connection->commit();
            SessionHelper::flash('success', $successMessage);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->uploads->delete($filePath);
            SessionHelper::flash('error', $throwable->getMessage() !== '' ? $throwable->getMessage() : 'SVP action failed.');
        }

        $this->redirect($this->svpWorkflowPath($parentId));
    }

}
