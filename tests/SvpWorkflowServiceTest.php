<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';

use App\Services\SvpWorkflowService;

function assertSameSvp(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ' Expected `' . var_export($expected, true) . '`, got `' . var_export($actual, true) . '`.');
    }
}

function assertTrueSvp(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$service = new SvpWorkflowService();

$draftParent = ['id' => 1, 'abc' => 500000.00, 'archived_at' => null, 'completed_at' => null, 'quotation_receipt_closed_at' => null];
assertSameSvp(SvpWorkflowService::STAGE_DRAFT, $service->computeStage($draftParent, ['rfq' => null, 'quotation_count' => 0]), 'SVP without RFQ should remain in draft.');

$rfqPrepared = [
    'rfq' => [
        'quotation_deadline' => '2026-04-20 17:00:00',
        'issued_at' => null,
    ],
    'quotation_count' => 0,
    'evaluation' => null,
    'award' => null,
    'contract' => null,
    'ntp' => null,
];
assertSameSvp(SvpWorkflowService::STAGE_RFQ_PREPARED, $service->computeStage($draftParent, $rfqPrepared), 'Saved but unissued RFQ should be RFQ prepared.');

$quotationOpen = $rfqPrepared;
$quotationOpen['rfq']['issued_at'] = '2026-04-10 09:00:00';
assertSameSvp(
    SvpWorkflowService::STAGE_QUOTATION_OPEN,
    $service->computeStage($draftParent, $quotationOpen, new DateTimeImmutable('2026-04-15 09:00:00')),
    'Issued RFQ before deadline should be quotation open.'
);

$evaluationWorkflow = $quotationOpen;
$evaluationWorkflow['quotation_count'] = 1;
assertSameSvp(
    SvpWorkflowService::STAGE_UNDER_EVALUATION,
    $service->computeStage($draftParent, $evaluationWorkflow, new DateTimeImmutable('2026-04-21 09:00:00')),
    'Closed quotation period with quotations should be under evaluation.'
);

$postingGuard = $service->validateBeforeEvaluation(
    ['abc' => 450000.00, 'quotation_receipt_closed_at' => '2026-04-06 17:00:00'],
    [
        'rfq' => ['issued_at' => '2026-04-01 09:00:00', 'quotation_deadline' => '2026-04-05 17:00:00', 'is_posting_required' => 1],
        'posting_compliance' => ['is_compliant' => false],
        'invited_supplier_count' => 3,
        'quotation_count' => 1,
    ]
);
assertTrueSvp(!$postingGuard['allowed'], 'Evaluation must fail when posting compliance is incomplete.');
assertTrueSvp(in_array('Posting compliance is incomplete for this SVP RFQ.', $postingGuard['errors'], true), 'Posting compliance error should be reported.');

$singleQuoteAllowed = $service->validateBeforeEvaluation(
    ['abc' => 1900000.00, 'quotation_receipt_closed_at' => '2026-04-06 17:00:00'],
    [
        'rfq' => ['issued_at' => '2026-04-01 09:00:00', 'quotation_deadline' => '2026-04-05 17:00:00', 'is_posting_required' => 0],
        'posting_compliance' => ['is_compliant' => true],
        'invited_supplier_count' => 3,
        'quotation_count' => 1,
    ]
);
assertTrueSvp($singleQuoteAllowed['allowed'], 'One quotation should be enough when ABC does not exceed PHP 2,000,000.');

$threeQuoteRequired = $service->validateBeforeEvaluation(
    ['abc' => 2500000.00, 'quotation_receipt_closed_at' => '2026-04-06 17:00:00'],
    [
        'rfq' => ['issued_at' => '2026-04-01 09:00:00', 'quotation_deadline' => '2026-04-05 17:00:00', 'is_posting_required' => 1],
        'posting_compliance' => ['is_compliant' => true],
        'invited_supplier_count' => 3,
        'quotation_count' => 2,
    ]
);
assertTrueSvp(!$threeQuoteRequired['allowed'], 'Amounts above PHP 2,000,000 should require at least three quotations.');

$awardGuard = $service->validateBeforeAward(
    ['quotation_receipt_closed_at' => '2026-04-06 17:00:00'],
    [
        'rfq' => ['quotation_deadline' => '2026-04-05 17:00:00'],
        'evaluation' => ['recommended_supplier_id' => 9],
        'quotation_count' => 1,
        'responsive_supplier_ids' => [3, 4],
    ]
);
assertTrueSvp(!$awardGuard['allowed'], 'Award must fail when recommended supplier is not responsive.');
assertTrueSvp(in_array('Selected supplier must be marked responsive before award.', $awardGuard['errors'], true), 'Responsive supplier guard should be enforced.');

$contractGuard = $service->validateBeforeContract(['award' => null]);
assertTrueSvp(!$contractGuard['allowed'], 'Contract must fail when no award exists.');

echo "SvpWorkflowService tests passed.\n";
