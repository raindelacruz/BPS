<?php

namespace App\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Models\ProcurementDocument;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\ProcurementPostingService;
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

        $pending = array_values(array_filter($records, static fn (array $record): bool => ($record['status'] ?? '') === 'pending'));
        $archived = array_values(array_filter($records, static fn (array $record): bool => (int) ($record['is_archived'] ?? 0) === 1));

        $this->view('notice/pending-list', [
            'title' => 'Procurement Postings',
            'notices' => $records,
            'pendingNotices' => $pending,
            'archivedNotices' => $archived,
            'currentUser' => $user,
            'documentTypes' => $this->posting->documentTypes(),
        ]);
    }

    public function create(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $state = $this->formState('notice-create', $this->parentDefaults($user));

        $this->view('notice/create', [
            'title' => 'Create Procurement Posting',
            'errors' => $state['errors'],
            'old' => $state['old'],
            'procurementTypes' => $this->posting->procurementTypes(),
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
            'relatedTypes' => $this->posting->documentTypes(),
            'eligibleParents' => $eligibleParents,
            'selectedType' => $selectedType,
            'assignedBranch' => $user['branch'] ?? '',
        ]);
    }

    public function store(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $old = array_merge($this->parentDefaults($user), [
            'procurement_title' => trim((string) ($_POST['procurement_title'] ?? '')),
            'reference_number' => trim((string) ($_POST['reference_number'] ?? '')),
            'abc' => trim((string) ($_POST['abc'] ?? '')),
            'mode_of_procurement' => trim((string) ($_POST['mode_of_procurement'] ?? '')),
            'posting_date' => trim((string) ($_POST['posting_date'] ?? '')),
            'bid_submission_deadline' => trim((string) ($_POST['bid_submission_deadline'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ]);
        $this->enforceCsrfOrRedirect('notices/create', 'notice-create', $old);
        $validation = $this->posting->validateParentInput($_POST);
        $fileErrors = $this->validatePdfUpload($_FILES['notice_pdf'] ?? null, true);

        $errors = $validation['errors'];
        foreach ($fileErrors as $field => $messages) {
            foreach ($messages as $message) {
                ValidationHelper::addError($errors, $field, $message);
            }
        }

        if (ValidationHelper::hasErrors($errors)) {
            $this->redirectWithValidation('notices/create', 'notice-create', $errors, array_merge($this->parentDefaults($user), $validation['data']));
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $filePath = null;

        try {
            $filePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            $parentId = $this->posting->createParent($validation['data'], $user, $filePath);
            $connection->commit();

            SessionHelper::flash('success', 'Procurement record created successfully.');
            $this->redirect('notices/' . $parentId);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->uploads->delete($filePath);
            $this->handleFormException(
                $throwable,
                'Procurement record creation failed.',
                'notices/create',
                'Procurement posting could not be created.',
                'notice-create',
                array_merge($this->parentDefaults($user), $validation['data']),
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
            $result = $this->posting->createDocument(
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

        $currentUser = $this->currentUser();
        $editability = [
            ProcurementDocument::TYPE_BID_NOTICE => ['allowed' => false, 'errors' => []],
            ProcurementDocument::TYPE_SBB => [],
            ProcurementDocument::TYPE_RESOLUTION => ['allowed' => false, 'errors' => []],
            ProcurementDocument::TYPE_AWARD => ['allowed' => false, 'errors' => []],
            ProcurementDocument::TYPE_CONTRACT => ['allowed' => false, 'errors' => []],
            ProcurementDocument::TYPE_NOTICE_TO_PROCEED => ['allowed' => false, 'errors' => []],
        ];

        $bidNotice = $workflow['documents'][ProcurementDocument::TYPE_BID_NOTICE][0] ?? null;
        if ($bidNotice) {
            $editability[ProcurementDocument::TYPE_BID_NOTICE] = $this->posting->canEditDocument(
                ProcurementDocument::TYPE_BID_NOTICE,
                $bidNotice,
                $workflow['parent'],
                $workflow['documents'],
                $currentUser
            );
        }

        foreach ($workflow['documents'][ProcurementDocument::TYPE_SBB] ?? [] as $document) {
            $editability[ProcurementDocument::TYPE_SBB][(int) $document['id']] = $this->posting->canEditDocument(
                ProcurementDocument::TYPE_SBB,
                $document,
                $workflow['parent'],
                $workflow['documents'],
                $currentUser
            );
        }

        foreach ([ProcurementDocument::TYPE_RESOLUTION, ProcurementDocument::TYPE_AWARD, ProcurementDocument::TYPE_CONTRACT, ProcurementDocument::TYPE_NOTICE_TO_PROCEED] as $type) {
            $document = $workflow['documents'][$type][0] ?? null;
            if ($document) {
                $editability[$type] = $this->posting->canEditDocument(
                    $type,
                    $document,
                    $workflow['parent'],
                    $workflow['documents'],
                    $currentUser
                );
            }
        }

        $this->view('notice/view', [
            'title' => $workflow['parent']['procurement_title'],
            'parent' => $workflow['parent'],
            'documents' => $workflow['documents'],
            'timeline' => $workflow['timeline'],
            'actions' => $workflow['actions'],
            'activityLogs' => $workflow['activityLogs'],
            'currentUser' => SecurityHelper::currentUser(),
            'editability' => $editability,
        ]);
    }

    public function edit(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
        }

        $bidNotice = $workflow['documents'][ProcurementDocument::TYPE_BID_NOTICE][0] ?? null;
        if (!$bidNotice) {
            ResponseHelper::abort(404, 'Bid notice not found.');
        }

        $guard = $this->posting->canEditDocument(ProcurementDocument::TYPE_BID_NOTICE, $bidNotice, $workflow['parent'], $workflow['documents'], $this->currentUser());
        if (!$guard['allowed']) {
            ResponseHelper::abort(403, implode(' ', $guard['errors']));
        }

        $state = $this->formState('notice-edit-' . (int) ($workflow['parent']['id'] ?? 0), $this->oldFromParent($workflow['parent']));

        $this->view('notice/edit', [
            'title' => 'Edit Procurement Posting',
            'errors' => $state['errors'],
            'isParentEdit' => true,
            'notice' => $workflow['parent'],
            'old' => $state['old'],
            'procurementTypes' => $this->posting->procurementTypes(),
        ]);
    }

    public function update(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentUser();
        $parentId = (int) ($params['id'] ?? 0);
        $old = array_merge($this->parentDefaults($user), [
            'procurement_title' => trim((string) ($_POST['procurement_title'] ?? '')),
            'reference_number' => trim((string) ($_POST['reference_number'] ?? '')),
            'abc' => trim((string) ($_POST['abc'] ?? '')),
            'mode_of_procurement' => trim((string) ($_POST['mode_of_procurement'] ?? '')),
            'posting_date' => trim((string) ($_POST['posting_date'] ?? '')),
            'bid_submission_deadline' => trim((string) ($_POST['bid_submission_deadline'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ]);
        $redirectPath = 'notices/' . $parentId . '/edit';
        $this->enforceCsrfOrRedirect($redirectPath, 'notice-edit-' . $parentId, $old);
        $validation = $this->posting->validateParentInput($_POST, $parentId);
        $fileErrors = $this->validatePdfUpload($_FILES['notice_pdf'] ?? null, false);

        $errors = $validation['errors'];
        foreach ($fileErrors as $field => $messages) {
            foreach ($messages as $message) {
                ValidationHelper::addError($errors, $field, $message);
            }
        }

        if (ValidationHelper::hasErrors($errors)) {
            $this->redirectWithValidation($redirectPath, 'notice-edit-' . $parentId, $errors, array_merge($old, $validation['data']));
            return;
        }

        $workflow = $this->posting->findParentWithWorkflow($parentId);
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $newFilePath = null;
        $oldFilePath = $workflow['documents'][ProcurementDocument::TYPE_BID_NOTICE][0]['file_path'] ?? null;

        try {
            if (($_FILES['notice_pdf']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $newFilePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            }

            $result = $this->posting->updateParent($parentId, $validation['data'], $user, $newFilePath);
            if (!$result['allowed']) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                $this->uploads->delete($newFilePath);
                $errors = [];
                foreach ($result['errors'] as $message) {
                    ValidationHelper::addError($errors, '_global', $message);
                }
                $this->redirectWithValidation($redirectPath, 'notice-edit-' . $parentId, $errors, array_merge($this->oldFromParent($workflow['parent']), $validation['data']));
            }

            $connection->commit();
            if ($newFilePath !== null) {
                $this->uploads->delete($oldFilePath);
            }

            SessionHelper::flash('success', 'Procurement posting updated successfully.');
            $this->redirect('notices/' . $parentId);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->uploads->delete($newFilePath);
            $this->handleFormException(
                $throwable,
                'Procurement record update failed.',
                $redirectPath,
                'Procurement posting could not be updated.',
                'notice-edit-' . $parentId,
                array_merge($this->oldFromParent($workflow['parent']), $validation['data']),
                [
                    'user_id' => (int) ($user['id'] ?? 0),
                    'parent_procurement_id' => $parentId,
                ]
            );
        }
    }

    public function editDocument(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $type = trim((string) ($params['type'] ?? ''));
        $documentId = (int) ($params['id'] ?? 0);
        $document = (new ProcurementDocument())->findById($type, $documentId);
        if (!$document) {
            ResponseHelper::abort(404, 'Document not found.');
        }

        $workflow = $this->posting->findParentWithWorkflow((int) $document['parent_procurement_id']);
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
        }

        $guard = $this->posting->canEditDocument($type, $document, $workflow['parent'], $workflow['documents'], $this->currentUser());
        if (!$guard['allowed']) {
            ResponseHelper::abort(403, implode(' ', $guard['errors']));
        }

        $state = $this->formState('notice-document-edit-' . $type . '-' . $documentId, $this->oldFromDocument($type, $document));

        $this->view('notice/edit', [
            'title' => 'Edit ' . ProcurementDocument::label($type),
            'errors' => $state['errors'],
            'isParentEdit' => false,
            'notice' => $document,
            'documentType' => $type,
            'old' => $state['old'],
            'procurementTypes' => $this->posting->procurementTypes(),
        ]);
    }

    public function updateDocument(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $type = trim((string) ($params['type'] ?? ''));
        $documentId = (int) ($params['id'] ?? 0);
        $old = [
            'type' => $type,
            'parent_procurement_id' => (int) ($_POST['parent_procurement_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'posted_at' => trim((string) ($_POST['posted_at'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ];
        $redirectPath = 'documents/' . $type . '/' . $documentId . '/edit';
        $this->enforceCsrfOrRedirect($redirectPath, 'notice-document-edit-' . $type . '-' . $documentId, $old);
        $validation = $this->posting->validateDocumentInput($_POST + ['type' => $type]);
        $fileErrors = $this->validatePdfUpload($_FILES['notice_pdf'] ?? null, false);
        $document = (new ProcurementDocument())->findById($type, $documentId);
        if (!$document) {
            ResponseHelper::abort(404, 'Document not found.');
        }

        $errors = $validation['errors'];
        foreach ($fileErrors as $field => $messages) {
            foreach ($messages as $message) {
                ValidationHelper::addError($errors, $field, $message);
            }
        }

        if (ValidationHelper::hasErrors($errors)) {
            $this->redirectWithValidation($redirectPath, 'notice-document-edit-' . $type . '-' . $documentId, $errors, array_merge($this->oldFromDocument($type, $document), $validation['data']));
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $newFilePath = null;

        try {
            if (($_FILES['notice_pdf']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $newFilePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            }

            $result = $this->posting->updateDocument($type, $documentId, $validation['data'], $this->currentUser(), $newFilePath);
            if (!$result['allowed']) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                $this->uploads->delete($newFilePath);
                $errors = [];
                foreach ($result['errors'] as $message) {
                    ValidationHelper::addError($errors, '_global', $message);
                }
                $this->redirectWithValidation($redirectPath, 'notice-document-edit-' . $type . '-' . $documentId, $errors, array_merge($this->oldFromDocument($type, $document), $validation['data']));
            }

            $connection->commit();
            if ($newFilePath !== null) {
                $this->uploads->delete($document['file_path'] ?? null);
            }

            SessionHelper::flash('success', ProcurementDocument::label($type) . ' updated successfully.');
            $this->redirect('notices/' . (int) $document['parent_procurement_id']);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $this->uploads->delete($newFilePath);
            $this->handleFormException(
                $throwable,
                'Procurement document update failed.',
                $redirectPath,
                'Document could not be updated.',
                'notice-document-edit-' . $type . '-' . $documentId,
                array_merge($this->oldFromDocument($type, $document), $validation['data']),
                [
                    'user_id' => (int) (($this->currentUser()['id'] ?? 0)),
                    'type' => $type,
                    'document_id' => $documentId,
                ]
            );
        }
    }

    public function reopenDocument(array $params = []): void
    {
        SecurityHelper::requireAuth();
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect('notices');
        }
        $type = trim((string) ($params['type'] ?? ''));
        $documentId = (int) ($params['id'] ?? 0);
        $document = (new ProcurementDocument())->findById($type, $documentId);
        if (!$document) {
            ResponseHelper::abort(404, 'Document not found.');
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $result = $this->posting->reopenDocument($type, $documentId, $this->currentUser());
            if (!$result['allowed']) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                SessionHelper::flash('error', implode(' ', $result['errors']));
                $this->redirect('notices/' . (int) $document['parent_procurement_id']);
            }
            $connection->commit();
            SessionHelper::flash('success', ProcurementDocument::label($type) . ' reopened for editing.');
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            LogHelper::error('Document reopen failed.', [
                'type' => $type,
                'document_id' => $documentId,
                'user_id' => (int) (($this->currentUser()['id'] ?? 0)),
            ], $throwable);
            SessionHelper::flash('error', 'Document could not be reopened.');
        }

        $this->redirect('notices/' . (int) $document['parent_procurement_id']);
    }

    public function file(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $workflow = $this->posting->findParentWithWorkflow((int) ($params['id'] ?? 0));
        if (!$workflow) {
            ResponseHelper::abort(404, 'Procurement posting not found.');
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

    private function parentDefaults(array $user): array
    {
        return [
            'procurement_title' => '',
            'reference_number' => '',
            'abc' => '',
            'mode_of_procurement' => '',
            'posting_date' => '',
            'bid_submission_deadline' => '',
            'description' => '',
            'assigned_region' => $user['region'] ?? '',
            'assigned_branch' => $user['branch'] ?? '',
        ];
    }

    private function oldFromParent(array $parent): array
    {
        return [
            'procurement_title' => $parent['procurement_title'] ?? '',
            'reference_number' => $parent['reference_number'] ?? '',
            'abc' => $parent['abc'] ?? '',
            'mode_of_procurement' => $parent['mode_of_procurement'] ?? '',
            'posting_date' => isset($parent['posting_date']) ? date('Y-m-d\TH:i', strtotime((string) $parent['posting_date'])) : '',
            'bid_submission_deadline' => isset($parent['bid_submission_deadline']) ? date('Y-m-d\TH:i', strtotime((string) $parent['bid_submission_deadline'])) : '',
            'description' => $parent['description'] ?? '',
        ];
    }

    private function oldFromDocument(string $type, array $document): array
    {
        return [
            'type' => $type,
            'parent_procurement_id' => $document['parent_procurement_id'] ?? 0,
            'title' => $document['title'] ?? '',
            'posted_at' => isset($document['posted_at']) ? date('Y-m-d\TH:i', strtotime((string) $document['posted_at'])) : '',
            'description' => $document['description'] ?? '',
        ];
    }
}
