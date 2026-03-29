<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Models\Notice;
use App\Models\User;
use App\Services\DateStatusService;
use App\Services\FileUploadService;
use App\Services\ArchiveService;
use App\Services\NoticeWorkflowService;
use App\Services\NoticeValidationService;
use App\Services\PrerequisiteService;
use Bootstrap\Database;
use Throwable;

class NoticeController extends BaseController
{
    private Notice $notices;

    private User $users;

    private NoticeValidationService $validation;

    private DateStatusService $statusService;

    private FileUploadService $uploads;

    private PrerequisiteService $prerequisites;

    private NoticeWorkflowService $workflow;

    private ArchiveService $archiveService;

    public function __construct()
    {
        $this->notices = new Notice();
        $this->users = new User();
        $this->validation = new NoticeValidationService($this->notices);
        $this->statusService = new DateStatusService($this->notices);
        $this->uploads = new FileUploadService();
        $this->prerequisites = new PrerequisiteService($this->notices, $this->statusService);
        $this->workflow = new NoticeWorkflowService();
        $this->archiveService = new ArchiveService($this->notices, $this->statusService);
    }

    public function index(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $user = SecurityHelper::currentUser();
        $records = ($user['role'] ?? '') === 'admin'
            ? $this->notices->findAllBids()
            : $this->notices->findByUploader((int) $user['id']);

        $records = array_values(array_filter(
            $this->statusService->synchronizeCollection($records),
            static fn (array $notice): bool => ($notice['type'] ?? null) === 'bid'
        ));

        $pending = array_values(array_filter(
            $records,
            static fn (array $notice): bool => ($notice['status'] ?? null) === 'pending'
        ));
        $archived = array_values(array_filter(
            $records,
            static fn (array $notice): bool => (int) ($notice['is_archived'] ?? 0) === 1
        ));

        $this->view('notice/pending-list', [
            'title' => 'Bid Notices',
            'notices' => $records,
            'pendingNotices' => $pending,
            'archivedNotices' => $archived,
            'currentUser' => $user,
        ]);
    }

    public function create(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $user = $this->currentNoticeUser();

        $this->view('notice/create', [
            'title' => 'Create Bid Notice',
            'errors' => [],
            'old' => $this->bidDefaults(),
            'procurementTypes' => $this->validation->procurementTypes(),
            'assignedRegion' => $user['region'] ?? '',
            'assignedBranch' => $user['branch'] ?? '',
        ]);
    }

    public function createRelated(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $selectedType = strtolower(trim((string) ($_GET['type'] ?? '')));
        $selectedBidId = (int) ($_GET['bid_id'] ?? 0);
        if ($selectedType !== '' && !in_array($selectedType, $this->validation->relatedTypes(), true)) {
            ResponseHelper::abort(400, 'Invalid related notice type.');
        }

        $eligibleBids = $selectedType !== '' ? $this->prerequisites->eligibleParentBids($selectedType) : [];
        if ($selectedBidId > 0 && $selectedType !== '') {
            $eligibleIds = array_map(static fn (array $bid): int => (int) $bid['id'], $eligibleBids);
            if (!in_array($selectedBidId, $eligibleIds, true)) {
                $selectedBidId = 0;
            }
        }

        $this->view('notice/related-create', [
            'title' => 'Create Related Notice',
            'errors' => [],
            'old' => array_merge($this->relatedDefaults($selectedType), ['selected_bid_id' => $selectedBidId]),
            'relatedTypes' => $this->validation->relatedTypes(),
            'eligibleBids' => $eligibleBids,
            'selectedType' => $selectedType,
        ]);
    }

    public function store(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->enforceCsrf();

        $validation = $this->validation->validateBid($_POST, $_FILES['notice_pdf'] ?? null);
        $user = $this->currentNoticeUser();

        if ($validation['errors'] !== []) {
            $this->view('notice/create', [
                'title' => 'Create Bid Notice',
                'errors' => $validation['errors'],
                'old' => array_merge($this->bidDefaults(), $validation['data']),
                'procurementTypes' => $this->validation->procurementTypes(),
                'assignedRegion' => $user['region'] ?? '',
                'assignedBranch' => $user['branch'] ?? '',
            ]);
            return;
        }

        if (($user['region'] ?? '') === '') {
            $this->view('notice/create', [
                'title' => 'Create Bid Notice',
                'errors' => ['Your account does not have an assigned region.'],
                'old' => array_merge($this->bidDefaults(), $validation['data']),
                'procurementTypes' => $this->validation->procurementTypes(),
                'assignedRegion' => '',
                'assignedBranch' => $user['branch'] ?? '',
            ]);
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $filePath = null;

        try {
            $filePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            $noticeId = $this->notices->create([
                'title' => $validation['data']['title'],
                'reference_code' => $validation['data']['reference_code'],
                'type' => 'bid',
                'file_path' => $filePath,
                'start_date' => $validation['data']['start_date'],
                'end_date' => $validation['data']['end_date'],
                'uploaded_by' => (int) $user['id'],
                'description' => $validation['data']['description'],
                'is_archived' => 0,
                'status' => $this->statusService->determineStatus(
                    $validation['data']['start_date'],
                    $validation['data']['end_date']
                ),
                'region' => $user['region'],
                'branch' => $user['branch'] ?? null,
                'procurement_type' => $validation['data']['procurement_type'],
            ]);

            $connection->commit();
            SessionHelper::flash('success', 'Bid notice created successfully.');
            $this->redirect('notices/' . $noticeId);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            $this->uploads->delete($filePath);
            SessionHelper::flash('error', 'Bid notice could not be created.');
            $this->view('notice/create', [
                'title' => 'Create Bid Notice',
                'errors' => ['Bid notice could not be created.'],
                'old' => array_merge($this->bidDefaults(), $validation['data']),
                'procurementTypes' => $this->validation->procurementTypes(),
                'assignedRegion' => $user['region'] ?? '',
                'assignedBranch' => $user['branch'] ?? '',
            ]);
        }
    }

    public function storeRelated(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->enforceCsrf();

        $validation = $this->validation->validateRelatedNotice($_POST, $_FILES['notice_pdf'] ?? null);
        $type = $validation['data']['type'] ?? '';
        $eligibleBids = $type !== '' ? $this->prerequisites->eligibleParentBids($type) : [];

        if ($validation['errors'] !== []) {
            $this->view('notice/related-create', [
                'title' => 'Create Related Notice',
                'errors' => $validation['errors'],
                'old' => array_merge($this->relatedDefaults($type), $validation['data']),
                'relatedTypes' => $this->validation->relatedTypes(),
                'eligibleBids' => $eligibleBids,
                'selectedType' => $type,
            ]);
            return;
        }

        $bid = $this->statusService->synchronizeNotice($this->loadNotice((int) $validation['data']['selected_bid_id']));
        $guard = $this->prerequisites->validateForBid($type, $bid);

        if (!$guard['allowed']) {
            $this->view('notice/related-create', [
                'title' => 'Create Related Notice',
                'errors' => $guard['errors'],
                'old' => array_merge($this->relatedDefaults($type), $validation['data']),
                'relatedTypes' => $this->validation->relatedTypes(),
                'eligibleBids' => $eligibleBids,
                'selectedType' => $type,
            ]);
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $filePath = null;

        try {
            $filePath = $this->uploads->storeNoticePdf($_FILES['notice_pdf']);
            $user = SecurityHelper::currentUser();
            $noticeId = $this->notices->create(
                $this->workflow->buildRelatedNoticePayload(
                    $type,
                    $bid,
                    $validation['data'],
                    (int) $user['id'],
                    $filePath,
                    $this->statusService->determineStatus(
                        $validation['data']['start_date'],
                        $validation['data']['end_date']
                    )
                )
            );

            $connection->commit();
            SessionHelper::flash('success', ucfirst($type) . ' notice created successfully.');
            $this->redirect('notices/' . $noticeId);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            $this->uploads->delete($filePath);
            $this->view('notice/related-create', [
                'title' => 'Create Related Notice',
                'errors' => ['Related notice could not be created.'],
                'old' => array_merge($this->relatedDefaults($type), $validation['data']),
                'relatedTypes' => $this->validation->relatedTypes(),
                'eligibleBids' => $eligibleBids,
                'selectedType' => $type,
            ]);
        }
    }

    public function show(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $notice = $this->loadNotice((int) ($params['id'] ?? 0));
        $notice = $this->statusService->synchronizeNotice($notice);
        $workflowSet = $this->statusService->synchronizeCollection(
            $this->notices->findByReferenceCode((string) $notice['reference_code'])
        );
        $rootBid = $this->notices->findWorkflowBidByReferenceCode((string) $notice['reference_code']);
        $archiveGuard = $this->archiveService->canArchive($notice, SecurityHelper::currentUser() ?? []);

        $this->view('notice/view', [
            'title' => $notice['title'],
            'notice' => $notice,
            'workflowSet' => $workflowSet,
            'rootBid' => $rootBid,
            'canArchive' => $archiveGuard['allowed'],
            'archiveErrors' => $archiveGuard['errors'],
            'currentUser' => SecurityHelper::currentUser(),
        ]);
    }

    public function edit(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $notice = $this->statusService->synchronizeNotice($this->loadNotice((int) ($params['id'] ?? 0)));
        $this->assertCanManagePendingNotice($notice);

        $this->view('notice/edit', [
            'title' => 'Edit Bid Notice',
            'notice' => $notice,
            'errors' => [],
            'old' => $this->oldFromNotice($notice),
            'procurementTypes' => $this->validation->procurementTypes(),
            'assignedRegion' => $notice['region'] ?? '',
            'assignedBranch' => $notice['branch'] ?? '',
        ]);
    }

    public function update(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->enforceCsrf();

        $notice = $this->statusService->synchronizeNotice($this->loadNotice((int) ($params['id'] ?? 0)));
        $this->assertCanManagePendingNotice($notice);

        $file = $_FILES['notice_pdf'] ?? null;
        $requiresFile = !$notice['file_path'];
        $validation = ($notice['type'] ?? null) === 'bid'
            ? $this->validation->validateBid($_POST, $file, (int) $notice['id'], $requiresFile)
            : $this->validation->validateRelatedNotice($_POST, $file, $requiresFile);

        if ($validation['errors'] !== []) {
            $this->view('notice/edit', [
                'title' => 'Edit Notice',
                'notice' => $notice,
                'errors' => $validation['errors'],
                'old' => array_merge($this->oldFromNotice($notice), $validation['data']),
                'procurementTypes' => $this->validation->procurementTypes(),
                'assignedRegion' => $notice['region'] ?? '',
                'assignedBranch' => $notice['branch'] ?? '',
            ]);
            return;
        }

        $connection = Database::connection();
        $connection->beginTransaction();
        $newFilePath = null;

        try {
            $filePath = $notice['file_path'];

            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $newFilePath = $this->uploads->storeNoticePdf($file);
                $filePath = $newFilePath;
            }

            $payload = [
                'title' => $validation['data']['title'],
                'reference_code' => $notice['reference_code'],
                'file_path' => $filePath,
                'start_date' => $validation['data']['start_date'],
                'end_date' => $validation['data']['end_date'],
                'description' => $validation['data']['description'],
                'status' => $this->statusService->determineStatus(
                    $validation['data']['start_date'],
                    $validation['data']['end_date']
                ),
                'region' => $notice['region'],
                'branch' => $notice['branch'] ?? null,
                'procurement_type' => $notice['procurement_type'],
            ];

            if (($notice['type'] ?? null) === 'bid') {
                $payload['reference_code'] = $validation['data']['reference_code'];
                $payload['procurement_type'] = $validation['data']['procurement_type'];
            }

            $this->notices->updateById((int) $notice['id'], $payload);

            $connection->commit();
            if ($newFilePath) {
                $this->uploads->delete($notice['file_path']);
            }
            SessionHelper::flash('success', 'Bid notice updated successfully.');
            $this->redirect('notices/' . $notice['id']);
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            $this->uploads->delete($newFilePath);
            SessionHelper::flash('error', 'Notice could not be updated.');
            $this->view('notice/edit', [
                'title' => 'Edit Notice',
                'notice' => $notice,
                'errors' => ['Notice could not be updated.'],
                'old' => array_merge($this->oldFromNotice($notice), $validation['data']),
                'procurementTypes' => $this->validation->procurementTypes(),
                'assignedRegion' => $notice['region'] ?? '',
                'assignedBranch' => $notice['branch'] ?? '',
            ]);
        }
    }

    public function destroy(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->enforceCsrf();

        $notice = $this->statusService->synchronizeNotice($this->loadNotice((int) ($params['id'] ?? 0)));
        $this->assertCanDeleteNotice($notice);

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $this->notices->deleteById((int) $notice['id']);
            $this->uploads->delete($notice['file_path'] ?? null);
            $connection->commit();

            SessionHelper::flash('success', 'Bid notice deleted successfully.');
            $this->redirect('notices');
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            SessionHelper::flash('error', 'Bid notice could not be deleted.');
            $this->redirect('notices/' . $notice['id']);
        }
    }

    public function file(array $params = []): void
    {
        SecurityHelper::requireAuth();

        $notice = $this->loadNotice((int) ($params['id'] ?? 0));
        $relativePath = $notice['file_path'] ?? null;

        if (!$relativePath) {
            ResponseHelper::abort(404, 'Notice file not found.');
        }

        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (!is_file($absolutePath)) {
            ResponseHelper::abort(404, 'Notice file not found.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($absolutePath) . '"');
        header('Content-Length: ' . (string) filesize($absolutePath));
        readfile($absolutePath);
        exit;
    }

    private function loadNotice(int $id): array
    {
        $notice = $this->notices->findById($id);

        if (!$notice) {
            ResponseHelper::abort(404, 'Notice not found.');
        }

        return $notice;
    }

    private function assertCanManagePendingNotice(array $notice): void
    {
        $user = SecurityHelper::currentUser();
        $isOwner = (int) $notice['uploaded_by'] === (int) ($user['id'] ?? 0);
        $isAdmin = ($user['role'] ?? null) === 'admin';

        if (!$isOwner && !$isAdmin) {
            ResponseHelper::abort(403, 'You do not have permission to edit this notice.');
        }

        if (($notice['status'] ?? null) !== 'pending') {
            ResponseHelper::abort(403, 'Only pending notices may be edited.');
        }
    }

    private function assertCanDeleteNotice(array $notice): void
    {
        $user = SecurityHelper::currentUser();
        $isOwner = (int) $notice['uploaded_by'] === (int) ($user['id'] ?? 0);

        if (!$isOwner) {
            ResponseHelper::abort(403, 'Only the uploader may delete this notice.');
        }
    }

    private function enforceCsrf(): void
    {
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            ResponseHelper::abort(419, 'Invalid CSRF token.');
        }
    }

    private function bidDefaults(): array
    {
        $user = $this->currentNoticeUser();

        return [
            'title' => '',
            'reference_code' => '',
            'procurement_type' => '',
            'start_date' => '',
            'end_date' => '',
            'description' => '',
            'assigned_region' => $user['region'] ?? '',
            'assigned_branch' => $user['branch'] ?? '',
        ];
    }

    private function oldFromNotice(array $notice): array
    {
        $data = [
            'type' => $notice['type'] ?? '',
            'selected_bid_id' => 0,
            'title' => $notice['title'] ?? '',
            'reference_code' => $notice['reference_code'] ?? '',
            'procurement_type' => $notice['procurement_type'] ?? '',
            'start_date' => isset($notice['start_date']) ? date('Y-m-d\TH:i', strtotime((string) $notice['start_date'])) : '',
            'end_date' => isset($notice['end_date']) ? date('Y-m-d\TH:i', strtotime((string) $notice['end_date'])) : '',
            'description' => $notice['description'] ?? '',
            'assigned_region' => $notice['region'] ?? '',
            'assigned_branch' => $notice['branch'] ?? '',
        ];

        if (($notice['type'] ?? null) !== 'bid') {
            $rootBid = $this->notices->findWorkflowBidByReferenceCode((string) ($notice['reference_code'] ?? ''));
            $data['selected_bid_id'] = (int) ($rootBid['id'] ?? 0);
        }

        return $data;
    }

    private function relatedDefaults(string $type = ''): array
    {
        return [
            'type' => $type,
            'selected_bid_id' => 0,
            'title' => '',
            'start_date' => '',
            'end_date' => '',
            'description' => '',
        ];
    }

    private function currentNoticeUser(): array
    {
        $currentUser = SecurityHelper::currentUser();
        $userId = (int) ($currentUser['id'] ?? 0);
        $user = $userId > 0 ? $this->users->findById($userId) : null;

        if (!$user) {
            ResponseHelper::abort(403, 'Authenticated user context is invalid.');
        }

        return $user;
    }
}
