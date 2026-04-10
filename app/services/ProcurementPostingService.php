<?php

namespace App\Services;

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ValidationHelper;
use App\Models\ParentProcurement;
use App\Models\ProcurementActivityLog;
use App\Models\ProcurementDocument;
use DateTimeImmutable;

class ProcurementPostingService extends BaseService
{
    public const POSTING_STATUS_SCHEDULED = 'scheduled';
    public const POSTING_STATUS_OPEN = 'open';
    public const POSTING_STATUS_CLOSED = 'closed';
    public const POSTING_STATUS_ARCHIVED = 'archived';

    public function __construct(
        private readonly ?ParentProcurement $parents = null,
        private readonly ?ProcurementDocument $documents = null,
        private readonly ?ProcurementActivityLog $activityLogs = null,
        private readonly ?FileUploadService $uploads = null
    ) {
    }

    public function procurementTypes(): array
    {
        return ProcurementTypeHelper::all();
    }

    public function documentTypes(): array
    {
        return ProcurementDocument::types();
    }

    public function validateParentInput(array $input, ?int $ignoreId = null): array
    {
        $data = [
            'procurement_title' => trim((string) ($input['procurement_title'] ?? '')),
            'reference_number' => trim((string) ($input['reference_number'] ?? '')),
            'abc' => trim((string) ($input['abc'] ?? '')),
            'mode_of_procurement' => trim((string) ($input['mode_of_procurement'] ?? '')),
            'posting_date' => trim((string) ($input['posting_date'] ?? '')),
            'bid_submission_deadline' => trim((string) ($input['bid_submission_deadline'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
        ];
        $errors = [];

        foreach (['procurement_title', 'reference_number', 'abc', 'mode_of_procurement', 'posting_date', 'bid_submission_deadline', 'description'] as $field) {
            if ($data[$field] === '') {
                ValidationHelper::addError($errors, $field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        if ($data['mode_of_procurement'] !== '' && !in_array($data['mode_of_procurement'], ProcurementTypeHelper::values(), true)) {
            ValidationHelper::addError($errors, 'mode_of_procurement', 'Mode of procurement is invalid.');
        }

        if ($data['reference_number'] !== '' && ($this->parents ?? new ParentProcurement())->referenceNumberExists($data['reference_number'], $ignoreId)) {
            ValidationHelper::addError($errors, 'reference_number', 'Reference number is already used by another procurement posting.');
        }

        if ($data['abc'] !== '') {
            $normalizedAbc = str_replace(',', '', $data['abc']);
            if (!is_numeric($normalizedAbc) || (float) $normalizedAbc < 0) {
                ValidationHelper::addError($errors, 'abc', 'ABC must be a valid non-negative amount.');
            } else {
                $data['abc'] = number_format((float) $normalizedAbc, 2, '.', '');
            }
        }

        if ($data['posting_date'] !== '' && $data['bid_submission_deadline'] !== '') {
            try {
                $postingDate = new DateTimeImmutable($data['posting_date']);
                $deadline = new DateTimeImmutable($data['bid_submission_deadline']);

                if ($deadline <= $postingDate) {
                    ValidationHelper::addError($errors, 'bid_submission_deadline', 'Bid submission deadline must be later than the posting date.');
                }

                $data['posting_date'] = $postingDate->format('Y-m-d H:i:s');
                $data['bid_submission_deadline'] = $deadline->format('Y-m-d H:i:s');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'posting_date', 'Posting date or bid submission deadline is invalid.');
            }
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    public function validateDocumentInput(array $input): array
    {
        $data = [
            'type' => trim((string) ($input['type'] ?? '')),
            'parent_procurement_id' => (int) ($input['parent_procurement_id'] ?? 0),
            'title' => trim((string) ($input['title'] ?? '')),
            'posted_at' => trim((string) ($input['posted_at'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
        ];
        $errors = [];

        foreach (['type', 'parent_procurement_id', 'title', 'posted_at', 'description'] as $field) {
            if ($data[$field] === '' || $data[$field] === 0) {
                ValidationHelper::addError($errors, $field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        if ($data['type'] !== '' && !in_array($data['type'], ProcurementDocument::types(), true)) {
            ValidationHelper::addError($errors, 'type', 'Document type is invalid.');
        }

        if ($data['posted_at'] !== '') {
            try {
                $data['posted_at'] = (new DateTimeImmutable($data['posted_at']))->format('Y-m-d H:i:s');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'posted_at', 'Posted at value is invalid.');
            }
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    public function determinePostingStatus(array $parent, ?DateTimeImmutable $now = null): string
    {
        if (!empty($parent['archived_at'])) {
            return self::POSTING_STATUS_ARCHIVED;
        }

        $now ??= new DateTimeImmutable();
        $postingDate = new DateTimeImmutable((string) $parent['posting_date']);
        $deadline = new DateTimeImmutable((string) $parent['bid_submission_deadline']);

        if ($deadline < $now) {
            return self::POSTING_STATUS_CLOSED;
        }

        if ($postingDate > $now) {
            return self::POSTING_STATUS_SCHEDULED;
        }

        return self::POSTING_STATUS_OPEN;
    }

    public function currentStage(array $documentsByType): string
    {
        foreach ([
            ProcurementDocument::TYPE_NOTICE_TO_PROCEED,
            ProcurementDocument::TYPE_CONTRACT,
            ProcurementDocument::TYPE_AWARD,
            ProcurementDocument::TYPE_RESOLUTION,
            ProcurementDocument::TYPE_SBB,
            ProcurementDocument::TYPE_BID_NOTICE,
        ] as $type) {
            if (!empty($documentsByType[$type])) {
                return $type;
            }
        }

        return ProcurementDocument::TYPE_BID_NOTICE;
    }

    public function listForUser(array $user): array
    {
        $parents = ($user['role'] ?? '') === 'admin'
            ? ($this->parents ?? new ParentProcurement())->findAll()
            : ($this->parents ?? new ParentProcurement())->findByCreator((int) ($user['id'] ?? 0));

        return array_map(fn (array $parent): array => $this->refreshParentState($parent), $parents);
    }

    public function eligibleParents(string $type, array $user): array
    {
        $type = trim($type);
        if (!in_array($type, ProcurementDocument::types(), true)) {
            return [];
        }

        $parents = ($user['role'] ?? '') === 'admin'
            ? ($this->parents ?? new ParentProcurement())->findAll()
            : (($user['branch'] ?? '') !== ''
                ? ($this->parents ?? new ParentProcurement())->findByBranch((string) $user['branch'])
                : []);

        $eligible = [];
        foreach ($parents as $parent) {
            $parent = $this->refreshParentState($parent);
            $guard = $this->canCreateDocument($type, $parent);
            if ($guard['allowed']) {
                $eligible[] = $parent;
            }
        }

        return $eligible;
    }

    public function findParentWithWorkflow(int $parentId): ?array
    {
        $parent = ($this->parents ?? new ParentProcurement())->findById($parentId);
        if (!$parent) {
            return null;
        }

        $parent = $this->refreshParentState($parent);
        $documents = $this->documentsForParent($parentId);
        $actions = [];

        foreach (ProcurementDocument::types() as $type) {
            $actions[$type] = $this->canCreateDocument($type, $parent, $documents);
        }

        return [
            'parent' => $parent,
            'documents' => $documents,
            'timeline' => ($this->documents ?? new ProcurementDocument())->allForParent($parentId),
            'actions' => $actions,
            'activityLogs' => ($this->activityLogs ?? new ProcurementActivityLog())->findByParent($parentId),
        ];
    }

    public function createParent(array $data, array $user, string $filePath): int
    {
        $parentModel = $this->parents ?? new ParentProcurement();
        $documentModel = $this->documents ?? new ProcurementDocument();
        $activityLog = $this->activityLogs ?? new ProcurementActivityLog();
        $uploads = $this->uploads ?? new FileUploadService();
        $status = $this->determinePostingStatus($data);
        $fileHash = $uploads->hash($filePath);

        $parentId = $parentModel->create([
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'mode_of_procurement' => $data['mode_of_procurement'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'posting_status' => $status,
            'current_stage' => ProcurementDocument::TYPE_BID_NOTICE,
            'archived_at' => null,
            'archive_reason' => null,
            'archived_by' => null,
            'archive_approval_reference' => null,
            'archive_approved_by' => null,
            'archive_approved_at' => null,
            'region' => $user['region'],
            'branch' => $user['branch'] ?? null,
            'created_by' => $user['id'],
            'updated_by' => $user['id'],
        ]);

        $documentId = $documentModel->create(ProcurementDocument::TYPE_BID_NOTICE, [
            'parent_procurement_id' => $parentId,
            'title' => $data['procurement_title'],
            'description' => $data['description'],
            'file_path' => $filePath,
            'file_hash' => $fileHash,
            'posted_at' => $data['posting_date'],
            'created_by' => $user['id'],
            'updated_by' => $user['id'],
        ]);

        $parent = $parentModel->findById($parentId) ?? ['id' => $parentId];
        $activityLog->create([
            'parent_procurement_id' => $parentId,
            'user_id' => $user['id'],
            'action_type' => 'create_parent',
            'document_type' => ProcurementDocument::TYPE_BID_NOTICE,
            'document_id' => $documentId,
            'before_snapshot' => null,
            'after_snapshot' => json_encode($parent, JSON_UNESCAPED_SLASHES),
            'reason' => 'Official public procurement posting created.',
            'file_hash' => $fileHash,
            'approval_reference' => null,
        ]);

        return $parentId;
    }

    public function createDocument(string $type, int $parentId, array $data, array $user, string $filePath): array
    {
        $workflow = $this->findParentWithWorkflow($parentId);
        if (!$workflow) {
            return ['allowed' => false, 'errors' => ['Selected procurement posting was not found.']];
        }

        $parent = $workflow['parent'];
        $isAdmin = ($user['role'] ?? '') === 'admin';
        $sameBranch = trim((string) ($parent['branch'] ?? '')) !== '' && trim((string) ($parent['branch'] ?? '')) === trim((string) ($user['branch'] ?? ''));
        $isOwner = (int) ($parent['created_by'] ?? 0) === (int) ($user['id'] ?? 0);
        if (!$isAdmin && !$sameBranch && !$isOwner) {
            return ['allowed' => false, 'errors' => ['You may only post documents to procurement records assigned to your branch.']];
        }

        $guard = $this->canCreateDocument($type, $parent, $workflow['documents'], $data['posted_at']);
        if (!$guard['allowed']) {
            return $guard;
        }

        $uploads = $this->uploads ?? new FileUploadService();
        $fileHash = $uploads->hash($filePath);

        $documentId = ($this->documents ?? new ProcurementDocument())->create($type, [
            'parent_procurement_id' => $parentId,
            'title' => $data['title'],
            'description' => $data['description'],
            'file_path' => $filePath,
            'file_hash' => $fileHash,
            'posted_at' => $data['posted_at'],
            'created_by' => $user['id'],
            'updated_by' => $user['id'],
        ]);

        $document = ($this->documents ?? new ProcurementDocument())->findById($type, $documentId) ?? ['id' => $documentId];
        ($this->activityLogs ?? new ProcurementActivityLog())->create([
            'parent_procurement_id' => $parentId,
            'user_id' => $user['id'],
            'action_type' => 'create_document',
            'document_type' => $type,
            'document_id' => $documentId,
            'before_snapshot' => null,
            'after_snapshot' => json_encode($document, JSON_UNESCAPED_SLASHES),
            'reason' => 'Official signed procurement document posted.',
            'file_hash' => $fileHash,
            'approval_reference' => null,
        ]);

        $refreshed = $this->findParentWithWorkflow($parentId);
        if ($refreshed) {
            $this->persistParentState($refreshed['parent'], $refreshed['documents']);
        }

        return ['allowed' => true, 'errors' => [], 'document_id' => $documentId];
    }

    public function canCreateDocument(string $type, array $parent, ?array $documents = null, ?string $postedAt = null): array
    {
        $documents = $documents ?? $this->documentsForParent((int) $parent['id']);
        $errors = [];
        $status = $this->determinePostingStatus($parent);

        if (($parent['posting_status'] ?? null) === self::POSTING_STATUS_ARCHIVED || !empty($parent['archived_at'])) {
            $errors[] = 'Archived procurement records are immutable and cannot accept new postings.';
        }

        if ($type !== ProcurementDocument::TYPE_BID_NOTICE && empty($documents[ProcurementDocument::TYPE_BID_NOTICE])) {
            $errors[] = 'Bid Notice / Invitation to Bid must be posted first.';
        }

        if ($type === ProcurementDocument::TYPE_BID_NOTICE && !empty($documents[ProcurementDocument::TYPE_BID_NOTICE])) {
            $errors[] = 'Bid Notice / Invitation to Bid already exists for this procurement.';
        }

        if ($type === ProcurementDocument::TYPE_SBB) {
            if ($status !== self::POSTING_STATUS_OPEN) {
                $errors[] = 'Supplemental/Bid Bulletin may only be posted while bidding is open.';
            }
            if ($postedAt !== null) {
                if (!$this->isOnOrAfter($postedAt, (string) $parent['posting_date'])) {
                    $errors[] = 'Supplemental/Bid Bulletin date must be on or after the Bid Notice posting date.';
                }
                if (!$this->isOnOrBefore($postedAt, (string) $parent['bid_submission_deadline'])) {
                    $errors[] = 'Supplemental/Bid Bulletin date must be on or before the bid submission deadline.';
                }
            }
        }

        if ($type === ProcurementDocument::TYPE_RESOLUTION) {
            if ($status !== self::POSTING_STATUS_CLOSED) {
                $errors[] = 'Resolution may only be posted after bidding has closed.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_RESOLUTION])) {
                $errors[] = 'Resolution has already been posted for this procurement.';
            }
            if ($postedAt !== null && !$this->isAfter($postedAt, (string) $parent['bid_submission_deadline'])) {
                $errors[] = 'Resolution date must be later than the bid submission deadline.';
            }
        }

        if ($type === ProcurementDocument::TYPE_AWARD) {
            if ($status !== self::POSTING_STATUS_CLOSED) {
                $errors[] = 'Notice of Award / Award may only be posted after bidding has closed.';
            }
            if (empty($documents[ProcurementDocument::TYPE_RESOLUTION])) {
                $errors[] = 'Resolution must be posted before Notice of Award / Award.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_AWARD])) {
                $errors[] = 'Notice of Award / Award has already been posted for this procurement.';
            }
            $resolutionPostedAt = $this->latestPostedAt($documents, ProcurementDocument::TYPE_RESOLUTION);
            if ($postedAt !== null && $resolutionPostedAt !== null && !$this->isOnOrAfter($postedAt, $resolutionPostedAt)) {
                $errors[] = 'Award date must be on or after the Resolution date.';
            }
        }

        if ($type === ProcurementDocument::TYPE_CONTRACT) {
            if ($status !== self::POSTING_STATUS_CLOSED) {
                $errors[] = 'Contract may only be posted after bidding has closed.';
            }
            if (empty($documents[ProcurementDocument::TYPE_AWARD])) {
                $errors[] = 'Notice of Award / Award must be posted before Contract.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_CONTRACT])) {
                $errors[] = 'Contract has already been posted for this procurement.';
            }
            $awardPostedAt = $this->latestPostedAt($documents, ProcurementDocument::TYPE_AWARD);
            if ($postedAt !== null && $awardPostedAt !== null && !$this->isOnOrAfter($postedAt, $awardPostedAt)) {
                $errors[] = 'Contract date must be on or after the Award date.';
            }
        }

        if ($type === ProcurementDocument::TYPE_NOTICE_TO_PROCEED) {
            if ($status !== self::POSTING_STATUS_CLOSED) {
                $errors[] = 'Notice to Proceed may only be posted after bidding has closed.';
            }
            if (empty($documents[ProcurementDocument::TYPE_CONTRACT])) {
                $errors[] = 'Contract must be posted before Notice to Proceed.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_NOTICE_TO_PROCEED])) {
                $errors[] = 'Notice to Proceed has already been posted for this procurement.';
            }
            $contractPostedAt = $this->latestPostedAt($documents, ProcurementDocument::TYPE_CONTRACT);
            if ($postedAt !== null && $contractPostedAt !== null && !$this->isOnOrAfter($postedAt, $contractPostedAt)) {
                $errors[] = 'Notice to Proceed date must be on or after the Contract date.';
            }
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
            'helper_text' => $errors[0] ?? 'Ready to post.',
        ];
    }

    public function publicList(?string $search = null, ?string $region = null, ?string $mode = null): array
    {
        $parents = ($this->parents ?? new ParentProcurement())->findPublic($search, $region, $mode);

        return array_map(fn (array $parent): array => $this->refreshParentState($parent), $parents);
    }

    public function documentsForParent(int $parentId): array
    {
        $documentModel = $this->documents ?? new ProcurementDocument();
        $documents = [];
        foreach (ProcurementDocument::types() as $type) {
            $documents[$type] = $documentModel->findForParent($type, $parentId);
        }

        return $documents;
    }

    public function refreshParentState(array $parent): array
    {
        $documents = $this->documentsForParent((int) $parent['id']);
        $this->persistParentState($parent, $documents);

        $parent['current_stage'] = $this->currentStage($documents);
        $parent['posting_status'] = $this->determinePostingStatus($parent);

        return $parent;
    }

    public function isPubliclyVisible(array $parent): bool
    {
        return true;
    }

    public function canArchive(array $parent, array $documents, array $user, array $input): array
    {
        $errors = [];
        $status = $this->determinePostingStatus($parent);
        $reason = trim((string) ($input['archive_reason'] ?? ''));
        $approvalReference = trim((string) ($input['archive_approval_reference'] ?? ''));

        if (($user['role'] ?? '') !== 'admin') {
            $errors[] = 'Only administrators may archive procurement records.';
        }

        if (!empty($parent['archived_at'])) {
            $errors[] = 'This procurement record is already archived.';
        }

        if ($status !== self::POSTING_STATUS_CLOSED) {
            $errors[] = 'Archive is allowed only after the procurement is closed.';
        }

        if (empty($documents[ProcurementDocument::TYPE_NOTICE_TO_PROCEED])) {
            $errors[] = 'Archive is allowed only after Notice to Proceed has been posted.';
        }

        if ($reason === '') {
            $errors[] = 'Archive reason is required.';
        }

        if ($approvalReference === '') {
            $errors[] = 'Archive approval reference is required.';
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
        ];
    }

    public function archiveParent(int $parentId, array $user, array $input): array
    {
        $workflow = $this->findParentWithWorkflow($parentId);
        if (!$workflow) {
            return ['allowed' => false, 'errors' => ['Procurement posting not found.']];
        }

        $guard = $this->canArchive($workflow['parent'], $workflow['documents'], $user, $input);
        if (!$guard['allowed']) {
            return $guard;
        }

        $archivedAt = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $reason = trim((string) ($input['archive_reason'] ?? ''));
        $approvalReference = trim((string) ($input['archive_approval_reference'] ?? ''));
        ($this->parents ?? new ParentProcurement())->updateArchiveState(
            $parentId,
            $archivedAt,
            self::POSTING_STATUS_ARCHIVED,
            $reason,
            $approvalReference,
            (int) ($user['id'] ?? 0),
            (int) ($user['id'] ?? 0)
        );

        ($this->activityLogs ?? new ProcurementActivityLog())->create([
            'parent_procurement_id' => $parentId,
            'user_id' => (int) ($user['id'] ?? 0),
            'action_type' => 'archive',
            'document_type' => (string) ($workflow['parent']['current_stage'] ?? ProcurementDocument::TYPE_NOTICE_TO_PROCEED),
            'document_id' => null,
            'before_snapshot' => json_encode($workflow['parent'], JSON_UNESCAPED_SLASHES),
            'after_snapshot' => json_encode(array_merge($workflow['parent'], [
                'posting_status' => self::POSTING_STATUS_ARCHIVED,
                'archived_at' => $archivedAt,
                'archive_reason' => $reason,
                'archive_approval_reference' => $approvalReference,
            ]), JSON_UNESCAPED_SLASHES),
            'reason' => $reason,
            'file_hash' => null,
            'approval_reference' => $approvalReference,
        ]);

        return ['allowed' => true, 'errors' => []];
    }

    public function unarchiveParent(int $parentId, array $user): array
    {
        return [
            'allowed' => false,
            'errors' => ['Archived procurement records are immutable and cannot be restored.'],
        ];
    }

    private function persistParentState(array $parent, array $documents): void
    {
        if (!empty($parent['archived_at'])) {
            return;
        }

        $currentStage = $this->currentStage($documents);
        $postingStatus = $this->determinePostingStatus($parent);

        if (
            ($parent['current_stage'] ?? null) !== $currentStage
            || ($parent['posting_status'] ?? null) !== $postingStatus
        ) {
            ($this->parents ?? new ParentProcurement())->updateWorkflowAndPostingState((int) $parent['id'], $currentStage, $postingStatus);
        }
    }

    private function latestPostedAt(array $documents, string $type): ?string
    {
        if (empty($documents[$type])) {
            return null;
        }

        $last = $documents[$type][count($documents[$type]) - 1];

        return (string) ($last['posted_at'] ?? null);
    }

    private function isAfter(string $left, string $right): bool
    {
        return new DateTimeImmutable($left) > new DateTimeImmutable($right);
    }

    private function isOnOrAfter(string $left, string $right): bool
    {
        return new DateTimeImmutable($left) >= new DateTimeImmutable($right);
    }

    private function isOnOrBefore(string $left, string $right): bool
    {
        return new DateTimeImmutable($left) <= new DateTimeImmutable($right);
    }
}
