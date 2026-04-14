<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;

$workflowStageLabels = [
    'bid_notice' => 'Bid Notice / Invitation to Bid',
    'rfq' => 'Request for Quotation',
    'abstract_of_quotations' => 'Abstract of Quotations',
    'canvass' => 'Canvass',
    'supplemental_bid_bulletin' => 'Supplemental/Bid Bulletin',
    'resolution' => 'Resolution',
    'award' => 'Notice of Award / Award',
    'contract' => 'Contract',
    'contract_or_purchase_order' => 'Contract / Purchase Order',
];

$postingStatusLabels = [
    'scheduled' => 'Scheduled',
    'open' => 'Open',
    'closed' => 'Closed',
    'archived' => 'Archived',
];

$publicDocumentRows = [];

if (!empty($rootDocument)) {
    $publicDocumentRows[] = [
        'label' => ProcurementDocument::label((string) ($rootDocument['document_type'] ?? (($bid['procurement_mode'] ?? $bid['mode_of_procurement'] ?? '') === 'svp' ? ProcurementDocument::TYPE_RFQ : ProcurementDocument::TYPE_BID_NOTICE))),
        'title' => $rootDocument['title'] ?? $bid['procurement_title'],
        'posted_at' => $rootDocument['posted_at'] ?? $bid['posting_date'] ?? null,
        'file_url' => ResponseHelper::url('public/notices/' . (int) $bid['id'] . '/file'),
    ];
}

foreach ($relatedNotices as $notice) {
    $publicDocumentRows[] = [
        'label' => ProcurementDocument::label((string) $notice['document_type']),
        'title' => $notice['title'],
        'posted_at' => $notice['posted_at'] ?? null,
        'file_url' => ResponseHelper::url('public/documents/' . $notice['document_type'] . '/' . (int) $notice['id'] . '/file'),
    ];
}
?>
<h1><?= ViewHelper::escape($bid['procurement_title']); ?></h1>
<p>Official procurement posting details and posted supporting documents across the full lifecycle.</p>

<dl class="detail-grid">
    <div>
        <dt>Reference number</dt>
        <dd><?= ViewHelper::escape($bid['reference_number']); ?></dd>
    </div>
    <div>
        <dt>Region</dt>
        <dd><?= ViewHelper::escape($bid['region']); ?></dd>
    </div>
    <div>
        <dt>Procurement type</dt>
        <dd><?= ViewHelper::escape(ProcurementTypeHelper::label((string) ($bid['procurement_mode'] ?? $bid['mode_of_procurement']))); ?></dd>
    </div>
    <div>
        <dt>Workflow stage</dt>
        <dd><?= ViewHelper::escape($workflowStageLabels[(string) ($bid['current_stage'] ?? 'bid_notice')] ?? ucwords(str_replace('_', ' ', (string) ($bid['current_stage'] ?? 'bid_notice')))); ?></dd>
    </div>
    <div>
        <dt>Posting status</dt>
        <dd><?= ViewHelper::escape($postingStatusLabels[(string) ($bid['posting_status'] ?? 'scheduled')] ?? ucfirst((string) ($bid['posting_status'] ?? 'scheduled'))); ?></dd>
    </div>
</dl>

<div class="section-head" style="margin-top: 18px; margin-bottom: 8px;">
    <div>
        <h2>Description</h2>
    </div>
</div>
<div class="public-tools" style="margin-bottom: 18px; align-items: center;">
    <div class="field search" style="gap: 4px;">
        <div style="color: var(--text);"><?= nl2br(ViewHelper::escape($bid['description'] ?? '')); ?></div>
    </div>
    <div class="actions">
        <a href="<?= ViewHelper::escape(ResponseHelper::url()); ?>">Back to public notices</a>
    </div>
</div>

<div class="section-head" style="margin-bottom: 8px;">
    <div>
        <h2>Posted Documents</h2>
    </div>
</div>
<?php if (empty($publicDocumentRows)): ?>
    <p>No posted documents are currently public for this procurement.</p>
<?php else: ?>
    <div class="public-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Posted At</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicDocumentRows as $row): ?>
                    <tr>
                        <td><?= ViewHelper::escape($row['label']); ?></td>
                        <td><?= ViewHelper::escape($row['title']); ?></td>
                        <td><?= ViewHelper::escape($row['posted_at'] ? date('Y-m-d H:i', strtotime((string) $row['posted_at'])) : ''); ?></td>
                        <td><a href="<?= ViewHelper::escape($row['file_url']); ?>" target="_blank" rel="noopener">Open PDF</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
