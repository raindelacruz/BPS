<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';

use App\Models\ProcurementDocument;
use App\Services\ProcurementPostingService;

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

$service = new ProcurementPostingService();
$now = new DateTimeImmutable('2026-04-09 09:00:00');

assertSameValue(
    ProcurementPostingService::POSTING_STATUS_SCHEDULED,
    $service->determinePostingStatus([
        'posting_date' => '2026-04-10 09:00:00',
        'bid_submission_deadline' => '2026-04-12 09:00:00',
        'archived_at' => null,
    ], $now),
    'Future procurement should be scheduled.'
);

assertSameValue(
    ProcurementPostingService::POSTING_STATUS_OPEN,
    $service->determinePostingStatus([
        'posting_date' => '2026-04-08 09:00:00',
        'bid_submission_deadline' => '2026-04-10 09:00:00',
        'archived_at' => null,
    ], $now),
    'Procurement inside the posting window should be open.'
);

assertSameValue(
    ProcurementPostingService::POSTING_STATUS_CLOSED,
    $service->determinePostingStatus([
        'posting_date' => '2026-04-01 09:00:00',
        'bid_submission_deadline' => '2026-04-08 08:59:59',
        'archived_at' => null,
    ], $now),
    'Deadline-passed procurement must be closed.'
);

assertSameValue(
    ProcurementPostingService::POSTING_STATUS_ARCHIVED,
    $service->determinePostingStatus([
        'posting_date' => '2026-04-01 09:00:00',
        'bid_submission_deadline' => '2026-04-10 09:00:00',
        'archived_at' => '2026-04-11 10:00:00',
    ], $now),
    'Archived procurement must remain archived regardless of dates.'
);

$openParent = [
    'id' => 501,
    'posting_date' => '2026-04-01 09:00:00',
    'bid_submission_deadline' => '2026-04-15 09:00:00',
    'posting_status' => ProcurementPostingService::POSTING_STATUS_OPEN,
    'archived_at' => null,
];

$closedParent = [
    'id' => 502,
    'posting_date' => '2026-04-01 09:00:00',
    'bid_submission_deadline' => '2026-04-08 09:00:00',
    'posting_status' => ProcurementPostingService::POSTING_STATUS_CLOSED,
    'archived_at' => null,
];

$baseDocuments = [
    ProcurementDocument::TYPE_BID_NOTICE => [['id' => 1, 'posted_at' => '2026-04-01 09:00:00']],
    ProcurementDocument::TYPE_SBB => [],
    ProcurementDocument::TYPE_RESOLUTION => [],
    ProcurementDocument::TYPE_AWARD => [],
    ProcurementDocument::TYPE_CONTRACT => [],
    ProcurementDocument::TYPE_NOTICE_TO_PROCEED => [],
];

$resolutionWhileOpen = $service->canCreateDocument(
    ProcurementDocument::TYPE_RESOLUTION,
    $openParent,
    $baseDocuments,
    '2026-04-10 10:00:00'
);
assertTrueValue(!$resolutionWhileOpen['allowed'], 'Resolution while bidding is open must be rejected.');
assertTrueValue(
    in_array('Resolution may only be posted after bidding has closed.', $resolutionWhileOpen['errors'], true),
    'Resolution validation must report that bidding is still open.'
);

$lateSbb = $service->canCreateDocument(
    ProcurementDocument::TYPE_SBB,
    $openParent,
    $baseDocuments,
    '2026-04-16 10:00:00'
);
assertTrueValue(!$lateSbb['allowed'], 'Supplemental/Bid Bulletin after the deadline must be rejected.');
assertTrueValue(
    in_array('Supplemental/Bid Bulletin date must be on or before the bid submission deadline.', $lateSbb['errors'], true),
    'SBB validation must enforce the deadline chronology.'
);

$resolutionDocuments = $baseDocuments;
$resolutionDocuments[ProcurementDocument::TYPE_RESOLUTION] = [['id' => 2, 'posted_at' => '2026-04-09 10:00:00']];
$awardBeforeResolution = $service->canCreateDocument(
    ProcurementDocument::TYPE_AWARD,
    $closedParent,
    $resolutionDocuments,
    '2026-04-09 09:00:00'
);
assertTrueValue(!$awardBeforeResolution['allowed'], 'Award before the resolution timestamp must be rejected.');
assertTrueValue(
    in_array('Award date must be on or after the Resolution date.', $awardBeforeResolution['errors'], true),
    'Award validation must enforce chronology relative to the resolution.'
);

$contractWithoutAward = $service->canCreateDocument(
    ProcurementDocument::TYPE_CONTRACT,
    $closedParent,
    $resolutionDocuments,
    '2026-04-10 10:00:00'
);
assertTrueValue(!$contractWithoutAward['allowed'], 'Contract before Award must be rejected.');
assertTrueValue(
    in_array('Notice of Award / Award must be posted before Contract.', $contractWithoutAward['errors'], true),
    'Contract validation must report the missing Award prerequisite.'
);

$multipleSbbs = $service->canCreateDocument(
    ProcurementDocument::TYPE_SBB,
    $openParent,
    array_merge($baseDocuments, [
        ProcurementDocument::TYPE_SBB => [
            ['id' => 2, 'posted_at' => '2026-04-02 09:00:00'],
            ['id' => 3, 'posted_at' => '2026-04-03 09:00:00'],
        ],
    ]),
    '2026-04-05 09:00:00'
);
assertTrueValue($multipleSbbs['allowed'], 'Multiple Supplemental/Bid Bulletins before deadline should be allowed.');

assertSameValue(
    ProcurementDocument::TYPE_NOTICE_TO_PROCEED,
    $service->currentStage([
        ProcurementDocument::TYPE_BID_NOTICE => [['id' => 1]],
        ProcurementDocument::TYPE_SBB => [['id' => 2]],
        ProcurementDocument::TYPE_RESOLUTION => [['id' => 3]],
        ProcurementDocument::TYPE_AWARD => [['id' => 4]],
        ProcurementDocument::TYPE_CONTRACT => [['id' => 5]],
        ProcurementDocument::TYPE_NOTICE_TO_PROCEED => [['id' => 6]],
    ]),
    'Notice to Proceed should be the terminal workflow stage.'
);

$archiveGuard = $service->canArchive(
    $closedParent,
    $resolutionDocuments,
    ['id' => 2, 'role' => 'admin'],
    ['archive_reason' => 'Test archive', 'archive_approval_reference' => 'ADM-001']
);
assertTrueValue(!$archiveGuard['allowed'], 'Archiving without Notice to Proceed must be rejected.');
assertTrueValue(
    in_array('Archive is allowed only after Notice to Proceed has been posted.', $archiveGuard['errors'], true),
    'Archive validation must require Notice to Proceed.'
);

echo "ProcurementPostingService tests passed.\n";
