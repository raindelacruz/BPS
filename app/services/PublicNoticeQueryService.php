<?php

namespace App\Services;

use App\Models\Notice;

class PublicNoticeQueryService extends BaseService
{
    public function __construct(
        private readonly ?Notice $notices = null,
        private readonly ?DateStatusService $statusService = null
    ) {
    }

    public function listPublicBids(?string $search = null, ?string $region = null, ?string $procurementType = null): array
    {
        $noticeModel = $this->notices ?? new Notice();
        $statusService = $this->statusService ?? new DateStatusService($noticeModel);
        $bids = $noticeModel->findPublicActiveBids($search, $region, $procurementType);

        return $statusService->synchronizeCollection($bids);
    }

    public function publicWorkflowSet(string $referenceCode): array
    {
        $noticeModel = $this->notices ?? new Notice();
        $statusService = $this->statusService ?? new DateStatusService($noticeModel);
        $workflowSet = $statusService->synchronizeCollection($noticeModel->findByReferenceCode($referenceCode));

        return array_values(array_filter($workflowSet, static function (array $notice): bool {
            return (int) ($notice['is_archived'] ?? 0) === 0 && ($notice['status'] ?? null) !== 'pending' && ($notice['status'] ?? null) !== 'archived';
        }));
    }

    public function publicBidById(int $id): ?array
    {
        $noticeModel = $this->notices ?? new Notice();
        $statusService = $this->statusService ?? new DateStatusService($noticeModel);
        $notice = $noticeModel->findById($id);

        if (!$notice) {
            return null;
        }

        $notice = $statusService->synchronizeNotice($notice);

        if (($notice['type'] ?? null) !== 'bid' || (int) ($notice['is_archived'] ?? 0) === 1 || ($notice['status'] ?? null) !== 'active') {
            return null;
        }

        return $notice;
    }

    public function publicVisibleNoticeById(int $id): ?array
    {
        $noticeModel = $this->notices ?? new Notice();
        $statusService = $this->statusService ?? new DateStatusService($noticeModel);
        $notice = $noticeModel->findById($id);

        if (!$notice) {
            return null;
        }

        $notice = $statusService->synchronizeNotice($notice);
        $rootBid = $noticeModel->findWorkflowBidByReferenceCode((string) ($notice['reference_code'] ?? ''));

        if (!$rootBid) {
            return null;
        }

        $rootBid = $statusService->synchronizeNotice($rootBid);

        if (($rootBid['type'] ?? null) !== 'bid' || (int) ($rootBid['is_archived'] ?? 0) === 1 || ($rootBid['status'] ?? null) !== 'active') {
            return null;
        }

        if ((int) ($notice['is_archived'] ?? 0) === 1 || in_array(($notice['status'] ?? null), ['pending', 'archived'], true)) {
            return null;
        }

        return $notice;
    }
}
