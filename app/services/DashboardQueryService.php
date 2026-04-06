<?php

namespace App\Services;

use App\Models\ParentProcurement;
use App\Models\User;

class DashboardQueryService extends BaseService
{
    public function __construct(
        private readonly ?ParentProcurement $parents = null,
        private readonly ?User $users = null,
        private readonly ?ProcurementPostingService $posting = null
    ) {
    }

    public function overview(array $currentUser): array
    {
        $parentModel = $this->parents ?? new ParentProcurement();
        $posting = $this->posting ?? new ProcurementPostingService($parentModel);
        $isAdmin = ($currentUser['role'] ?? null) === 'admin';
        $records = $isAdmin
            ? $parentModel->findAll()
            : $parentModel->findByCreator((int) ($currentUser['id'] ?? 0));

        $counts = [
            'total_bids' => 0,
            'pending' => 0,
            'active' => 0,
            'expired' => 0,
            'archived' => 0,
        ];

        foreach ($records as $record) {
            $record = $posting->refreshParentState($record);
            $counts['total_bids']++;
            $status = (string) ($record['status'] ?? '');
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
