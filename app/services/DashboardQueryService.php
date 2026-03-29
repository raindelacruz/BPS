<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\User;

class DashboardQueryService extends BaseService
{
    public function __construct(
        private readonly ?Notice $notices = null,
        private readonly ?User $users = null,
        private readonly ?DateStatusService $statusService = null
    ) {
    }

    public function overview(array $currentUser): array
    {
        $noticeModel = $this->notices ?? new Notice();
        $statusService = $this->statusService ?? new DateStatusService($noticeModel);
        $isAdmin = ($currentUser['role'] ?? null) === 'admin';
        $records = $isAdmin
            ? $noticeModel->findAllBids()
            : $noticeModel->findByUploader((int) ($currentUser['id'] ?? 0));

        $records = $statusService->synchronizeCollection($records);

        $counts = [
            'total_bids' => 0,
            'pending' => 0,
            'active' => 0,
            'expired' => 0,
            'archived' => 0,
        ];

        foreach ($records as $notice) {
            if (($notice['type'] ?? null) !== 'bid') {
                continue;
            }

            $counts['total_bids']++;
            $status = (string) ($notice['status'] ?? '');
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
        }

        if ($isAdmin) {
            $counts['users'] = count(($this->users ?? new User())->all());
        }

        return $counts;
    }
}
