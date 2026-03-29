<?php

namespace App\Services;

use App\Models\Notice;
use DateTimeImmutable;

class DateStatusService extends BaseService
{
    public function __construct(private readonly ?Notice $notices = null)
    {
    }

    public function determineStatus(
        string $startDate,
        string $endDate,
        bool $isArchived = false,
        ?DateTimeImmutable $now = null
    ): string {
        if ($isArchived) {
            return 'archived';
        }

        $now = $now ?? new DateTimeImmutable();
        $start = new DateTimeImmutable($startDate);
        $end = new DateTimeImmutable($endDate);

        if ($end < $now) {
            return 'expired';
        }

        if ($start > $now) {
            return 'pending';
        }

        return 'active';
    }

    public function synchronizeNotice(array $notice): array
    {
        $computedStatus = $this->determineStatus(
            (string) $notice['start_date'],
            (string) $notice['end_date'],
            (bool) ($notice['is_archived'] ?? false)
        );

        if (($notice['status'] ?? null) !== $computedStatus) {
            ($this->notices ?? new Notice())->updateStatus((int) $notice['id'], $computedStatus);
            $notice['status'] = $computedStatus;
        }

        return $notice;
    }

    public function synchronizeCollection(array $notices): array
    {
        $results = [];

        foreach ($notices as $notice) {
            $results[] = $this->synchronizeNotice($notice);
        }

        return $results;
    }
}
