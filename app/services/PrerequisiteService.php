<?php

namespace App\Services;

use App\Models\Notice;

class PrerequisiteService extends BaseService
{
    public function __construct(
        private readonly ?Notice $notices = null,
        private readonly ?DateStatusService $statusService = null
    ) {
    }

    public function eligibleParentBids(string $targetType): array
    {
        $noticeModel = $this->notices ?? new Notice();
        $statusService = $this->statusService ?? new DateStatusService($noticeModel);
        $bids = $statusService->synchronizeCollection($noticeModel->findActiveNonArchivedBids());
        $eligible = [];

        foreach ($bids as $bid) {
            $check = $this->validateForBid($targetType, $bid);

            if ($check['allowed']) {
                $eligible[] = $bid;
            }
        }

        return $eligible;
    }

    public function validateForBid(string $targetType, array $bid, ?int $ignoreNoticeId = null): array
    {
        $noticeModel = $this->notices ?? new Notice();
        $type = strtolower(trim($targetType));

        if (($bid['type'] ?? null) !== 'bid') {
            return ['allowed' => false, 'errors' => ['Selected parent notice must be a bid.']];
        }

        if ((int) ($bid['is_archived'] ?? 0) === 1 || ($bid['status'] ?? null) !== 'active') {
            return ['allowed' => false, 'errors' => ['Selected bid must exist and be active.']];
        }

        $referenceCode = (string) $bid['reference_code'];
        $region = (string) ($bid['region'] ?? '');

        return match ($type) {
            'sbb' => ['allowed' => true, 'errors' => []],
            'resolution' => $noticeModel->hasActiveNonArchivedType($referenceCode, 'resolution', $region, $ignoreNoticeId)
                ? ['allowed' => false, 'errors' => ['An active non-archived resolution already exists for this reference code and region.']]
                : ['allowed' => true, 'errors' => []],
            'award' => $this->validateRequiredAndUnique($referenceCode, 'resolution', 'award', null, $ignoreNoticeId),
            'contract' => $this->validateRequiredAndUnique($referenceCode, 'award', 'contract', null, $ignoreNoticeId),
            'proceed' => $this->validateRequiredAndUnique($referenceCode, 'contract', 'proceed', null, $ignoreNoticeId),
            default => ['allowed' => false, 'errors' => ['Unsupported notice type.']],
        };
    }

    private function validateRequiredAndUnique(
        string $referenceCode,
        string $requiredType,
        string $targetType,
        ?string $region = null,
        ?int $ignoreNoticeId = null
    ): array {
        $noticeModel = $this->notices ?? new Notice();
        $errors = [];

        if (!$noticeModel->hasActiveNonArchivedType($referenceCode, $requiredType, $region)) {
            $errors[] = ucfirst($requiredType) . ' must already exist for the same reference code.';
        }

        if ($noticeModel->hasActiveNonArchivedType($referenceCode, $targetType, $region, $ignoreNoticeId)) {
            $errors[] = 'An active non-archived ' . $targetType . ' already exists for this reference code.';
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
        ];
    }
}
