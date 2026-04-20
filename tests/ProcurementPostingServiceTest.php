<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';

use App\Models\ParentProcurement;
use App\Models\ProcurementDocument;
use App\Models\ProcurementActivityLog;
use App\Services\FileUploadService;
use App\Services\ProcurementPostingService;
use App\Services\SmallValueProcurementService;

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ' Expected `' . var_export($expected, true) . '`, got `' . var_export($actual, true) . '`.');
    }
}

function assertTrueValue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$parentStub = new class extends ParentProcurement {
    public function referenceNumberExists(string $referenceNumber, ?int $ignoreId = null): bool
    {
        return false;
    }
};

$service = new ProcurementPostingService($parentStub);
$now = new DateTimeImmutable('2026-04-09 09:00:00');

$competitiveValidation = $service->validateCompetitiveBiddingInput([
    'procurement_title' => 'Rice Buffer Stocking',
    'reference_number' => 'CB-2026-001',
    'abc' => '1250000',
    'category' => 'goods',
    'end_user_unit' => 'Operations',
    'posting_date' => '2026-04-10 09:00',
    'bid_submission_deadline' => '2026-04-15 17:00',
    'description' => 'Competitive bidding notice',
]);
assertSameValue(ProcurementPostingService::COMPETITIVE_BIDDING_MODE, $competitiveValidation['data']['procurement_mode'], 'Competitive Bidding validation should lock the mode.');

$svpValidation = $service->validateSvpInput([
    'procurement_title' => 'Office Chairs',
    'reference_number' => 'SVP-2026-001',
    'abc' => '95000',
    'category' => 'goods',
    'end_user_unit' => 'Admin Division',
    'description' => 'SVP purchase',
]);
assertSameValue(SmallValueProcurementService::MODE, $svpValidation['data']['procurement_mode'], 'SVP validation should lock the SVP mode.');
assertSameValue(null, $svpValidation['data']['posting_date'], 'SVP validation should not keep bidding posting dates.');
assertSameValue(null, $svpValidation['data']['bid_submission_deadline'], 'SVP validation should not keep bidding deadlines.');

$relatedValidation = $service->validateDocumentInput([
    'type' => ProcurementDocument::TYPE_CANVASS,
    'parent_procurement_id' => 700,
    'title' => 'Canvass - SVP-2026-001',
    'posted_at' => '2026-04-10 09:00',
    'description' => 'Canvass document',
]);
assertSameValue([], $relatedValidation['errors'], 'Shared related-document validation should accept SVP canvass documents.');

assertSameValue(
    ProcurementPostingService::POSTING_STATUS_SCHEDULED,
    $service->determinePostingStatus([
        'procurement_mode' => ProcurementPostingService::COMPETITIVE_BIDDING_MODE,
        'posting_date' => '2026-04-10 09:00:00',
        'bid_submission_deadline' => '2026-04-12 09:00:00',
        'archived_at' => null,
    ], $now),
    'Future competitive procurement should be scheduled.'
);

$svpParent = [
    'id' => 700,
    'procurement_mode' => SmallValueProcurementService::MODE,
    'archived_at' => null,
    'posting_status' => ProcurementPostingService::POSTING_STATUS_SCHEDULED,
    'bid_submission_deadline' => '2026-04-05 17:00:00',
];

$svpDocs = [
    ProcurementDocument::TYPE_RFQ => [],
    ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS => [],
    ProcurementDocument::TYPE_CANVASS => [],
    ProcurementDocument::TYPE_AWARD => [],
    ProcurementDocument::TYPE_CONTRACT_OR_PO => [],
];

assertSameValue(
    ProcurementDocument::TYPE_RFQ,
    $service->currentStage($svpDocs, SmallValueProcurementService::MODE),
    'SVP without documents should start at RFQ.'
);

$duplicateRfq = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_RFQ,
    $svpParent,
    array_merge($svpDocs, [
        ProcurementDocument::TYPE_RFQ => [['id' => 1, 'posted_at' => '2026-04-01 09:00:00']],
    ]),
    '2026-04-02 09:00:00'
);
assertTrueValue(!$duplicateRfq['allowed'], 'Duplicate RFQ must fail.');
assertTrueValue(in_array('Only one RFQ is allowed per SVP procurement.', $duplicateRfq['errors'], true), 'Duplicate RFQ error should be reported.');

$abstractWithoutRfq = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS,
    $svpParent,
    $svpDocs,
    '2026-04-02 09:00:00'
);
assertTrueValue(!$abstractWithoutRfq['allowed'], 'Posting without RFQ must fail.');
assertTrueValue(in_array('RFQ must be posted first.', $abstractWithoutRfq['errors'], true), 'Missing RFQ prerequisite should be reported.');

$rfqOnly = array_merge($svpDocs, [
    ProcurementDocument::TYPE_RFQ => [['id' => 10, 'posted_at' => '2026-04-05 09:00:00']],
]);
$earlyAbstract = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS,
    $svpParent,
    $rfqOnly,
    '2026-04-04 08:59:59'
);
assertTrueValue(!$earlyAbstract['allowed'], 'Abstract before RFQ date must fail.');
assertTrueValue(in_array('Abstract of Quotations date must be on or after the RFQ date.', $earlyAbstract['errors'], true), 'Abstract chronology must be enforced.');

$canvassAfterAbstract = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_CANVASS,
    $svpParent,
    array_merge($rfqOnly, [
        ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS => [['id' => 11, 'posted_at' => '2026-04-06 10:00:00']],
    ]),
    '2026-04-06 11:00:00'
);
assertTrueValue(!$canvassAfterAbstract['allowed'], 'Canvass must fail once Abstract exists.');
assertTrueValue(in_array('Canvass cannot be posted after an Abstract of Quotations has already been posted.', $canvassAfterAbstract['errors'], true), 'Abstract/Canvass exclusivity should be enforced.');

$awardWithoutBasis = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_AWARD,
    $svpParent,
    $rfqOnly,
    '2026-04-06 09:00:00'
);
assertTrueValue(!$awardWithoutBasis['allowed'], 'Award without abstract/canvass must fail.');
assertTrueValue(in_array('Award requires a posted Abstract of Quotations or Canvass.', $awardWithoutBasis['errors'], true), 'Award prerequisite should be enforced.');

$abstractPosted = array_merge($rfqOnly, [
    ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS => [['id' => 11, 'posted_at' => '2026-04-06 10:00:00']],
]);
$closedSvpParent = array_merge($svpParent, ['posting_status' => ProcurementPostingService::POSTING_STATUS_CLOSED]);
$awardAllowed = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_AWARD,
    $closedSvpParent,
    $abstractPosted,
    '2026-04-07 09:00:00'
);
assertTrueValue($awardAllowed['allowed'], 'Award should be allowed after RFQ closes and abstract exists.');

$contractWithoutAward = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_CONTRACT_OR_PO,
    $closedSvpParent,
    $abstractPosted,
    '2026-04-07 09:00:00'
);
assertTrueValue(!$contractWithoutAward['allowed'], 'Contract / PO without award must fail.');
assertTrueValue(in_array('Contract or Purchase Order requires a posted Award.', $contractWithoutAward['errors'], true), 'Contract / PO prerequisite should be enforced.');

$archivedSvpParent = [
    'id' => 701,
    'procurement_mode' => SmallValueProcurementService::MODE,
    'archived_at' => '2026-04-09 12:00:00',
    'posting_status' => ProcurementPostingService::POSTING_STATUS_ARCHIVED,
    'bid_submission_deadline' => '2026-04-05 17:00:00',
];
$immutableAfterPosting = $service->canCreateSvpDocument(
    ProcurementDocument::TYPE_AWARD,
    $archivedSvpParent,
    $abstractPosted,
    '2026-04-07 10:00:00'
);
assertTrueValue(!$immutableAfterPosting['allowed'], 'Posting after archive must fail.');
assertTrueValue(in_array('Archived SVP records are immutable and cannot accept new postings.', $immutableAfterPosting['errors'], true), 'SVP immutability must be enforced.');

$missingFileParentStub = new class extends ParentProcurement {
    public function findById(int $id): ?array
    {
        return [
            'id' => $id,
            'procurement_mode' => ProcurementPostingService::COMPETITIVE_BIDDING_MODE,
            'mode_of_procurement' => ProcurementPostingService::COMPETITIVE_BIDDING_MODE,
            'procurement_title' => 'Missing Attachment Test',
            'reference_number' => 'CB-2026-MISSING',
            'posting_date' => '2026-04-01 09:00:00',
            'bid_submission_deadline' => '2026-04-20 17:00:00',
            'description' => 'Bid notice row exists but file is missing.',
            'posting_status' => ProcurementPostingService::POSTING_STATUS_OPEN,
            'current_stage' => ProcurementDocument::TYPE_BID_NOTICE,
            'archived_at' => null,
            'branch' => 'Central',
            'created_by' => 1,
        ];
    }

    public function updateWorkflowAndPostingState(int $id, string $stage, string $postingStatus): bool
    {
        return true;
    }
};

$missingFileDocumentStub = new class extends ProcurementDocument {
    public function findForParent(string $type, int $parentId): array
    {
        if ($type !== ProcurementDocument::TYPE_BID_NOTICE) {
            return [];
        }

        return [[
            'id' => 900,
            'parent_procurement_id' => $parentId,
            'title' => 'Bid Notice',
            'file_path' => 'storage/uploads/notices/missing.pdf',
            'posted_at' => '2026-04-01 09:00:00',
            'sequence_stage' => 1,
        ]];
    }
};

$missingFileActivityStub = new class extends ProcurementActivityLog {
    public function findByParent(int $parentId): array
    {
        return [];
    }
};

$missingFileUploadStub = new class extends FileUploadService {
    public function exists(?string $relativePath): bool
    {
        return false;
    }
};

$missingFileService = new ProcurementPostingService(
    $missingFileParentStub,
    $missingFileDocumentStub,
    $missingFileActivityStub,
    $missingFileUploadStub
);

$missingFileWorkflow = $missingFileService->findParentWithWorkflow(900);
assertTrueValue($missingFileWorkflow !== null, 'Workflow should load for the missing-file test parent.');
assertSameValue([], $missingFileWorkflow['documents'][ProcurementDocument::TYPE_BID_NOTICE], 'Documents with missing files must not count as posted.');
assertTrueValue(!$missingFileWorkflow['actions'][ProcurementDocument::TYPE_SBB]['allowed'], 'Downstream posting must be blocked when the root file is missing.');
assertTrueValue(in_array('Bid Notice must be posted first.', $missingFileWorkflow['actions'][ProcurementDocument::TYPE_SBB]['errors'], true), 'Missing root file should behave like an unposted Bid Notice.');

echo "ProcurementPostingService tests passed.\n";
