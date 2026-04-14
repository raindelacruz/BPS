<?php

namespace App\Services;

use App\Helpers\ValidationHelper;
use App\Models\ProcurementDocument;
use DateTimeImmutable;

class CompetitiveBiddingService extends BaseService
{
    public const MODE = 'competitive_bidding';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_ARCHIVED = 'archived';

    public function allowedDocumentTypes(): array
    {
        return ProcurementDocument::competitiveTypes();
    }

    public function stageLabels(): array
    {
        return [
            ProcurementDocument::TYPE_BID_NOTICE => 'Bid Notice',
            ProcurementDocument::TYPE_SBB => 'Supplemental Bid Bulletin',
            ProcurementDocument::TYPE_RESOLUTION => 'Resolution',
            ProcurementDocument::TYPE_AWARD => 'Award',
            ProcurementDocument::TYPE_CONTRACT => 'Contract',
            ProcurementDocument::TYPE_NOTICE_TO_PROCEED => 'Notice to Proceed',
        ];
    }

    public function statusLabels(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public function validateParentInput(array $input): array
    {
        $data = [
            'procurement_mode' => self::MODE,
            'procurement_title' => trim((string) ($input['procurement_title'] ?? '')),
            'reference_number' => trim((string) ($input['reference_number'] ?? '')),
            'abc' => trim((string) ($input['abc'] ?? '')),
            'posting_date' => trim((string) ($input['posting_date'] ?? '')),
            'bid_submission_deadline' => trim((string) ($input['bid_submission_deadline'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'category' => trim((string) ($input['category'] ?? '')),
            'end_user_unit' => trim((string) ($input['end_user_unit'] ?? '')),
        ];
        $errors = [];

        foreach (['procurement_title', 'reference_number', 'abc', 'posting_date', 'bid_submission_deadline', 'description', 'category', 'end_user_unit'] as $field) {
            if ($data[$field] === '') {
                ValidationHelper::addError($errors, $field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        if ($data['abc'] !== '') {
            $normalized = str_replace(',', '', $data['abc']);
            if (!is_numeric($normalized) || (float) $normalized < 0) {
                ValidationHelper::addError($errors, 'abc', 'ABC must be a valid non-negative amount.');
            } else {
                $data['abc'] = number_format((float) $normalized, 2, '.', '');
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

        return ['data' => $data, 'errors' => $errors];
    }

    public function determinePostingStatus(array $parent, ?DateTimeImmutable $now = null): string
    {
        if (!empty($parent['archived_at']) || (int) ($parent['is_archived'] ?? 0) === 1) {
            return self::STATUS_ARCHIVED;
        }

        $now ??= new DateTimeImmutable();
        $postingDate = new DateTimeImmutable((string) $parent['posting_date']);
        $deadline = new DateTimeImmutable((string) $parent['bid_submission_deadline']);

        if ($postingDate > $now) {
            return self::STATUS_SCHEDULED;
        }

        if ($deadline < $now) {
            return self::STATUS_CLOSED;
        }

        return self::STATUS_OPEN;
    }

    public function currentStage(array $documents): string
    {
        foreach ([ProcurementDocument::TYPE_NOTICE_TO_PROCEED, ProcurementDocument::TYPE_CONTRACT, ProcurementDocument::TYPE_AWARD, ProcurementDocument::TYPE_RESOLUTION, ProcurementDocument::TYPE_SBB, ProcurementDocument::TYPE_BID_NOTICE] as $type) {
            if (!empty($documents[$type])) {
                return $type;
            }
        }

        return ProcurementDocument::TYPE_BID_NOTICE;
    }

    public function canCreateDocument(string $type, array $parent, array $documents, ?string $postedAt = null): array
    {
        $errors = [];
        $status = $this->determinePostingStatus($parent);

        if (!in_array($type, $this->allowedDocumentTypes(), true)) {
            $errors[] = 'Document type is not allowed for Competitive Bidding.';
        }
        if (!empty($parent['archived_at']) || (string) ($parent['posting_status'] ?? '') === self::STATUS_ARCHIVED) {
            $errors[] = 'Archived procurement records are immutable and cannot accept new postings.';
        }
        if ($type !== ProcurementDocument::TYPE_BID_NOTICE && empty($documents[ProcurementDocument::TYPE_BID_NOTICE])) {
            $errors[] = 'Bid Notice must be posted first.';
        }
        if ($type === ProcurementDocument::TYPE_BID_NOTICE && !empty($documents[ProcurementDocument::TYPE_BID_NOTICE])) {
            $errors[] = 'Only one Bid Notice is allowed per Competitive Bidding procurement.';
        }
        if ($type === ProcurementDocument::TYPE_SBB && $status !== self::STATUS_OPEN) {
            $errors[] = 'Supplemental Bid Bulletin is allowed only while the procurement is OPEN.';
        }
        if (in_array($type, [ProcurementDocument::TYPE_RESOLUTION, ProcurementDocument::TYPE_AWARD, ProcurementDocument::TYPE_CONTRACT, ProcurementDocument::TYPE_NOTICE_TO_PROCEED], true) && $status !== self::STATUS_CLOSED) {
            $errors[] = 'This document may only be posted after the procurement is CLOSED.';
        }
        if ($type === ProcurementDocument::TYPE_RESOLUTION && !empty($documents[ProcurementDocument::TYPE_RESOLUTION])) {
            $errors[] = 'Only one Resolution is allowed per Competitive Bidding procurement.';
        }
        if ($type === ProcurementDocument::TYPE_AWARD) {
            if (empty($documents[ProcurementDocument::TYPE_RESOLUTION])) {
                $errors[] = 'Award requires a posted Resolution.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_AWARD])) {
                $errors[] = 'Only one Award is allowed per Competitive Bidding procurement.';
            }
        }
        if ($type === ProcurementDocument::TYPE_CONTRACT) {
            if (empty($documents[ProcurementDocument::TYPE_AWARD])) {
                $errors[] = 'Contract requires a posted Award.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_CONTRACT])) {
                $errors[] = 'Only one Contract is allowed per Competitive Bidding procurement.';
            }
        }
        if ($type === ProcurementDocument::TYPE_NOTICE_TO_PROCEED) {
            if (empty($documents[ProcurementDocument::TYPE_CONTRACT])) {
                $errors[] = 'Notice to Proceed requires a posted Contract.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_NOTICE_TO_PROCEED])) {
                $errors[] = 'Only one Notice to Proceed is allowed per Competitive Bidding procurement.';
            }
        }

        if ($postedAt !== null && $postedAt !== '') {
            $errors = array_merge($errors, $this->validateChronology($type, $parent, $documents, $postedAt));
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
            'helper_text' => $errors[0] ?? 'Ready for posting.',
        ];
    }

    private function validateChronology(string $type, array $parent, array $documents, string $postedAt): array
    {
        $errors = [];

        if ($type === ProcurementDocument::TYPE_SBB) {
            if (!$this->isOnOrAfter($postedAt, (string) $parent['posting_date'])) {
                $errors[] = 'Supplemental Bid Bulletin date must be on or after the Bid Notice posting date.';
            }
            if (!$this->isOnOrBefore($postedAt, (string) $parent['bid_submission_deadline'])) {
                $errors[] = 'Supplemental Bid Bulletin date must be on or before the bid submission deadline.';
            }
        }

        if ($type === ProcurementDocument::TYPE_RESOLUTION && !$this->isAfter($postedAt, (string) $parent['bid_submission_deadline'])) {
            $errors[] = 'Resolution date must be later than the bid submission deadline.';
        }

        $dependencyMap = [
            ProcurementDocument::TYPE_AWARD => ProcurementDocument::TYPE_RESOLUTION,
            ProcurementDocument::TYPE_CONTRACT => ProcurementDocument::TYPE_AWARD,
            ProcurementDocument::TYPE_NOTICE_TO_PROCEED => ProcurementDocument::TYPE_CONTRACT,
        ];

        if (isset($dependencyMap[$type])) {
            $dependencyType = $dependencyMap[$type];
            $latestDependencyDate = $this->latestPostedAt($documents, $dependencyType);
            if ($latestDependencyDate !== null && !$this->isOnOrAfter($postedAt, $latestDependencyDate)) {
                $errors[] = ProcurementDocument::label($type) . ' date must be on or after the ' . ProcurementDocument::label($dependencyType) . ' date.';
            }
        }

        return $errors;
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
