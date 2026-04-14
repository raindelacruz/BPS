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
    public const COMPETITIVE_BIDDING_MODE = CompetitiveBiddingService::MODE;
    public const SVP_MODE = SmallValueProcurementService::MODE;
    public const POSTING_STATUS_SCHEDULED = 'scheduled';
    public const POSTING_STATUS_OPEN = 'open';
    public const POSTING_STATUS_CLOSED = 'closed';
    public const POSTING_STATUS_ARCHIVED = 'archived';

    public function __construct(
        private readonly ?ParentProcurement $parents = null,
        private readonly ?ProcurementDocument $documents = null,
        private readonly ?ProcurementActivityLog $activityLogs = null,
        private readonly ?FileUploadService $uploads = null,
        private readonly ?CompetitiveBiddingService $competitive = null,
        private readonly ?SmallValueProcurementService $svp = null
    ) {
    }

    public function procurementTypes(): array
    {
        return ProcurementTypeHelper::all();
    }

    public function documentTypes(): array
    {
        return $this->competitive()->allowedDocumentTypes();
    }

    public function competitiveDocumentTypes(): array
    {
        return $this->competitive()->allowedDocumentTypes();
    }

    public function svpDocumentTypes(): array
    {
        return $this->svp()->allowedDocumentTypes();
    }

    public function relatedDocumentTypes(): array
    {
        return array_values(array_unique(array_merge(
            $this->competitiveDocumentTypes(),
            $this->svpDocumentTypes()
        )));
    }

    public function competitiveStageLabels(): array
    {
        return $this->competitive()->stageLabels();
    }

    public function competitiveStatusLabels(): array
    {
        return $this->competitive()->statusLabels();
    }

    public function svpStageLabels(): array
    {
        return $this->svp()->stageLabels();
    }

    public function svpStatusLabels(): array
    {
        return $this->svp()->statusLabels();
    }

    public function validateParentInput(array $input): array
    {
        $mode = $this->normalizeMode((string) ($input['procurement_mode'] ?? $input['mode_of_procurement'] ?? ''));
        if ($mode === self::COMPETITIVE_BIDDING_MODE) {
            return $this->validateCompetitiveBiddingInput($input);
        }
        if ($mode === self::SVP_MODE) {
            return $this->validateSvpInput($input);
        }

        $validation = ['data' => [], 'errors' => []];
        ValidationHelper::addError($validation['errors'], 'procurement_mode', 'Select a valid procurement mode.');

        return $validation;
    }

    public function validateCompetitiveBiddingInput(array $input): array
    {
        $validation = $this->competitive()->validateParentInput($input);
        $validation['data']['mode_of_procurement'] = self::COMPETITIVE_BIDDING_MODE;

        return $this->validateReferenceUniqueness($validation);
    }

    public function validateSvpInput(array $input): array
    {
        $validation = $this->svp()->validateParentInput($input);
        $validation['data']['mode_of_procurement'] = self::SVP_MODE;

        return $this->validateReferenceUniqueness($validation);
    }

    public function validateDocumentInput(array $input): array
    {
        return $this->validatePostedDocumentInput($input, $this->relatedDocumentTypes());
    }

    public function validateSvpDocumentInput(array $input, string $type): array
    {
        return $this->validatePostedDocumentInput(array_merge($input, ['type' => $type]), $this->svpDocumentTypes());
    }

    public function determinePostingStatus(array $parent, ?DateTimeImmutable $now = null): string
    {
        $mode = $this->modeOf($parent);
        if ($mode === self::SVP_MODE) {
            return $this->svp()->determinePostingStatus($parent, $this->documentsForParent((int) ($parent['id'] ?? 0), $this->svpDocumentTypes()), $now);
        }

        return $this->competitive()->determinePostingStatus($parent, $now);
    }

    public function currentStage(array $documentsByType, ?string $mode = null): string
    {
        $mode = $this->normalizeMode((string) $mode);

        return $mode === self::SVP_MODE
            ? $this->svp()->currentStage($documentsByType)
            : $this->competitive()->currentStage($documentsByType);
    }

    public function listForUser(array $user): array
    {
        $parents = ($user['role'] ?? '') === 'admin'
            ? ($this->parents ?? new ParentProcurement())->findAll()
            : ($this->parents ?? new ParentProcurement())->findByCreator((int) ($user['id'] ?? 0));

        return array_map(fn (array $parent): array => $this->refreshParentState($parent), $parents);
    }

    public function publicList(?string $search = null, ?string $region = null, ?string $mode = null): array
    {
        $mode = $this->normalizeMode((string) $mode);
        $records = ($this->parents ?? new ParentProcurement())->findPublic($search, $region, $mode !== '' ? $mode : null);
        $records = array_map(fn (array $parent): array => $this->refreshParentState($parent), $records);

        return array_values(array_filter($records, fn (array $parent): bool => $this->isPubliclyVisible($parent)));
    }

    public function eligibleParents(string $type, array $user): array
    {
        $type = trim($type);
        if (!in_array($type, $this->relatedDocumentTypes(), true)) {
            return [];
        }

        $parents = ($user['role'] ?? '') === 'admin'
            ? ($this->parents ?? new ParentProcurement())->findAll()
            : (($user['branch'] ?? '') !== '' ? ($this->parents ?? new ParentProcurement())->findByBranch((string) $user['branch']) : []);

        $eligible = [];
        foreach ($parents as $parent) {
            $parent = $this->refreshParentState($parent);
            $guard = $this->modeOf($parent) === self::SVP_MODE
                ? $this->canCreateSvpDocument($type, $parent)
                : $this->canCreateDocument($type, $parent);
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
        $mode = $this->modeOf($parent);
        $types = $mode === self::SVP_MODE ? $this->svpDocumentTypes() : $this->competitiveDocumentTypes();
        $documents = $this->documentsForParent($parentId, $types);
        $actions = [];
        foreach ($types as $type) {
            $actions[$type] = $mode === self::SVP_MODE
                ? $this->canCreateSvpDocument($type, $parent, $documents)
                : $this->canCreateDocument($type, $parent, $documents);
        }

        return [
            'parent' => $parent,
            'documents' => $documents,
            'timeline' => $this->timelineForParent($documents, $types),
            'actions' => $actions,
            'activityLogs' => ($this->activityLogs ?? new ProcurementActivityLog())->findByParent($parentId),
        ];
    }

    public function createParent(array $data, array $user, string $filePath): int
    {
        return $this->modeOf($data) === self::SVP_MODE
            ? $this->createSvpParent($data, $user)
            : $this->createCompetitiveBiddingParent($data, $user, $filePath);
    }

    public function createDocument(string $type, int $parentId, array $data, array $user, string $filePath): array
    {
        $workflow = $this->findParentWithWorkflow($parentId);
        if (!$workflow) {
            return ['allowed' => false, 'errors' => ['Selected procurement posting was not found.']];
        }
        if ($this->modeOf($workflow['parent']) !== self::COMPETITIVE_BIDDING_MODE) {
            return ['allowed' => false, 'errors' => ['Competitive Bidding document actions are not available for this procurement.']];
        }
        if (!$this->userCanPostToParent($workflow['parent'], $user)) {
            return ['allowed' => false, 'errors' => ['You may only post documents to procurement records assigned to your branch.']];
        }

        $guard = $this->canCreateDocument($type, $workflow['parent'], $workflow['documents'], (string) ($data['posted_at'] ?? ''));
        if (!$guard['allowed']) {
            return $guard;
        }

        $documentId = $this->storeDocument($type, $parentId, $data, $user, $filePath, 'create_document', 'Competitive Bidding document posted.');
        $refreshed = $this->findParentWithWorkflow($parentId);
        if ($refreshed) {
            $this->persistParentState($refreshed['parent'], $refreshed['documents']);
        }

        return ['allowed' => true, 'errors' => [], 'document_id' => $documentId];
    }

    public function createSvpDocument(string $type, int $parentId, array $data, array $user, string $filePath): array
    {
        $workflow = $this->findParentWithWorkflow($parentId);
        if (!$workflow) {
            return ['allowed' => false, 'errors' => ['Selected SVP procurement was not found.']];
        }
        if ($this->modeOf($workflow['parent']) !== self::SVP_MODE) {
            return ['allowed' => false, 'errors' => ['This procurement does not use the SVP workflow.']];
        }
        if (!$this->userCanPostToParent($workflow['parent'], $user)) {
            return ['allowed' => false, 'errors' => ['You may only post documents to procurement records assigned to your branch.']];
        }

        $guard = $this->canCreateSvpDocument($type, $workflow['parent'], $workflow['documents'], (string) ($data['posted_at'] ?? ''));
        if (!$guard['allowed']) {
            return $guard;
        }

        $documentId = $this->storeDocument($type, $parentId, $data, $user, $filePath, 'create_svp_document', 'SVP document posted.');
        $refreshed = $this->findParentWithWorkflow($parentId);
        if ($refreshed) {
            $this->persistParentState($refreshed['parent'], $refreshed['documents']);
        }

        return ['allowed' => true, 'errors' => [], 'document_id' => $documentId];
    }

    public function createRelatedDocument(string $type, int $parentId, array $data, array $user, string $filePath): array
    {
        $workflow = $this->findParentWithWorkflow($parentId);
        if (!$workflow) {
            return ['allowed' => false, 'errors' => ['Selected procurement posting was not found.']];
        }

        return $this->modeOf($workflow['parent']) === self::SVP_MODE
            ? $this->createSvpDocument($type, $parentId, $data, $user, $filePath)
            : $this->createDocument($type, $parentId, $data, $user, $filePath);
    }

    public function canCreateDocument(string $type, array $parent, ?array $documents = null, ?string $postedAt = null): array
    {
        if ($this->modeOf($parent) !== self::COMPETITIVE_BIDDING_MODE) {
            return ['allowed' => false, 'errors' => ['Competitive Bidding actions are not used for this procurement.'], 'helper_text' => 'Use the SVP workflow page instead.'];
        }

        $documents ??= $this->documentsForParent((int) $parent['id'], $this->competitiveDocumentTypes());

        return $this->competitive()->canCreateDocument($type, $parent, $documents, $postedAt);
    }

    public function canCreateSvpDocument(string $type, array $parent, ?array $documents = null, ?string $postedAt = null): array
    {
        if ($this->modeOf($parent) !== self::SVP_MODE) {
            return ['allowed' => false, 'errors' => ['SVP actions are not used for this procurement.'], 'helper_text' => 'Use the Competitive Bidding workflow page instead.'];
        }

        $documents ??= $this->documentsForParent((int) $parent['id'], $this->svpDocumentTypes());

        return $this->svp()->canCreateDocument($type, $parent, $documents, $postedAt);
    }

    public function refreshParentState(array $parent): array
    {
        $documents = $this->documentsForParent(
            (int) ($parent['id'] ?? 0),
            $this->modeOf($parent) === self::SVP_MODE ? $this->svpDocumentTypes() : $this->competitiveDocumentTypes()
        );

        $this->persistParentState($parent, $documents);
        $parent['current_stage'] = $this->currentStage($documents, $this->modeOf($parent));
        $parent['posting_status'] = $this->determinePostingStatus($parent);
        $parent['procurement_mode'] = $this->modeOf($parent);
        $parent['mode_of_procurement'] = $parent['procurement_mode'];

        return $parent;
    }

    public function isPubliclyVisible(array $parent): bool
    {
        return !empty($parent['posting_status']) && (string) ($parent['posting_status'] ?? '') !== self::POSTING_STATUS_SCHEDULED;
    }

    public function canArchive(array $parent, array $documents, array $user, array $input): array
    {
        $errors = [];
        $reason = trim((string) ($input['archive_reason'] ?? ''));
        $approvalReference = trim((string) ($input['archive_approval_reference'] ?? ''));

        if (($user['role'] ?? '') !== 'admin') {
            $errors[] = 'Only administrators may archive procurement records.';
        }
        if (!empty($parent['archived_at'])) {
            $errors[] = 'This procurement record is already archived.';
        }
        if ($reason === '') {
            $errors[] = 'Archive reason is required.';
        }
        if ($approvalReference === '') {
            $errors[] = 'Archive approval reference is required.';
        }
        if ($this->modeOf($parent) === self::COMPETITIVE_BIDDING_MODE && empty($documents[ProcurementDocument::TYPE_NOTICE_TO_PROCEED])) {
            $errors[] = 'Competitive Bidding records may be archived only after Notice to Proceed has been posted.';
        }
        if ($this->modeOf($parent) === self::SVP_MODE && empty($documents[ProcurementDocument::TYPE_AWARD])) {
            $errors[] = 'SVP records may be archived only after Award has been posted.';
        }

        return ['allowed' => $errors === [], 'errors' => $errors];
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
            'document_type' => (string) ($workflow['parent']['current_stage'] ?? self::POSTING_STATUS_ARCHIVED),
            'document_id' => null,
            'before_snapshot' => json_encode($workflow['parent'], JSON_UNESCAPED_SLASHES),
            'after_snapshot' => json_encode(array_merge($workflow['parent'], ['posting_status' => self::POSTING_STATUS_ARCHIVED, 'archived_at' => $archivedAt]), JSON_UNESCAPED_SLASHES),
            'reason' => $reason,
            'file_hash' => null,
            'approval_reference' => $approvalReference,
        ]);

        return ['allowed' => true, 'errors' => []];
    }

    public function unarchiveParent(int $parentId, array $user): array
    {
        return ['allowed' => false, 'errors' => ['Archived procurement records are immutable and cannot be restored.']];
    }

    private function validateReferenceUniqueness(array $validation): array
    {
        $reference = trim((string) ($validation['data']['reference_number'] ?? ''));
        if ($reference !== '' && ($this->parents ?? new ParentProcurement())->referenceNumberExists($reference)) {
            ValidationHelper::addError($validation['errors'], 'reference_number', 'Reference number is already used by another procurement posting.');
        }

        return $validation;
    }

    private function validatePostedDocumentInput(array $input, array $allowedTypes): array
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
        if ($data['type'] !== '' && !in_array($data['type'], $allowedTypes, true)) {
            ValidationHelper::addError($errors, 'type', 'Document type is invalid.');
        }
        if ($data['posted_at'] !== '') {
            try {
                $data['posted_at'] = (new DateTimeImmutable($data['posted_at']))->format('Y-m-d H:i:s');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'posted_at', 'Posted at value is invalid.');
            }
        }

        return ['data' => $data, 'errors' => $errors];
    }

    private function documentsForParent(int $parentId, array $types): array
    {
        $documentModel = $this->documents ?? new ProcurementDocument();
        $documents = [];
        foreach ($types as $type) {
            $documents[$type] = $documentModel->findForParent($type, $parentId);
        }

        return $documents;
    }

    private function persistParentState(array $parent, array $documents): void
    {
        if (!empty($parent['archived_at'])) {
            return;
        }

        $mode = $this->modeOf($parent);
        $stage = $this->currentStage($documents, $mode);
        $status = $mode === self::SVP_MODE
            ? $this->svp()->determinePostingStatus($parent, $documents)
            : $this->competitive()->determinePostingStatus($parent);

        if (($parent['current_stage'] ?? null) !== $stage || ($parent['posting_status'] ?? null) !== $status) {
            ($this->parents ?? new ParentProcurement())->updateWorkflowAndPostingState((int) $parent['id'], $stage, $status);
        }
    }

    private function createCompetitiveBiddingParent(array $data, array $user, string $filePath): int
    {
        $parentModel = $this->parents ?? new ParentProcurement();
        $documentModel = $this->documents ?? new ProcurementDocument();
        $activityLog = $this->activityLogs ?? new ProcurementActivityLog();
        $uploads = $this->uploads ?? new FileUploadService();
        $status = $this->competitive()->determinePostingStatus($data);
        $fileHash = (string) $uploads->hash($filePath);
        $parentId = $parentModel->create([
            'procurement_mode' => self::COMPETITIVE_BIDDING_MODE,
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'posting_status' => $status,
            'current_stage' => ProcurementDocument::TYPE_BID_NOTICE,
            'category' => $data['category'],
            'end_user_unit' => $data['end_user_unit'],
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
            'reason' => 'Competitive Bidding procurement record created.',
            'file_hash' => $fileHash,
            'approval_reference' => null,
        ]);

        return $parentId;
    }

    private function createSvpParent(array $data, array $user): int
    {
        $parentModel = $this->parents ?? new ParentProcurement();
        $activityLog = $this->activityLogs ?? new ProcurementActivityLog();
        $parentId = $parentModel->create([
            'procurement_mode' => self::SVP_MODE,
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'posting_date' => null,
            'bid_submission_deadline' => null,
            'description' => $data['description'],
            'posting_status' => self::POSTING_STATUS_SCHEDULED,
            'current_stage' => ProcurementDocument::TYPE_RFQ,
            'category' => $data['category'],
            'end_user_unit' => $data['end_user_unit'],
            'region' => $user['region'],
            'branch' => $user['branch'] ?? null,
            'created_by' => $user['id'],
            'updated_by' => $user['id'],
        ]);

        $parent = $parentModel->findById($parentId) ?? ['id' => $parentId];
        $activityLog->create([
            'parent_procurement_id' => $parentId,
            'user_id' => $user['id'],
            'action_type' => 'create_parent',
            'document_type' => 'svp_procurement',
            'document_id' => null,
            'before_snapshot' => null,
            'after_snapshot' => json_encode($parent, JSON_UNESCAPED_SLASHES),
            'reason' => 'Small Value Procurement record created.',
            'file_hash' => null,
            'approval_reference' => null,
        ]);

        return $parentId;
    }

    private function timelineForParent(array $documents, array $types): array
    {
        $timeline = [];
        foreach ($types as $type) {
            foreach ($documents[$type] ?? [] as $document) {
                $document['document_type'] = $type;
                $document['document_label'] = ProcurementDocument::label($type);
                $timeline[] = $document;
            }
        }

        usort($timeline, static function (array $left, array $right): int {
            $stageDiff = ((int) ($left['sequence_stage'] ?? 0)) <=> ((int) ($right['sequence_stage'] ?? 0));
            if ($stageDiff !== 0) {
                return $stageDiff;
            }

            return strcmp((string) ($left['posted_at'] ?? ''), (string) ($right['posted_at'] ?? ''));
        });

        return $timeline;
    }

    private function userCanPostToParent(array $parent, array $user): bool
    {
        $isAdmin = ($user['role'] ?? '') === 'admin';
        $sameBranch = trim((string) ($parent['branch'] ?? '')) !== '' && trim((string) ($parent['branch'] ?? '')) === trim((string) ($user['branch'] ?? ''));
        $isOwner = (int) ($parent['created_by'] ?? 0) === (int) ($user['id'] ?? 0);

        return $isAdmin || $sameBranch || $isOwner;
    }

    private function storeDocument(string $type, int $parentId, array $data, array $user, string $filePath, string $actionType, string $reason): int
    {
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
            'action_type' => $actionType,
            'document_type' => $type,
            'document_id' => $documentId,
            'before_snapshot' => null,
            'after_snapshot' => json_encode($document, JSON_UNESCAPED_SLASHES),
            'reason' => $reason,
            'file_hash' => $fileHash,
            'approval_reference' => null,
        ]);

        return $documentId;
    }

    private function modeOf(array $record): string
    {
        return $this->normalizeMode((string) ($record['procurement_mode'] ?? $record['mode_of_procurement'] ?? ''));
    }

    private function normalizeMode(string $mode): string
    {
        $mode = trim($mode);

        return match ($mode) {
            'competitive_bidding' => self::COMPETITIVE_BIDDING_MODE,
            'small_value_procurement', 'svp' => self::SVP_MODE,
            default => $mode,
        };
    }

    private function competitive(): CompetitiveBiddingService
    {
        return $this->competitive ?? new CompetitiveBiddingService();
    }

    private function svp(): SmallValueProcurementService
    {
        return $this->svp ?? new SmallValueProcurementService();
    }
}
