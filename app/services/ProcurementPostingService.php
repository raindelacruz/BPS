<?php

namespace App\Services;

use App\Helpers\LogHelper;
use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ValidationHelper;
use App\Models\ParentProcurement;
use App\Models\ProcurementActivityLog;
use App\Models\ProcurementDocument;
use DateTimeImmutable;

class ProcurementPostingService extends BaseService
{
    public function __construct(
        private readonly ?ParentProcurement $parents = null,
        private readonly ?ProcurementDocument $documents = null,
        private readonly ?ProcurementActivityLog $activityLogs = null
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
            'parent_procurement_id' => (int) ($input['parent_procurement_id'] ?? $input['selected_bid_id'] ?? $input['bid_id'] ?? 0),
            'title' => trim((string) ($input['title'] ?? '')),
            'posted_at' => trim((string) ($input['posted_at'] ?? $input['start_date'] ?? '')),
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

    public function determineParentStatus(array $parent): string
    {
        if ((int) ($parent['is_archived'] ?? 0) === 1) {
            return 'archived';
        }

        $now = new DateTimeImmutable();
        $postingDate = new DateTimeImmutable((string) $parent['posting_date']);
        $deadline = new DateTimeImmutable((string) $parent['bid_submission_deadline']);
        $currentStage = (string) ($parent['current_stage'] ?? 'draft');

        if ($postingDate > $now) {
            return 'pending';
        }

        if ($deadline < $now && in_array($currentStage, ['draft', 'bid_notice'], true)) {
            return 'expired';
        }

        return 'active';
    }

    public function currentStage(array $parent, array $documentsByType): string
    {
        foreach ([
            ProcurementDocument::TYPE_NOTICE_TO_PROCEED,
            ProcurementDocument::TYPE_CONTRACT,
            ProcurementDocument::TYPE_AWARD,
            ProcurementDocument::TYPE_RESOLUTION,
            ProcurementDocument::TYPE_BID_NOTICE,
        ] as $type) {
            if (!empty($documentsByType[$type])) {
                return $type === ProcurementDocument::TYPE_BID_NOTICE ? 'bid_notice' : $type;
            }
        }

        return 'draft';
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
        $status = $this->determineParentStatus([
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'current_stage' => 'bid_notice',
            'is_archived' => 0,
        ]);

        $parentId = $parentModel->create([
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'mode_of_procurement' => $data['mode_of_procurement'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'status' => $status,
            'current_stage' => 'bid_notice',
            'is_archived' => 0,
            'archived_at' => null,
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
            'posted_at' => $data['posting_date'],
            'created_by' => $user['id'],
            'updated_by' => $user['id'],
        ]);

        $activityLog->create([
            'parent_procurement_id' => $parentId,
            'document_type' => ProcurementDocument::TYPE_BID_NOTICE,
            'document_id' => $documentId,
            'sequence_stage' => ProcurementDocument::stageNumber(ProcurementDocument::TYPE_BID_NOTICE),
            'action_type' => 'create',
            'acted_by' => $user['id'],
            'action_note' => 'Created the root bid notice posting record.',
        ]);

        return $parentId;
    }

    public function updateParent(int $parentId, array $data, array $user, ?string $filePath = null): array
    {
        $workflow = $this->findParentWithWorkflow($parentId);
        if (!$workflow) {
            return ['allowed' => false, 'errors' => ['Procurement record not found.']];
        }

        $parent = $workflow['parent'];
        $bidNotice = $workflow['documents'][ProcurementDocument::TYPE_BID_NOTICE][0] ?? null;
        if (!$bidNotice) {
            return ['allowed' => false, 'errors' => ['Bid notice record is missing.']];
        }

        $guard = $this->canEditDocument(ProcurementDocument::TYPE_BID_NOTICE, $bidNotice, $parent, $workflow['documents'], $user);
        if (!$guard['allowed']) {
            return $guard;
        }

        ($this->parents ?? new ParentProcurement())->updateById($parentId, [
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'mode_of_procurement' => $data['mode_of_procurement'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'status' => $this->determineParentStatus(array_merge($parent, [
                'posting_date' => $data['posting_date'],
                'bid_submission_deadline' => $data['bid_submission_deadline'],
            ])),
            'current_stage' => $parent['current_stage'],
            'is_archived' => $parent['is_archived'],
            'archived_at' => $parent['archived_at'] ?? null,
            'region' => $parent['region'],
            'branch' => $parent['branch'] ?? null,
            'updated_by' => $user['id'],
        ]);

        ($this->documents ?? new ProcurementDocument())->updateById(ProcurementDocument::TYPE_BID_NOTICE, (int) $bidNotice['id'], [
            'title' => $data['procurement_title'],
            'description' => $data['description'],
            'file_path' => $filePath ?? $bidNotice['file_path'],
            'posted_at' => $data['posting_date'],
            'updated_by' => $user['id'],
        ]);

        ($this->activityLogs ?? new ProcurementActivityLog())->create([
            'parent_procurement_id' => $parentId,
            'document_type' => ProcurementDocument::TYPE_BID_NOTICE,
            'document_id' => $bidNotice['id'],
            'sequence_stage' => ProcurementDocument::stageNumber(ProcurementDocument::TYPE_BID_NOTICE),
            'action_type' => 'edit',
            'acted_by' => $user['id'],
            'action_note' => 'Edited the bid notice record.',
        ]);

        return ['allowed' => true, 'errors' => []];
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

        // This module is strictly for posting already approved offline documents.
        // We only enforce posting order and posting-time eligibility here, not approvals or signatures.
        $guard = $this->canCreateDocument($type, $parent, $workflow['documents']);
        if (!$guard['allowed']) {
            return $guard;
        }

        $documentId = ($this->documents ?? new ProcurementDocument())->create($type, [
            'parent_procurement_id' => $parentId,
            'title' => $data['title'],
            'description' => $data['description'],
            'file_path' => $filePath,
            'posted_at' => $data['posted_at'],
            'created_by' => $user['id'],
            'updated_by' => $user['id'],
        ]);

        ($this->activityLogs ?? new ProcurementActivityLog())->create([
            'parent_procurement_id' => $parentId,
            'document_type' => $type,
            'document_id' => $documentId,
            'sequence_stage' => ProcurementDocument::stageNumber($type),
            'action_type' => 'create',
            'acted_by' => $user['id'],
            'action_note' => 'Posted ' . ProcurementDocument::label($type) . '.',
        ]);

        // Sequence locks stay simple and explicit: once the next stage exists, the previous stage becomes read-only
        // unless an admin later reopens it. SBB is the only optional repeatable stage and locks when resolution is posted.
        if ($type === ProcurementDocument::TYPE_RESOLUTION) {
            $this->lockStage($parentId, ProcurementDocument::TYPE_BID_NOTICE, $user['id'], 'Locked after resolution was posted.');
            $this->lockStage($parentId, ProcurementDocument::TYPE_SBB, $user['id'], 'Locked after resolution was posted.');
        } elseif ($type === ProcurementDocument::TYPE_AWARD) {
            $this->lockStage($parentId, ProcurementDocument::TYPE_RESOLUTION, $user['id'], 'Locked after award was posted.');
        } elseif ($type === ProcurementDocument::TYPE_CONTRACT) {
            $this->lockStage($parentId, ProcurementDocument::TYPE_AWARD, $user['id'], 'Locked after contract was posted.');
        } elseif ($type === ProcurementDocument::TYPE_NOTICE_TO_PROCEED) {
            $this->lockStage($parentId, ProcurementDocument::TYPE_CONTRACT, $user['id'], 'Locked after notice to proceed was posted.');
        }

        $refreshed = $this->findParentWithWorkflow($parentId);
        if ($refreshed) {
            $this->persistParentState($refreshed['parent'], $refreshed['documents']);
        }

        return ['allowed' => true, 'errors' => [], 'document_id' => $documentId];
    }

    public function updateDocument(string $type, int $documentId, array $data, array $user, ?string $newFilePath = null): array
    {
        $documentModel = $this->documents ?? new ProcurementDocument();
        $document = $documentModel->findById($type, $documentId);
        if (!$document) {
            return ['allowed' => false, 'errors' => ['Document not found.']];
        }

        $workflow = $this->findParentWithWorkflow((int) $document['parent_procurement_id']);
        if (!$workflow) {
            return ['allowed' => false, 'errors' => ['Procurement record not found.']];
        }

        $guard = $this->canEditDocument($type, $document, $workflow['parent'], $workflow['documents'], $user);
        if (!$guard['allowed']) {
            return $guard;
        }

        $documentModel->updateById($type, $documentId, [
            'title' => $data['title'],
            'description' => $data['description'],
            'file_path' => $newFilePath ?? $document['file_path'],
            'posted_at' => $data['posted_at'],
            'updated_by' => $user['id'],
        ]);

        ($this->activityLogs ?? new ProcurementActivityLog())->create([
            'parent_procurement_id' => $document['parent_procurement_id'],
            'document_type' => $type,
            'document_id' => $documentId,
            'sequence_stage' => ProcurementDocument::stageNumber($type),
            'action_type' => 'edit',
            'acted_by' => $user['id'],
            'action_note' => 'Edited ' . ProcurementDocument::label($type) . '.',
        ]);

        if ($type === ProcurementDocument::TYPE_BID_NOTICE) {
            $parent = $workflow['parent'];
            ($this->parents ?? new ParentProcurement())->updateById((int) $parent['id'], [
                'reference_number' => $parent['reference_number'],
                'procurement_title' => $data['title'],
                'abc' => $parent['abc'],
                'mode_of_procurement' => $parent['mode_of_procurement'],
                'posting_date' => $data['posted_at'],
                'bid_submission_deadline' => $parent['bid_submission_deadline'],
                'description' => $data['description'],
                'status' => $this->determineParentStatus(array_merge($parent, [
                    'posting_date' => $data['posted_at'],
                    'current_stage' => $parent['current_stage'],
                ])),
                'current_stage' => $parent['current_stage'],
                'is_archived' => $parent['is_archived'],
                'archived_at' => $parent['archived_at'] ?? null,
                'region' => $parent['region'],
                'branch' => $parent['branch'] ?? null,
                'updated_by' => $user['id'],
            ]);
        }

        return ['allowed' => true, 'errors' => []];
    }

    public function reopenDocument(string $type, int $documentId, array $user): array
    {
        if (($user['role'] ?? '') !== 'admin') {
            return ['allowed' => false, 'errors' => ['Only an admin may reopen a locked stage.']];
        }

        $document = ($this->documents ?? new ProcurementDocument())->findById($type, $documentId);
        if (!$document) {
            return ['allowed' => false, 'errors' => ['Document not found.']];
        }

        ($this->documents ?? new ProcurementDocument())->reopen($type, $documentId, (int) $user['id']);
        ($this->activityLogs ?? new ProcurementActivityLog())->create([
            'parent_procurement_id' => $document['parent_procurement_id'],
            'document_type' => $type,
            'document_id' => $documentId,
            'sequence_stage' => ProcurementDocument::stageNumber($type),
            'action_type' => 'reopen',
            'acted_by' => $user['id'],
            'action_note' => 'Admin reopened ' . ProcurementDocument::label($type) . ' for editing.',
        ]);

        return ['allowed' => true, 'errors' => []];
    }

    public function canCreateDocument(string $type, array $parent, ?array $documents = null): array
    {
        $documents = $documents ?? $this->documentsForParent((int) $parent['id']);
        $hasBidNotice = !empty($documents[ProcurementDocument::TYPE_BID_NOTICE]);
        $hasResolution = !empty($documents[ProcurementDocument::TYPE_RESOLUTION]);
        $hasAward = !empty($documents[ProcurementDocument::TYPE_AWARD]);
        $hasContract = !empty($documents[ProcurementDocument::TYPE_CONTRACT]);
        $deadlinePassed = $this->hasBidDeadlinePassed($parent);
        $errors = [];

        if ($type !== ProcurementDocument::TYPE_BID_NOTICE && !$hasBidNotice) {
            $errors[] = 'Bid Notice / Invitation to Bid must be posted first.';
        }

        if ($type === ProcurementDocument::TYPE_SBB) {
            if ($deadlinePassed) {
                $errors[] = 'Supplemental/Bid Bulletin may only be posted before the bid submission deadline.';
            }
            if ($hasResolution) {
                $errors[] = 'Supplemental/Bid Bulletin may no longer be posted after Resolution has been posted.';
            }
        }

        if ($type === ProcurementDocument::TYPE_RESOLUTION && $this->hasDocument($documents, $type)) {
            $errors[] = 'Resolution has already been posted for this procurement.';
        }

        if ($type === ProcurementDocument::TYPE_AWARD) {
            // Any reconsideration or post-bid issue must already be resolved offline before the signed
            // Resolution and Award are uploaded. The posting module therefore blocks Award until Resolution exists.
            if (!$hasResolution) {
                $errors[] = 'Resolution must be posted before Notice of Award / Award.';
            }
            if ($this->hasDocument($documents, $type)) {
                $errors[] = 'Notice of Award / Award has already been posted for this procurement.';
            }
        }

        if ($type === ProcurementDocument::TYPE_CONTRACT) {
            // The winning bidder's post-award compliance is handled offline before upload.
            // The posting system only allows Contract after the Notice of Award / Award is already posted.
            if (!$hasAward) {
                $errors[] = 'Notice of Award / Award must be posted before Contract.';
            }
            if ($this->hasDocument($documents, $type)) {
                $errors[] = 'Contract has already been posted for this procurement.';
            }
        }

        if ($type === ProcurementDocument::TYPE_NOTICE_TO_PROCEED) {
            if (!$hasContract) {
                $errors[] = 'Contract must be posted before Notice to Proceed.';
            }
            if ($this->hasDocument($documents, $type)) {
                $errors[] = 'Notice to Proceed has already been posted for this procurement.';
            }
        }

        if ($type === ProcurementDocument::TYPE_BID_NOTICE && $hasBidNotice) {
            $errors[] = 'Bid Notice / Invitation to Bid already exists for this procurement.';
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
            'helper_text' => $errors[0] ?? 'Ready to post.',
        ];
    }

    public function canEditDocument(string $type, array $document, array $parent, array $documents, array $user): array
    {
        $isOwner = (int) ($parent['created_by'] ?? 0) === (int) ($user['id'] ?? 0);
        $isAdmin = ($user['role'] ?? '') === 'admin';
        $errors = [];

        if (!$isOwner && !$isAdmin) {
            $errors[] = 'You do not have permission to edit this procurement posting.';
        }

        if ((int) ($document['is_locked'] ?? 0) === 1 && (int) ($document['is_reopened'] ?? 0) !== 1) {
            $errors[] = (string) (($document['lock_reason'] ?? '') !== '' ? $document['lock_reason'] : 'This stage is locked because a later stage has already been posted.');
        }

        if ($type === ProcurementDocument::TYPE_SBB && ($this->hasBidDeadlinePassed($parent) || !empty($documents[ProcurementDocument::TYPE_RESOLUTION]))) {
            $errors[] = 'Supplemental/Bid Bulletin becomes read-only once Resolution is posted or the bid submission deadline has passed.';
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
        ];
    }

    public function publicList(?string $search = null, ?string $region = null, ?string $mode = null): array
    {
        $parents = ($this->parents ?? new ParentProcurement())->findPublic($search, $region, $mode);
        $results = [];
        foreach ($parents as $parent) {
            $parent = $this->refreshParentState($parent);
            if ((int) ($parent['is_archived'] ?? 0) === 1 || ($parent['status'] ?? '') === 'pending') {
                continue;
            }
            $results[] = $parent;
        }

        return $results;
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

        $parent['current_stage'] = $this->currentStage($parent, $documents);
        $parent['status'] = $this->determineParentStatus(array_merge($parent, ['current_stage' => $parent['current_stage']]));

        return $parent;
    }

    private function persistParentState(array $parent, array $documents): void
    {
        $currentStage = $this->currentStage($parent, $documents);
        $status = $this->determineParentStatus(array_merge($parent, ['current_stage' => $currentStage]));

        if (($parent['current_stage'] ?? null) !== $currentStage || ($parent['status'] ?? null) !== $status) {
            ($this->parents ?? new ParentProcurement())->updateStageAndStatus((int) $parent['id'], $currentStage, $status);
        }
    }

    private function lockStage(int $parentId, string $type, int $userId, string $reason): void
    {
        $documentModel = $this->documents ?? new ProcurementDocument();
        $documents = $documentModel->findForParent($type, $parentId);
        if ($documents === []) {
            return;
        }

        $documentModel->lockForParent($type, $parentId, $reason);
        foreach ($documents as $document) {
            ($this->activityLogs ?? new ProcurementActivityLog())->create([
                'parent_procurement_id' => $parentId,
                'document_type' => $type,
                'document_id' => $document['id'],
                'sequence_stage' => ProcurementDocument::stageNumber($type),
                'action_type' => 'lock',
                'acted_by' => $userId,
                'action_note' => $reason,
            ]);
        }
    }

    private function hasDocument(array $documents, string $type): bool
    {
        return !empty($documents[$type]);
    }

    private function hasBidDeadlinePassed(array $parent): bool
    {
        return new DateTimeImmutable((string) $parent['bid_submission_deadline']) < new DateTimeImmutable();
    }
}
