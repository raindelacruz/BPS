<?php

namespace App\Services;

use App\Models\Notice;
use DateTimeImmutable;

class ArchiveService extends BaseService
{
    public function __construct(
        private readonly ?Notice $notices = null,
        private readonly ?DateStatusService $statusService = null
    ) {
    }

    public function canArchive(array $notice, array $currentUser): array
    {
        $errors = [];
        $referenceCode = (string) ($notice['reference_code'] ?? '');
        $isOwner = (int) ($notice['uploaded_by'] ?? 0) === (int) ($currentUser['id'] ?? 0);

        if (!$isOwner) {
            $errors[] = 'Only the uploader may archive this workflow set.';
        }

        $noticeModel = $this->notices ?? new Notice();
        $workflowSet = ($this->statusService ?? new DateStatusService($noticeModel))
            ->synchronizeCollection($noticeModel->findByReferenceCode($referenceCode));

        $hasActiveProceed = false;
        foreach ($workflowSet as $item) {
            if (($item['type'] ?? null) === 'proceed' && ($item['status'] ?? null) === 'active' && (int) ($item['is_archived'] ?? 0) === 0) {
                $hasActiveProceed = true;
                break;
            }
        }

        if (!$hasActiveProceed && ($notice['status'] ?? null) !== 'expired') {
            $errors[] = 'Archive is allowed only when an active proceed exists for this reference code or the current notice is expired.';
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
        ];
    }

    public function archive(string $referenceCode): bool
    {
        $archivedAt = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        return ($this->notices ?? new Notice())->updateArchiveStateByReferenceCode($referenceCode, true, $archivedAt);
    }

    public function unarchive(string $referenceCode): bool
    {
        $noticeModel = $this->notices ?? new Notice();
        $statusService = $this->statusService ?? new DateStatusService($noticeModel);
        $workflowSet = $noticeModel->findByReferenceCode($referenceCode);

        foreach ($workflowSet as $notice) {
            $status = $statusService->determineStatus(
                (string) $notice['start_date'],
                (string) $notice['end_date'],
                false
            );

            $noticeModel->updateLifecycleFieldsById((int) $notice['id'], $status, false, null);
        }

        return true;
    }
}
