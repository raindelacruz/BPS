<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;

$postingStatusLabels = [
    'scheduled' => 'Scheduled',
    'open' => 'Open for Bids',
    'closed' => 'Closed for Bids',
    'archived' => 'Archived',
];

$workflowStageLabels = [
    'bid_notice' => 'Bid Notice / Invitation to Bid',
    'supplemental_bid_bulletin' => 'Supplemental/Bid Bulletin',
    'resolution' => 'Resolution',
    'award' => 'Notice of Award / Award',
    'contract' => 'Contract',
    'notice_to_proceed' => 'Notice to Proceed',
];

$postingStatus = (string) ($parent['posting_status'] ?? 'scheduled');
$workflowStage = (string) ($parent['current_stage'] ?? 'bid_notice');
$isAdmin = ($currentUser['role'] ?? null) === 'admin';
$isArchived = $postingStatus === 'archived';

$bidNotice = $documents[ProcurementDocument::TYPE_BID_NOTICE][0] ?? null;
$resolution = $documents[ProcurementDocument::TYPE_RESOLUTION][0] ?? null;
$award = $documents[ProcurementDocument::TYPE_AWARD][0] ?? null;
$contract = $documents[ProcurementDocument::TYPE_CONTRACT][0] ?? null;
$noticeToProceed = $documents[ProcurementDocument::TYPE_NOTICE_TO_PROCEED][0] ?? null;
$sbbs = $documents[ProcurementDocument::TYPE_SBB] ?? [];
$documentRows = [];

    $documentRows[] = [
        'label' => ProcurementDocument::label(ProcurementDocument::TYPE_BID_NOTICE),
        'status' => $bidNotice ? 'Posted' : 'Not yet posted',
        'posted_at' => $bidNotice['posted_at'] ?? null,
        'file_url' => $bidNotice ? ResponseHelper::url('notices/' . (int) $parent['id'] . '/file') : null,
    ];

foreach ($sbbs as $sbb) {
    $documentRows[] = [
        'label' => ProcurementDocument::label(ProcurementDocument::TYPE_SBB),
        'status' => 'Posted',
        'posted_at' => $sbb['posted_at'] ?? null,
        'file_url' => ResponseHelper::url('documents/' . ProcurementDocument::TYPE_SBB . '/' . (int) $sbb['id'] . '/file'),
    ];
}

foreach ([
    ProcurementDocument::TYPE_RESOLUTION => $resolution,
    ProcurementDocument::TYPE_AWARD => $award,
    ProcurementDocument::TYPE_CONTRACT => $contract,
    ProcurementDocument::TYPE_NOTICE_TO_PROCEED => $noticeToProceed,
] as $type => $document) {
    $documentRows[] = [
        'label' => ProcurementDocument::label($type),
        'status' => $document ? 'Posted' : 'Not yet posted',
        'posted_at' => $document['posted_at'] ?? null,
        'file_url' => $document ? ResponseHelper::url('documents/' . $type . '/' . (int) $document['id'] . '/file') : null,
    ];
}
?>
<div class="page-head">
    <div>
        <h1><?= ViewHelper::escape($parent['procurement_title']); ?></h1>
        <p>Official procurement record for reference <?= ViewHelper::escape($parent['reference_number']); ?>.</p>
    </div>
    <div class="action-row">
        <span class="status-badge <?= ViewHelper::escape($postingStatus); ?>"><?= ViewHelper::escape($postingStatusLabels[$postingStatus] ?? ucfirst($postingStatus)); ?></span>
        <span class="status-badge"><?= ViewHelper::escape($workflowStageLabels[$workflowStage] ?? ucwords(str_replace('_', ' ', $workflowStage))); ?></span>
    </div>
</div>

<dl class="detail-grid">
    <div>
        <dt>APPROVED BUDGET FOR THE CONTRACT</dt>
        <dd><?= ViewHelper::escape(number_format((float) ($parent['abc'] ?? 0), 2)); ?></dd>
    </div>
    <div>
        <dt>Mode of procurement</dt>
        <dd><?= ViewHelper::escape(ProcurementTypeHelper::label((string) ($parent['mode_of_procurement'] ?? ''))); ?></dd>
    </div>
    <div>
        <dt>Bid Notice posting date</dt>
        <dd><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $parent['posting_date']))); ?></dd>
    </div>
    <div>
        <dt>Bid submission deadline</dt>
        <dd><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $parent['bid_submission_deadline']))); ?></dd>
    </div>
    <div>
        <dt>Public availability</dt>
        <dd>Official public posting</dd>
    </div>
</dl>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Posting Actions</h2>
            <p><?= ViewHelper::escape($isArchived ? 'Archived records are immutable and remain part of the public historical record.' : 'Bid availability is calculated from the legal posting and deadline dates shown here.'); ?></p>
        </div>
        <?php if ($isAdmin && !$isArchived): ?>
            <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $parent['id'] . '/archive')); ?>" class="inline-form">
                <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                <input type="text" name="archive_reason" placeholder="Archive reason" required>
                <input type="text" name="archive_approval_reference" placeholder="Approval reference" required>
                <button type="submit">Archive Record</button>
            </form>
        <?php endif; ?>
    </div>
    <div class="action-row">
        <?php foreach ([ProcurementDocument::TYPE_SBB, ProcurementDocument::TYPE_RESOLUTION, ProcurementDocument::TYPE_AWARD, ProcurementDocument::TYPE_CONTRACT, ProcurementDocument::TYPE_NOTICE_TO_PROCEED] as $type): ?>
            <?php $action = $actions[$type] ?? ['allowed' => false]; ?>
            <?php if ($action['allowed'] && !$isArchived): ?>
                <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=' . $type . '&parent_id=' . (int) $parent['id'])); ?>">
                    <?= ViewHelper::escape($type === ProcurementDocument::TYPE_SBB ? 'Add Supplemental/Bid Bulletin' : 'Add ' . ProcurementDocument::label($type)); ?>
                </a>
            <?php else: ?>
                <span class="chip-link is-disabled">
                    <?= ViewHelper::escape($type === ProcurementDocument::TYPE_SBB ? 'Add Supplemental/Bid Bulletin' : 'Add ' . ProcurementDocument::label($type)); ?>
                </span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Documents</h2>
        </div>
    </div>
    <p class="muted" style="margin: -2px 0 4px;">All posted records are read-only.</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Status</th>
                    <th>Date Posted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documentRows as $row): ?>
                    <tr>
                        <td><?= ViewHelper::escape($row['label']); ?></td>
                        <td><?= ViewHelper::escape($row['status']); ?></td>
                        <td><?= ViewHelper::escape($row['posted_at'] ? date('Y-m-d H:i', strtotime((string) $row['posted_at'])) : ''); ?></td>
                        <td>
                            <?php if ($row['file_url']): ?>
                                <div class="action-row">
                                    <a class="btn-link" href="<?= ViewHelper::escape($row['file_url']); ?>" target="_blank" rel="noopener">PDF</a>
                                </div>
                            <?php else: ?>
                                <span class="muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($isArchived): ?>
    <div class="card-section stack-sm">
        <div class="page-head">
            <div>
                <h2>Archive Metadata</h2>
            </div>
        </div>
        <dl class="detail-grid">
            <div>
                <dt>Archived at</dt>
                <dd><?= ViewHelper::escape(!empty($parent['archived_at']) ? date('Y-m-d H:i', strtotime((string) $parent['archived_at'])) : ''); ?></dd>
            </div>
            <div>
                <dt>Archive reason</dt>
                <dd><?= ViewHelper::escape((string) ($parent['archive_reason'] ?? '')); ?></dd>
            </div>
            <div>
                <dt>Approval reference</dt>
                <dd><?= ViewHelper::escape((string) ($parent['archive_approval_reference'] ?? '')); ?></dd>
            </div>
        </dl>
    </div>
<?php endif; ?>
