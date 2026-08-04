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

    public function overview(array $currentUser, ?string $mode = null): array
    {
        $parentModel = $this->parents ?? new ParentProcurement();
        $posting = $this->posting ?? new ProcurementPostingService($parentModel);
        $isAdmin = ($currentUser['role'] ?? null) === 'admin';
        $records = $isAdmin
            ? $parentModel->findAll()
            : $parentModel->findByCreator((int) ($currentUser['id'] ?? 0));

        $counts = [
            'total_procurements' => 0,
            'scheduled' => 0,
            'open' => 0,
            'closed' => 0,
            'archived' => 0,
            'recent_documents' => [],
        ];

        foreach ($records as $record) {
            $record = $posting->refreshParentState($record);
            if ($mode !== null && ($record['procurement_mode'] ?? $record['mode_of_procurement'] ?? null) !== $mode) {
                continue;
            }
            $counts['total_procurements']++;
            $status = (string) ($record['posting_status'] ?? '');
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }

            $workflow = $posting->findParentWithWorkflow((int) $record['id']);
            foreach (array_slice(array_reverse($workflow['timeline'] ?? []), 0, 3) as $document) {
                $counts['recent_documents'][] = [
                    'procurement_id' => (int) $record['id'],
                    'procurement_title' => $record['procurement_title'],
                    'document_type' => $document['document_type'] ?? '',
                    'posted_at' => $document['posted_at'] ?? null,
                ];
            }
        }

        if ($isAdmin) {
            $counts['users'] = count(($this->users ?? new User())->all());
        }

        usort($counts['recent_documents'], static fn (array $left, array $right): int => strcmp((string) ($right['posted_at'] ?? ''), (string) ($left['posted_at'] ?? '')));
        $counts['recent_documents'] = array_slice($counts['recent_documents'], 0, 8);

        return $counts;
    }

    public function procurementTable(array $currentUser): array
    {
        $parentModel = $this->parents ?? new ParentProcurement();
        $posting = $this->posting ?? new ProcurementPostingService($parentModel);
        $isAdmin = ($currentUser['role'] ?? null) === 'admin';
        $records = $isAdmin
            ? $parentModel->findAll()
            : $parentModel->findByCreator((int) ($currentUser['id'] ?? 0));

        $rows = array_map(static fn (array $record): array => $posting->refreshParentState($record), $records);

        usort($rows, static function (array $left, array $right): int {
            return strcmp((string) ($right['created_at'] ?? ''), (string) ($left['created_at'] ?? ''));
        });

        return $rows;
    }
}
