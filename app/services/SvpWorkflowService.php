<?php

namespace App\Services;

use App\Helpers\ValidationHelper;
use App\Models\ParentProcurement;
use App\Models\ProcurementActivityLog;
use App\Models\SvpWorkflow;
use DateInterval;
use DateTimeImmutable;
use DomainException;

class SvpWorkflowService extends BaseService
{
    public const MODE = 'small_value_procurement';
    public const ABC_POSTING_THRESHOLD = 200000.00;
    public const ABC_SINGLE_QUOTE_THRESHOLD = 2000000.00;

    public const STAGE_DRAFT = 'draft';
    public const STAGE_RFQ_PREPARED = 'rfq_prepared';
    public const STAGE_RFQ_POSTED = 'rfq_posted';
    public const STAGE_QUOTATION_OPEN = 'quotation_open';
    public const STAGE_UNDER_EVALUATION = 'under_evaluation';
    public const STAGE_AWARDED = 'awarded';
    public const STAGE_CONTRACT_PREPARED = 'contract_prepared';
    public const STAGE_NTP_ISSUED = 'ntp_issued';
    public const STAGE_COMPLETED = 'completed';
    public const STAGE_ARCHIVED = 'archived';

    public function __construct(
        private readonly ?SvpWorkflow $workflow = null,
        private readonly ?ParentProcurement $parents = null,
        private readonly ?ProcurementActivityLog $activityLogs = null
    ) {
    }

    public static function stageLabels(): array
    {
        return [
            self::STAGE_DRAFT => 'Draft',
            self::STAGE_RFQ_PREPARED => 'RFQ Prepared',
            self::STAGE_RFQ_POSTED => 'RFQ Posted',
            self::STAGE_QUOTATION_OPEN => 'Quotation Open',
            self::STAGE_UNDER_EVALUATION => 'Under Evaluation',
            self::STAGE_AWARDED => 'Awarded',
            self::STAGE_CONTRACT_PREPARED => 'Contract/PO',
            self::STAGE_NTP_ISSUED => 'NTP Issued',
            self::STAGE_COMPLETED => 'Completed',
            self::STAGE_ARCHIVED => 'Archived',
        ];
    }

    public function buildWorkflow(int $parentId): array
    {
        $model = $this->workflow ?? new SvpWorkflow();
        $rfq = $model->findRfqByParent($parentId);
        $postings = $rfq ? $model->postingsForRfq((int) $rfq['id']) : [];
        $suppliers = $model->suppliersForParent($parentId);
        $quotations = $model->quotationsForParent($parentId);
        $evaluation = $model->evaluationForParent($parentId);
        $evaluationItems = $evaluation ? $model->evaluationItems((int) $evaluation['id']) : [];
        $award = $model->awardForParent($parentId);
        $contract = $model->contractForParent($parentId);
        $ntp = $model->ntpForParent($parentId);

        return [
            'rfq' => $rfq,
            'postings' => $postings,
            'suppliers' => $suppliers,
            'quotations' => $quotations,
            'evaluation' => $evaluation,
            'evaluation_items' => $evaluationItems,
            'award' => $award,
            'contract' => $contract,
            'ntp' => $ntp,
            'posting_compliance' => $this->postingCompliance($rfq, $postings),
            'invited_supplier_count' => count(array_filter($suppliers, static fn (array $supplier): bool => (int) ($supplier['is_invited'] ?? 0) === 1)),
            'quotation_count' => count($quotations),
            'responsive_supplier_ids' => array_values(array_map(
                static fn (array $quote): int => (int) $quote['supplier_id'],
                array_filter($quotations, static fn (array $quote): bool => (int) ($quote['is_responsive'] ?? 0) === 1)
            )),
        ];
    }

    public function postingCompliance(?array $rfq, array $postings): array
    {
        $required = [];
        $compliant = true;

        if ($rfq && (int) ($rfq['is_posting_required'] ?? 0) === 1) {
            $required = [
                SvpWorkflow::POSTING_CHANNEL_PHILGEPS,
                SvpWorkflow::POSTING_CHANNEL_WEBSITE,
                SvpWorkflow::POSTING_CHANNEL_CONSPICUOUS,
            ];

            foreach ($required as $channel) {
                $record = null;
                foreach ($postings as $posting) {
                    if (($posting['posting_channel'] ?? '') === $channel) {
                        $record = $posting;
                        break;
                    }
                }

                if (!$record || empty($record['posted_at']) || empty($record['posting_end_at'])) {
                    $compliant = false;
                    continue;
                }

                $start = new DateTimeImmutable((string) $record['posted_at']);
                $end = new DateTimeImmutable((string) $record['posting_end_at']);
                if ($end < $start->add(new DateInterval('P3D'))) {
                    $compliant = false;
                }
            }
        }

        return [
            'required_channels' => $required,
            'is_required' => $required !== [],
            'is_compliant' => $compliant,
        ];
    }

    public function computeStage(array $parent, array $workflow, ?DateTimeImmutable $now = null): string
    {
        if (!empty($parent['archived_at'])) {
            return self::STAGE_ARCHIVED;
        }
        if (!empty($parent['completed_at'])) {
            return self::STAGE_COMPLETED;
        }
        if (!empty($workflow['ntp'])) {
            return self::STAGE_NTP_ISSUED;
        }
        if (!empty($workflow['contract'])) {
            return self::STAGE_CONTRACT_PREPARED;
        }
        if (!empty($workflow['award'])) {
            return self::STAGE_AWARDED;
        }

        $rfq = $workflow['rfq'] ?? null;
        if (!$rfq) {
            return self::STAGE_DRAFT;
        }
        if (empty($rfq['issued_at'])) {
            return self::STAGE_RFQ_PREPARED;
        }

        $now ??= new DateTimeImmutable();
        $deadline = new DateTimeImmutable((string) $rfq['quotation_deadline']);
        if ($now <= $deadline) {
            return self::STAGE_QUOTATION_OPEN;
        }

        if ((int) ($workflow['quotation_count'] ?? 0) > 0 || !empty($workflow['evaluation'])) {
            return self::STAGE_UNDER_EVALUATION;
        }

        return self::STAGE_RFQ_POSTED;
    }

    public function computePostingStatus(array $parent, array $workflow, ?DateTimeImmutable $now = null): string
    {
        if (!empty($parent['archived_at'])) {
            return ProcurementPostingService::POSTING_STATUS_ARCHIVED;
        }

        $rfq = $workflow['rfq'] ?? null;
        if (!$rfq || empty($rfq['issued_at'])) {
            return ProcurementPostingService::POSTING_STATUS_SCHEDULED;
        }

        $now ??= new DateTimeImmutable();
        $deadline = new DateTimeImmutable((string) $rfq['quotation_deadline']);

        return $now <= $deadline
            ? ProcurementPostingService::POSTING_STATUS_OPEN
            : ProcurementPostingService::POSTING_STATUS_CLOSED;
    }

    public function validateRfqInput(array $input, float $abc): array
    {
        $data = [
            'rfq_no' => trim((string) ($input['rfq_no'] ?? '')),
            'rfq_date' => trim((string) ($input['rfq_date'] ?? '')),
            'quotation_deadline' => trim((string) ($input['quotation_deadline'] ?? '')),
            'delivery_period' => trim((string) ($input['delivery_period'] ?? '')),
            'payment_terms' => trim((string) ($input['payment_terms'] ?? '')),
            'warranty_terms' => trim((string) ($input['warranty_terms'] ?? '')),
            'technical_specs' => trim((string) ($input['technical_specs'] ?? '')),
            'terms_and_conditions' => trim((string) ($input['terms_and_conditions'] ?? '')),
            'is_posting_required' => $abc > self::ABC_POSTING_THRESHOLD ? 1 : 0,
        ];
        $errors = [];

        foreach (['rfq_no', 'rfq_date', 'quotation_deadline', 'technical_specs'] as $field) {
            if ($data[$field] === '') {
                ValidationHelper::addError($errors, $field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        if ($data['rfq_date'] !== '' && $data['quotation_deadline'] !== '') {
            try {
                $rfqDate = new DateTimeImmutable($data['rfq_date']);
                $deadline = new DateTimeImmutable($data['quotation_deadline']);
                if ($deadline <= $rfqDate) {
                    ValidationHelper::addError($errors, 'quotation_deadline', 'Quotation deadline must be later than the RFQ date.');
                }
                $data['rfq_date'] = $rfqDate->format('Y-m-d');
                $data['quotation_deadline'] = $deadline->format('Y-m-d H:i:s');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'quotation_deadline', 'RFQ date or quotation deadline is invalid.');
            }
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validatePostingInput(array $input): array
    {
        $data = [
            'posting_channel' => trim((string) ($input['posting_channel'] ?? '')),
            'posting_reference' => trim((string) ($input['posting_reference'] ?? '')),
            'posted_at' => trim((string) ($input['posted_at'] ?? '')),
            'posting_end_at' => trim((string) ($input['posting_end_at'] ?? '')),
            'remarks' => trim((string) ($input['remarks'] ?? '')),
        ];
        $errors = [];

        if (!in_array($data['posting_channel'], [
            SvpWorkflow::POSTING_CHANNEL_PHILGEPS,
            SvpWorkflow::POSTING_CHANNEL_WEBSITE,
            SvpWorkflow::POSTING_CHANNEL_CONSPICUOUS,
        ], true)) {
            ValidationHelper::addError($errors, 'posting_channel', 'Posting channel is invalid.');
        }

        foreach (['posted_at', 'posting_end_at'] as $field) {
            if ($data[$field] === '') {
                ValidationHelper::addError($errors, $field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        if ($data['posted_at'] !== '' && $data['posting_end_at'] !== '') {
            try {
                $postedAt = new DateTimeImmutable($data['posted_at']);
                $endAt = new DateTimeImmutable($data['posting_end_at']);
                if ($endAt < $postedAt->add(new DateInterval('P3D'))) {
                    ValidationHelper::addError($errors, 'posting_end_at', 'Posting must remain available for at least three calendar days.');
                }
                $data['posted_at'] = $postedAt->format('Y-m-d H:i:s');
                $data['posting_end_at'] = $endAt->format('Y-m-d H:i:s');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'posted_at', 'Posting dates are invalid.');
            }
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validateSupplierInput(array $input): array
    {
        $data = [
            'supplier_name' => trim((string) ($input['supplier_name'] ?? '')),
            'tin_no' => trim((string) ($input['tin_no'] ?? '')),
            'address' => trim((string) ($input['address'] ?? '')),
            'contact_person' => trim((string) ($input['contact_person'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
            'philgeps_registration_no' => trim((string) ($input['philgeps_registration_no'] ?? '')),
            'is_invited' => isset($input['is_invited']) ? 1 : 0,
        ];
        $errors = [];

        if ($data['supplier_name'] === '') {
            ValidationHelper::addError($errors, 'supplier_name', 'Supplier name is required.');
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            ValidationHelper::addError($errors, 'email', 'Email address is invalid.');
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validateQuotationInput(array $input): array
    {
        $data = [
            'supplier_id' => (int) ($input['supplier_id'] ?? 0),
            'quotation_no' => trim((string) ($input['quotation_no'] ?? '')),
            'quotation_date' => trim((string) ($input['quotation_date'] ?? '')),
            'amount' => trim((string) ($input['amount'] ?? '')),
            'delivery_offer' => trim((string) ($input['delivery_offer'] ?? '')),
            'warranty_offer' => trim((string) ($input['warranty_offer'] ?? '')),
            'payment_offer' => trim((string) ($input['payment_offer'] ?? '')),
            'submission_time' => trim((string) ($input['submission_time'] ?? '')),
            'is_responsive' => isset($input['is_responsive']) ? 1 : 0,
            'responsiveness_notes' => trim((string) ($input['responsiveness_notes'] ?? '')),
        ];
        $errors = [];

        if ($data['supplier_id'] <= 0) {
            ValidationHelper::addError($errors, 'supplier_id', 'Supplier is required.');
        }
        foreach (['amount', 'submission_time'] as $field) {
            if ($data[$field] === '') {
                ValidationHelper::addError($errors, $field, ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        if ($data['amount'] !== '') {
            $normalized = str_replace(',', '', $data['amount']);
            if (!is_numeric($normalized) || (float) $normalized < 0) {
                ValidationHelper::addError($errors, 'amount', 'Quotation amount must be a valid non-negative number.');
            } else {
                $data['amount'] = number_format((float) $normalized, 2, '.', '');
            }
        }

        if ($data['quotation_date'] !== '') {
            try {
                $data['quotation_date'] = (new DateTimeImmutable($data['quotation_date']))->format('Y-m-d');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'quotation_date', 'Quotation date is invalid.');
            }
        } else {
            $data['quotation_date'] = null;
        }

        if ($data['submission_time'] !== '') {
            try {
                $data['submission_time'] = (new DateTimeImmutable($data['submission_time']))->format('Y-m-d H:i:s');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'submission_time', 'Submission time is invalid.');
            }
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validateEvaluationInput(array $input, array $workflow, array $parent): array
    {
        $data = [
            'evaluation_date' => trim((string) ($input['evaluation_date'] ?? '')),
            'recommended_supplier_id' => (int) ($input['recommended_supplier_id'] ?? 0),
            'recommendation_text' => trim((string) ($input['recommendation_text'] ?? '')),
            'exception_note' => trim((string) ($input['exception_note'] ?? '')),
        ];
        $errors = [];

        if ($data['evaluation_date'] === '') {
            ValidationHelper::addError($errors, 'evaluation_date', 'Evaluation date is required.');
        } else {
            try {
                $data['evaluation_date'] = (new DateTimeImmutable($data['evaluation_date']))->format('Y-m-d');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'evaluation_date', 'Evaluation date is invalid.');
            }
        }

        if ($data['recommended_supplier_id'] <= 0) {
            ValidationHelper::addError($errors, 'recommended_supplier_id', 'Recommended supplier is required.');
        }
        if ($data['recommendation_text'] === '') {
            ValidationHelper::addError($errors, 'recommendation_text', 'Recommendation summary is required.');
        }

        $guard = $this->validateBeforeEvaluation($parent, $workflow);
        foreach ($guard['errors'] as $message) {
            ValidationHelper::addError($errors, '_global', $message);
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validateAwardInput(array $input): array
    {
        $data = [
            'supplier_id' => (int) ($input['supplier_id'] ?? 0),
            'award_no' => trim((string) ($input['award_no'] ?? '')),
            'award_date' => trim((string) ($input['award_date'] ?? '')),
            'award_amount' => trim((string) ($input['award_amount'] ?? '')),
            'award_type' => trim((string) ($input['award_type'] ?? 'notice_of_award')),
            'remarks' => trim((string) ($input['remarks'] ?? '')),
        ];
        $errors = [];

        if ($data['supplier_id'] <= 0) {
            ValidationHelper::addError($errors, 'supplier_id', 'Awarded supplier is required.');
        }
        if ($data['award_date'] === '') {
            ValidationHelper::addError($errors, 'award_date', 'Award date is required.');
        } else {
            try {
                $data['award_date'] = (new DateTimeImmutable($data['award_date']))->format('Y-m-d');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'award_date', 'Award date is invalid.');
            }
        }

        $normalized = str_replace(',', '', $data['award_amount']);
        if ($data['award_amount'] === '' || !is_numeric($normalized) || (float) $normalized < 0) {
            ValidationHelper::addError($errors, 'award_amount', 'Award amount is required.');
        } else {
            $data['award_amount'] = number_format((float) $normalized, 2, '.', '');
        }

        if (!in_array($data['award_type'], ['notice_of_award', 'purchase_order'], true)) {
            ValidationHelper::addError($errors, 'award_type', 'Award type is invalid.');
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validateContractInput(array $input): array
    {
        $data = [
            'contract_no' => trim((string) ($input['contract_no'] ?? '')),
            'contract_date' => trim((string) ($input['contract_date'] ?? '')),
            'contract_amount' => trim((string) ($input['contract_amount'] ?? '')),
            'contract_type' => trim((string) ($input['contract_type'] ?? 'po')),
        ];
        $errors = [];

        if ($data['contract_date'] === '') {
            ValidationHelper::addError($errors, 'contract_date', 'Contract or PO date is required.');
        } else {
            try {
                $data['contract_date'] = (new DateTimeImmutable($data['contract_date']))->format('Y-m-d');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'contract_date', 'Contract or PO date is invalid.');
            }
        }

        $normalized = str_replace(',', '', $data['contract_amount']);
        if ($data['contract_amount'] === '' || !is_numeric($normalized) || (float) $normalized < 0) {
            ValidationHelper::addError($errors, 'contract_amount', 'Contract amount is required.');
        } else {
            $data['contract_amount'] = number_format((float) $normalized, 2, '.', '');
        }

        if (!in_array($data['contract_type'], ['po', 'contract'], true)) {
            ValidationHelper::addError($errors, 'contract_type', 'Contract type is invalid.');
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validateNtpInput(array $input): array
    {
        $data = [
            'ntp_no' => trim((string) ($input['ntp_no'] ?? '')),
            'ntp_date' => trim((string) ($input['ntp_date'] ?? '')),
            'remarks' => trim((string) ($input['remarks'] ?? '')),
        ];
        $errors = [];

        if ($data['ntp_date'] === '') {
            ValidationHelper::addError($errors, 'ntp_date', 'NTP date is required.');
        } else {
            try {
                $data['ntp_date'] = (new DateTimeImmutable($data['ntp_date']))->format('Y-m-d');
            } catch (\Exception) {
                ValidationHelper::addError($errors, 'ntp_date', 'NTP date is invalid.');
            }
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function validateBeforeEvaluation(array $parent, array $workflow): array
    {
        $errors = [];
        $rfq = $workflow['rfq'] ?? null;

        if (!$rfq) {
            $errors[] = 'RFQ is required before evaluation.';
        } else {
            if (empty($rfq['issued_at'])) {
                $errors[] = 'RFQ must be issued before evaluation.';
            }
            if ((int) ($rfq['is_posting_required'] ?? 0) === 1 && !(bool) ($workflow['posting_compliance']['is_compliant'] ?? false)) {
                $errors[] = 'Posting compliance is incomplete for this SVP RFQ.';
            }

            $deadlinePassed = false;
            try {
                $deadlinePassed = !empty($parent['quotation_receipt_closed_at'])
                    || new DateTimeImmutable((string) $rfq['quotation_deadline']) < new DateTimeImmutable();
            } catch (\Exception) {
                $deadlinePassed = !empty($parent['quotation_receipt_closed_at']);
            }
            if (!$deadlinePassed) {
                $errors[] = 'Quotation receipt must be closed before evaluation.';
            }
        }

        if ((int) ($workflow['invited_supplier_count'] ?? 0) < 3) {
            $errors[] = 'At least three known qualified suppliers must be invited before evaluation.';
        }

        $quotationCount = (int) ($workflow['quotation_count'] ?? 0);
        $abc = (float) ($parent['abc'] ?? 0);
        if ($abc <= self::ABC_SINGLE_QUOTE_THRESHOLD) {
            if ($quotationCount < 1) {
                $errors[] = 'At least one quotation is required for evaluation.';
            }
        } elseif ($quotationCount < 3) {
            $errors[] = 'At least three quotations are required for this SVP amount.';
        }

        return ['allowed' => $errors === [], 'errors' => $errors];
    }

    public function validateBeforeAward(array $parent, array $workflow): array
    {
        $errors = [];
        $rfq = $workflow['rfq'] ?? null;
        $evaluation = $workflow['evaluation'] ?? null;

        if (!$rfq) {
            $errors[] = 'RFQ is required before award.';
        }
        if (!$evaluation) {
            $errors[] = 'Evaluation/abstract is required before award.';
        }
        if (empty($evaluation['recommended_supplier_id'])) {
            $errors[] = 'Recommended supplier is required before award.';
        }
        if ((int) ($workflow['quotation_count'] ?? 0) < 1) {
            $errors[] = 'At least one quotation is required before award.';
        }

        $deadlinePassed = false;
        if ($rfq) {
            try {
                $deadlinePassed = !empty($parent['quotation_receipt_closed_at'])
                    || new DateTimeImmutable((string) $rfq['quotation_deadline']) < new DateTimeImmutable();
            } catch (\Exception) {
                $deadlinePassed = !empty($parent['quotation_receipt_closed_at']);
            }
        }
        if (!$deadlinePassed) {
            $errors[] = 'Quotation deadline must have passed or quotation receipt must be manually closed before award.';
        }

        $recommendedSupplierId = (int) ($evaluation['recommended_supplier_id'] ?? 0);
        if ($recommendedSupplierId > 0 && !in_array($recommendedSupplierId, $workflow['responsive_supplier_ids'] ?? [], true)) {
            $errors[] = 'Selected supplier must be marked responsive before award.';
        }

        return ['allowed' => $errors === [], 'errors' => $errors];
    }

    public function validateBeforeContract(array $workflow): array
    {
        $errors = [];
        if (empty($workflow['award'])) {
            $errors[] = 'Award is required before contract or PO.';
        }

        return ['allowed' => $errors === [], 'errors' => $errors];
    }

    public function validateBeforeNtp(array $parent, array $workflow): array
    {
        $errors = [];
        if (empty($workflow['contract'])) {
            $errors[] = 'Contract or PO is required before NTP.';
        }
        if ((int) ($parent['is_svp_ntp_required'] ?? 0) !== 1) {
            $errors[] = 'This procurement does not require an NTP.';
        }

        return ['allowed' => $errors === [], 'errors' => $errors];
    }

    public function assertEditable(array $parent, array $workflow, bool $allowPostAward = false): void
    {
        if (!empty($parent['archived_at'])) {
            throw new DomainException('Archived records are view-only.');
        }
        if (!$allowPostAward && (!empty($workflow['award']) || !empty($workflow['contract']) || !empty($workflow['ntp']))) {
            throw new DomainException('SVP records become read-only after award or contract, except for archive and view actions.');
        }
    }

    public function saveRfq(array $parent, array $workflow, array $data, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        $model = $this->workflow ?? new SvpWorkflow();
        $payload = array_merge($data, [
            'parent_procurement_id' => (int) $parent['id'],
            'created_by' => (int) $user['id'],
            'updated_by' => (int) $user['id'],
            'issued_at' => $workflow['rfq']['issued_at'] ?? null,
        ]);

        if (!empty($workflow['rfq'])) {
            $model->updateRfq((int) $workflow['rfq']['id'], $payload);
            $rfqId = (int) $workflow['rfq']['id'];
        } else {
            $rfqId = $model->createRfq($payload);
        }

        $this->log((int) $parent['id'], (int) $user['id'], 'svp_rfq_saved', 'rfq', $rfqId, null, $payload, 'RFQ creation or update recorded.');
        $this->syncParentState($parent);
    }

    public function issueRfq(array $parent, array $workflow, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        if (empty($workflow['rfq'])) {
            throw new DomainException('Save the RFQ before issuing it.');
        }
        if (!empty($workflow['rfq']['issued_at'])) {
            throw new DomainException('RFQ has already been issued.');
        }

        $issuedAt = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        ($this->workflow ?? new SvpWorkflow())->issueRfq((int) $workflow['rfq']['id'], $issuedAt, (int) $user['id']);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_rfq_issued', 'rfq', (int) $workflow['rfq']['id'], null, ['issued_at' => $issuedAt], 'RFQ issuance recorded.');
        $this->syncParentState($parent);
    }

    public function savePosting(array $parent, array $workflow, array $data, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        if (empty($workflow['rfq'])) {
            throw new DomainException('RFQ is required before posting records can be saved.');
        }
        if ((int) ($workflow['rfq']['is_posting_required'] ?? 0) !== 1) {
            throw new DomainException('Posting records are only required when the RFQ exceeds the posting threshold.');
        }

        $postingId = ($this->workflow ?? new SvpWorkflow())->upsertPosting(array_merge($data, [
            'svp_rfq_id' => (int) $workflow['rfq']['id'],
            'created_by' => (int) $user['id'],
        ]));
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_rfq_posted', 'rfq_posting', $postingId, null, $data, 'RFQ posting saved.');
        $this->syncParentState($parent);
    }

    public function addSupplier(array $parent, array $workflow, array $data, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        $supplierId = ($this->workflow ?? new SvpWorkflow())->createSupplier([
            'parent_procurement_id' => (int) $parent['id'],
            'supplier_name' => $data['supplier_name'],
            'tin_no' => $data['tin_no'],
            'address' => $data['address'],
            'contact_person' => $data['contact_person'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'philgeps_registration_no' => $data['philgeps_registration_no'],
            'is_invited' => $data['is_invited'],
            'invited_at' => $data['is_invited'] ? (new DateTimeImmutable())->format('Y-m-d H:i:s') : null,
        ]);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_supplier_added', 'supplier', $supplierId, null, $data, 'SVP supplier recorded.');
        $this->syncParentState($parent);
    }

    public function inviteSupplier(array $parent, array $workflow, int $supplierId, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        $supplier = ($this->workflow ?? new SvpWorkflow())->findSupplierById($supplierId);
        if (!$supplier || (int) $supplier['parent_procurement_id'] !== (int) $parent['id']) {
            throw new DomainException('Supplier not found.');
        }
        if ((int) ($supplier['is_invited'] ?? 0) === 1) {
            throw new DomainException('Supplier is already marked invited.');
        }

        $invitedAt = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        ($this->workflow ?? new SvpWorkflow())->markSupplierInvited($supplierId, $invitedAt);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_supplier_invited', 'supplier', $supplierId, null, ['invited_at' => $invitedAt], 'Supplier invitation recorded.');
        $this->syncParentState($parent);
    }

    public function addQuotation(array $parent, array $workflow, array $data, array $user, ?string $attachmentPath = null): void
    {
        $this->assertEditable($parent, $workflow);
        if (empty($workflow['rfq']) || empty($workflow['rfq']['issued_at'])) {
            throw new DomainException('Issue the RFQ before encoding quotations.');
        }
        if (($this->workflow ?? new SvpWorkflow())->findQuotationByParentAndSupplier((int) $parent['id'], (int) $data['supplier_id'])) {
            throw new DomainException('Only one quotation per supplier is allowed.');
        }

        $deadline = new DateTimeImmutable((string) $workflow['rfq']['quotation_deadline']);
        $submittedAt = new DateTimeImmutable((string) $data['submission_time']);
        $quotationId = ($this->workflow ?? new SvpWorkflow())->createQuotation([
            'parent_procurement_id' => (int) $parent['id'],
            'supplier_id' => (int) $data['supplier_id'],
            'quotation_no' => $data['quotation_no'],
            'quotation_date' => $data['quotation_date'],
            'amount' => $data['amount'],
            'delivery_offer' => $data['delivery_offer'],
            'warranty_offer' => $data['warranty_offer'],
            'payment_offer' => $data['payment_offer'],
            'submission_time' => $data['submission_time'],
            'is_late' => $submittedAt > $deadline ? 1 : 0,
            'is_responsive' => $data['is_responsive'],
            'responsiveness_notes' => $data['responsiveness_notes'],
            'attachment_path' => $attachmentPath,
        ]);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_quotation_submitted', 'quotation', $quotationId, null, $data, 'Quotation submission recorded.');
        $this->syncParentState($parent);
    }

    public function setQuotationResponsiveness(array $parent, array $workflow, int $quotationId, int $isResponsive, string $notes, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        $quotation = ($this->workflow ?? new SvpWorkflow())->findQuotationById($quotationId);
        if (!$quotation || (int) $quotation['parent_procurement_id'] !== (int) $parent['id']) {
            throw new DomainException('Quotation not found.');
        }

        ($this->workflow ?? new SvpWorkflow())->updateQuotationResponsiveness($quotationId, $isResponsive, $notes);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_quotation_responsiveness_set', 'quotation', $quotationId, null, [
            'is_responsive' => $isResponsive,
            'responsiveness_notes' => $notes,
        ], 'Quotation responsiveness updated.');
        $this->syncParentState($parent);
    }

    public function closeQuotationReceipt(array $parent, array $workflow, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        if (empty($workflow['rfq'])) {
            throw new DomainException('RFQ is required before quotation receipt can be closed.');
        }
        if (!empty($parent['quotation_receipt_closed_at'])) {
            throw new DomainException('Quotation receipt has already been manually closed.');
        }

        ($this->parents ?? new ParentProcurement())->updateOperationalState(
            (int) $parent['id'],
            $parent['category'] ?? null,
            $parent['end_user_unit'] ?? null,
            (int) ($parent['is_svp_ntp_required'] ?? 0),
            (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            $parent['completed_at'] ?? null,
            (int) $user['id']
        );
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_quotation_closed', 'rfq', (int) ($workflow['rfq']['id'] ?? 0), null, ['quotation_receipt_closed_at' => true], 'Quotation receipt manually closed.');
        $this->syncParentState($parent);
    }

    public function saveEvaluation(array $parent, array $workflow, array $data, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        $guard = $this->validateBeforeEvaluation($parent, $workflow);
        if (!$guard['allowed']) {
            throw new DomainException($guard['errors'][0]);
        }

        $quotations = $workflow['quotations'];
        usort($quotations, static fn (array $left, array $right): int => ((float) $left['amount'] <=> (float) $right['amount']) ?: strcmp((string) $left['submission_time'], (string) $right['submission_time']));

        $recommendedAmount = null;
        foreach ($quotations as $quote) {
            if ((int) $quote['supplier_id'] === (int) $data['recommended_supplier_id']) {
                $recommendedAmount = $quote['amount'];
                break;
            }
        }

        $payload = [
            'parent_procurement_id' => (int) $parent['id'],
            'evaluation_date' => $data['evaluation_date'],
            'quotation_count' => count($quotations),
            'is_posting_compliant' => ($workflow['posting_compliance']['is_required'] ?? false) ? (int) ($workflow['posting_compliance']['is_compliant'] ?? false) : 1,
            'is_supplier_invitation_compliant' => (int) ((int) ($workflow['invited_supplier_count'] ?? 0) >= 3),
            'exception_note' => $data['exception_note'],
            'recommended_supplier_id' => (int) $data['recommended_supplier_id'],
            'recommended_amount' => $recommendedAmount,
            'recommendation_text' => $data['recommendation_text'],
            'approved_by' => (int) $user['id'],
            'created_by' => (int) $user['id'],
        ];

        $model = $this->workflow ?? new SvpWorkflow();
        if (!empty($workflow['evaluation'])) {
            $evaluationId = (int) $workflow['evaluation']['id'];
            $model->updateEvaluation($evaluationId, $payload);
            $model->deleteEvaluationItems($evaluationId);
        } else {
            $evaluationId = $model->createEvaluation($payload);
        }

        $rank = 1;
        foreach ($quotations as $quote) {
            $model->createEvaluationItem([
                'evaluation_id' => $evaluationId,
                'quotation_id' => (int) $quote['id'],
                'rank_no' => $rank++,
                'quoted_amount' => $quote['amount'],
                'is_calculated' => 1,
                'is_responsive' => (int) ($quote['is_responsive'] ?? 0),
                'remarks' => $quote['responsiveness_notes'] ?? null,
            ]);
        }

        $this->log((int) $parent['id'], (int) $user['id'], 'svp_evaluation_completed', 'evaluation', $evaluationId, null, $payload, 'SVP evaluation completed.');
        $this->syncParentState($parent);
    }

    public function createAward(array $parent, array $workflow, array $data, array $user): void
    {
        $this->assertEditable($parent, $workflow);
        if (!empty($workflow['award'])) {
            throw new DomainException('Award has already been recorded for this procurement.');
        }

        $guard = $this->validateBeforeAward($parent, $workflow);
        if (!$guard['allowed']) {
            throw new DomainException($guard['errors'][0]);
        }

        $recommendedSupplierId = (int) ($workflow['evaluation']['recommended_supplier_id'] ?? 0);
        if ($recommendedSupplierId !== (int) $data['supplier_id']) {
            throw new DomainException('Awarded supplier must match the recommended supplier in the evaluation.');
        }

        $awardId = ($this->workflow ?? new SvpWorkflow())->createAward([
            'parent_procurement_id' => (int) $parent['id'],
            'supplier_id' => (int) $data['supplier_id'],
            'award_no' => $data['award_no'],
            'award_date' => $data['award_date'],
            'award_amount' => $data['award_amount'],
            'award_type' => $data['award_type'],
            'remarks' => $data['remarks'],
            'created_by' => (int) $user['id'],
        ]);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_award_issued', 'award', $awardId, null, $data, 'SVP award issued.');
        $this->syncParentState($parent);
    }

    public function createContract(array $parent, array $workflow, array $data, array $user, ?string $filePath = null): void
    {
        $this->assertEditable($parent, $workflow, true);
        if (!empty($workflow['contract'])) {
            throw new DomainException('Contract or PO has already been recorded.');
        }
        $guard = $this->validateBeforeContract($workflow);
        if (!$guard['allowed']) {
            throw new DomainException($guard['errors'][0]);
        }

        $contractId = ($this->workflow ?? new SvpWorkflow())->createContract([
            'parent_procurement_id' => (int) $parent['id'],
            'award_id' => (int) $workflow['award']['id'],
            'contract_no' => $data['contract_no'],
            'contract_date' => $data['contract_date'],
            'contract_amount' => $data['contract_amount'],
            'contract_type' => $data['contract_type'],
            'file_path' => $filePath,
            'created_by' => (int) $user['id'],
        ]);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_contract_created', 'contract', $contractId, null, $data, 'Contract or PO created.');
        $this->syncParentState($parent);
    }

    public function createNtp(array $parent, array $workflow, array $data, array $user): void
    {
        $this->assertEditable($parent, $workflow, true);
        if (!empty($workflow['ntp'])) {
            throw new DomainException('NTP has already been recorded.');
        }
        $guard = $this->validateBeforeNtp($parent, $workflow);
        if (!$guard['allowed']) {
            throw new DomainException($guard['errors'][0]);
        }

        $ntpId = ($this->workflow ?? new SvpWorkflow())->createNtp([
            'parent_procurement_id' => (int) $parent['id'],
            'contract_id' => (int) $workflow['contract']['id'],
            'ntp_no' => $data['ntp_no'],
            'ntp_date' => $data['ntp_date'],
            'remarks' => $data['remarks'],
            'created_by' => (int) $user['id'],
        ]);
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_ntp_issued', 'ntp', $ntpId, null, $data, 'NTP issuance recorded.');
        $this->syncParentState($parent);
    }

    public function markCompleted(array $parent, array $workflow, array $user): void
    {
        if (!empty($parent['archived_at'])) {
            throw new DomainException('Archived records are view-only.');
        }
        if (empty($workflow['contract']) && empty($workflow['ntp'])) {
            throw new DomainException('Complete the contract or NTP stage before marking this procurement completed.');
        }

        ($this->parents ?? new ParentProcurement())->updateOperationalState(
            (int) $parent['id'],
            $parent['category'] ?? null,
            $parent['end_user_unit'] ?? null,
            (int) ($parent['is_svp_ntp_required'] ?? 0),
            $parent['quotation_receipt_closed_at'] ?? null,
            (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            (int) $user['id']
        );
        $this->log((int) $parent['id'], (int) $user['id'], 'svp_completed', 'procurement', null, null, ['completed' => true], 'SVP procurement marked completed.');
        $this->syncParentState($parent);
    }

    private function syncParentState(array $parent): void
    {
        $workflow = $this->buildWorkflow((int) $parent['id']);
        $stage = $this->computeStage($parent, $workflow);
        $status = $this->computePostingStatus($parent, $workflow);
        ($this->parents ?? new ParentProcurement())->updateWorkflowAndPostingState((int) $parent['id'], $stage, $status);
    }

    private function log(int $parentId, int $userId, string $actionType, string $documentType, ?int $documentId, mixed $before, mixed $after, string $reason): void
    {
        ($this->activityLogs ?? new ProcurementActivityLog())->create([
            'parent_procurement_id' => $parentId,
            'user_id' => $userId,
            'action_type' => $actionType,
            'document_type' => $documentType,
            'document_id' => $documentId,
            'before_snapshot' => $before !== null ? json_encode($before, JSON_UNESCAPED_SLASHES) : null,
            'after_snapshot' => $after !== null ? json_encode($after, JSON_UNESCAPED_SLASHES) : null,
            'reason' => $reason,
            'file_hash' => null,
            'approval_reference' => null,
        ]);
    }
}
