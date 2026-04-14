<?php

namespace App\Services;

use App\Helpers\ValidationHelper;
use App\Models\ProcurementDocument;
use DateTimeImmutable;

class SmallValueProcurementService extends BaseService
{
    public const MODE = 'svp';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_ARCHIVED = 'archived';

    public function allowedDocumentTypes(): array
    {
        return ProcurementDocument::svpTypes();
    }

    public function stageLabels(): array
    {
        return [
            ProcurementDocument::TYPE_RFQ => 'Request for Quotation',
            ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS => 'Abstract of Quotations',
            ProcurementDocument::TYPE_CANVASS => 'Canvass',
            ProcurementDocument::TYPE_AWARD => 'Award',
            ProcurementDocument::TYPE_CONTRACT_OR_PO => 'Contract or Purchase Order',
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
            'description' => trim((string) ($input['description'] ?? '')),
            'category' => trim((string) ($input['category'] ?? '')),
            'end_user_unit' => trim((string) ($input['end_user_unit'] ?? '')),
            'posting_date' => null,
            'bid_submission_deadline' => null,
        ];
        $errors = [];

        foreach (['procurement_title', 'reference_number', 'abc', 'description', 'category', 'end_user_unit'] as $field) {
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

        return ['data' => $data, 'errors' => $errors];
    }

    public function determinePostingStatus(array $parent, array $documents, ?DateTimeImmutable $now = null): string
    {
        if (!empty($parent['archived_at']) || (int) ($parent['is_archived'] ?? 0) === 1) {
            return self::STATUS_ARCHIVED;
        }

        $rfq = $documents[ProcurementDocument::TYPE_RFQ][0] ?? null;
        if (!$rfq) {
            return self::STATUS_SCHEDULED;
        }

        $now ??= new DateTimeImmutable();
        $rfqPostingDate = new DateTimeImmutable((string) $rfq['posted_at']);
        $deadline = $this->rfqDeadline($parent, $rfq);

        if ($rfqPostingDate > $now) {
            return self::STATUS_SCHEDULED;
        }

        if ($deadline < $now) {
            return self::STATUS_CLOSED;
        }

        return self::STATUS_OPEN;
    }

    public function currentStage(array $documents): string
    {
        foreach ([ProcurementDocument::TYPE_CONTRACT_OR_PO, ProcurementDocument::TYPE_AWARD, ProcurementDocument::TYPE_CANVASS, ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS, ProcurementDocument::TYPE_RFQ] as $type) {
            if (!empty($documents[$type])) {
                return $type;
            }
        }

        return ProcurementDocument::TYPE_RFQ;
    }

    public function canCreateDocument(string $type, array $parent, array $documents, ?string $postedAt = null): array
    {
        $errors = [];
        $status = $this->determinePostingStatus($parent, $documents);

        if (!in_array($type, $this->allowedDocumentTypes(), true)) {
            $errors[] = 'Document type is not allowed for Small Value Procurement.';
        }
        if (!empty($parent['archived_at']) || (string) ($parent['posting_status'] ?? '') === self::STATUS_ARCHIVED) {
            $errors[] = 'Archived SVP records are immutable and cannot accept new postings.';
        }
        if ($type === ProcurementDocument::TYPE_RFQ && !empty($documents[ProcurementDocument::TYPE_RFQ])) {
            $errors[] = 'Only one RFQ is allowed per SVP procurement.';
        }
        if (in_array($type, [ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS, ProcurementDocument::TYPE_CANVASS, ProcurementDocument::TYPE_AWARD, ProcurementDocument::TYPE_CONTRACT_OR_PO], true) && empty($documents[ProcurementDocument::TYPE_RFQ])) {
            $errors[] = 'RFQ must be posted first.';
        }
        if ($type === ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS && !empty($documents[ProcurementDocument::TYPE_CANVASS])) {
            $errors[] = 'Abstract of Quotations cannot be posted after a Canvass has already been posted.';
        }
        if ($type === ProcurementDocument::TYPE_CANVASS && !empty($documents[ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS])) {
            $errors[] = 'Canvass cannot be posted after an Abstract of Quotations has already been posted.';
        }
        if ($type === ProcurementDocument::TYPE_AWARD) {
            if (empty($documents[ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS]) && empty($documents[ProcurementDocument::TYPE_CANVASS])) {
                $errors[] = 'Award requires a posted Abstract of Quotations or Canvass.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_AWARD])) {
                $errors[] = 'Only one Award is allowed per SVP procurement.';
            }
            if ($status !== self::STATUS_CLOSED) {
                $errors[] = 'Award may only be posted after the RFQ is CLOSED.';
            }
        }
        if ($type === ProcurementDocument::TYPE_CONTRACT_OR_PO) {
            if (empty($documents[ProcurementDocument::TYPE_AWARD])) {
                $errors[] = 'Contract or Purchase Order requires a posted Award.';
            }
            if (!empty($documents[ProcurementDocument::TYPE_CONTRACT_OR_PO])) {
                $errors[] = 'Only one Contract or Purchase Order is allowed per SVP procurement.';
            }
        }

        if (in_array($type, [ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS, ProcurementDocument::TYPE_CANVASS], true) && $postedAt !== null && $postedAt !== '') {
            $rfqPostedAt = $documents[ProcurementDocument::TYPE_RFQ][0]['posted_at'] ?? null;
            if ($rfqPostedAt !== null && !$this->isOnOrAfter($postedAt, (string) $rfqPostedAt)) {
                $errors[] = ProcurementDocument::label($type) . ' date must be on or after the RFQ date.';
            }
        }
        if ($type === ProcurementDocument::TYPE_AWARD && $postedAt !== null && $postedAt !== '') {
            $basisDate = $this->latestBasisDate($documents);
            if ($basisDate !== null && !$this->isOnOrAfter($postedAt, $basisDate)) {
                $errors[] = 'Award date must be on or after the Abstract of Quotations or Canvass date.';
            }
        }
        if ($type === ProcurementDocument::TYPE_CONTRACT_OR_PO && $postedAt !== null && $postedAt !== '') {
            $awardDate = $this->latestPostedAt($documents, ProcurementDocument::TYPE_AWARD);
            if ($awardDate !== null && !$this->isOnOrAfter($postedAt, $awardDate)) {
                $errors[] = 'Contract or Purchase Order date must be on or after the Award date.';
            }
        }

        return [
            'allowed' => $errors === [],
            'errors' => $errors,
            'helper_text' => $errors[0] ?? 'Ready for posting.',
        ];
    }

    private function latestBasisDate(array $documents): ?string
    {
        $abstractDate = $this->latestPostedAt($documents, ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS);
        $canvassDate = $this->latestPostedAt($documents, ProcurementDocument::TYPE_CANVASS);

        return $canvassDate ?? $abstractDate;
    }

    private function latestPostedAt(array $documents, string $type): ?string
    {
        if (empty($documents[$type])) {
            return null;
        }

        $last = $documents[$type][count($documents[$type]) - 1];

        return (string) ($last['posted_at'] ?? null);
    }

    private function rfqDeadline(array $parent, array $rfq): DateTimeImmutable
    {
        if (!empty($parent['bid_submission_deadline'])) {
            return new DateTimeImmutable((string) $parent['bid_submission_deadline']);
        }

        return new DateTimeImmutable((string) $rfq['posted_at']);
    }

    private function isOnOrAfter(string $left, string $right): bool
    {
        return new DateTimeImmutable($left) >= new DateTimeImmutable($right);
    }
}
