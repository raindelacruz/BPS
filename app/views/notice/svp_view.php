<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;
$stageLabels = [
    ProcurementDocument::TYPE_RFQ => 'Request for Quotation',
    ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS => 'Abstract of Quotations',
    ProcurementDocument::TYPE_CANVASS => 'Canvass',
    ProcurementDocument::TYPE_AWARD => 'Award',
    ProcurementDocument::TYPE_CONTRACT_OR_PO => 'Contract / Purchase Order',
];
$statusLabels = [
    'scheduled' => 'Scheduled',
    'open' => 'Open',
    'closed' => 'Closed',
    'archived' => 'Archived',
];
$postingStatus = (string) ($parent['posting_status'] ?? 'scheduled');
$stage = (string) ($parent['current_stage'] ?? ProcurementDocument::TYPE_RFQ);
$isArchived = !empty($parent['archived_at']);
$isAdmin = ($currentUser['role'] ?? null) === 'admin';
$documents = $documents ?? [];
$actions = $actions ?? [];
$documentConfigs = [
    ProcurementDocument::TYPE_RFQ => [
        'title' => 'Request for Quotation',
        'description' => 'Root document. Only one RFQ may be posted for each SVP procurement.',
        'action_label' => 'Add RFQ',
    ],
    ProcurementDocument::TYPE_ABSTRACT_OF_QUOTATIONS => [
        'title' => 'Abstract of Quotations',
        'description' => 'Requires a posted RFQ.',
        'action_label' => 'Add Abstract of Quotations',
    ],
    ProcurementDocument::TYPE_CANVASS => [
        'title' => 'Canvass',
        'description' => 'Alternative to Abstract of Quotations. Requires a posted RFQ.',
        'action_label' => 'Add Canvass',
    ],
    ProcurementDocument::TYPE_AWARD => [
        'title' => 'Award',
        'description' => 'Requires a posted Abstract of Quotations or Canvass after RFQ closing.',
        'action_label' => 'Add Award',
    ],
    ProcurementDocument::TYPE_CONTRACT_OR_PO => [
        'title' => 'Contract / Purchase Order',
        'description' => 'Optional. Requires a posted Award.',
        'action_label' => 'Add Contract / Purchase Order',
    ],
];

$documentRows = [];
foreach ($documentConfigs as $type => $config) {
    $document = $documents[$type][0] ?? null;
    $documentRows[] = [
        'type' => $type,
        'label' => $config['title'],
        'status' => $document ? 'Posted' : 'Not yet posted',
        'posted_at' => $document['posted_at'] ?? null,
        'file_url' => $document ? ResponseHelper::url('documents/' . $type . '/' . (int) $document['id'] . '/file') : null,
    ];
}
?>
<div class="page-head">
    <div>
        <h1><?= ViewHelper::escape($parent['procurement_title']); ?></h1>
        <p>Small Value Procurement workflow for reference <?= ViewHelper::escape($parent['reference_number']); ?>.</p>
    </div>
    <div class="action-row">
        <span class="status-badge <?= ViewHelper::escape($postingStatus); ?>"><?= ViewHelper::escape($statusLabels[$postingStatus] ?? ucfirst(str_replace('_', ' ', $postingStatus))); ?></span>
        <span class="status-badge"><?= ViewHelper::escape($stageLabels[$stage] ?? ucfirst(str_replace('_', ' ', $stage))); ?></span>
    </div>
</div>

<dl class="detail-grid">
    <div><dt>ABC</dt><dd><?= ViewHelper::escape(number_format((float) ($parent['abc'] ?? 0), 2)); ?></dd></div>
    <div><dt>Mode of procurement</dt><dd>Small Value Procurement</dd></div>
    <div><dt>Category</dt><dd><?= ViewHelper::escape((string) ($parent['category'] ?? '')); ?></dd></div>
    <div><dt>End-user unit</dt><dd><?= ViewHelper::escape((string) ($parent['end_user_unit'] ?? '')); ?></dd></div>
    <div><dt>Reference number</dt><dd><?= ViewHelper::escape((string) ($parent['reference_number'] ?? '')); ?></dd></div>
    <div><dt>Archive status</dt><dd><?= ViewHelper::escape($isArchived ? 'Archived' : 'Active'); ?></dd></div>
</dl>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Small Value Procurement Actions</h2>
            <p><?= ViewHelper::escape($isArchived ? 'Archived records are immutable and remain part of the public historical record.' : 'Only SVP-related actions are available on this workflow page.'); ?></p>
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
        <?php foreach ($documentConfigs as $type => $config): ?>
            <?php $guard = $actions[$type] ?? ['allowed' => false, 'helper_text' => '']; ?>
            <?php $document = $documents[$type][0] ?? null; ?>
            <?php if ($document || $isArchived): ?>
                <span class="chip-link is-disabled"><?= ViewHelper::escape($config['action_label']); ?></span>
            <?php elseif (!empty($guard['allowed'])): ?>
                <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=' . $type . '&parent_id=' . (int) $parent['id'])); ?>"><?= ViewHelper::escape($config['action_label']); ?></a>
            <?php else: ?>
                <span class="chip-link is-disabled" title="<?= ViewHelper::escape((string) ($guard['helper_text'] ?? $config['description'])); ?>"><?= ViewHelper::escape($config['action_label']); ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="card-section stack-sm">
    <div class="page-head"><div><h2>SVP Documents</h2></div></div>
    <p class="muted" style="margin: -2px 0 4px;">Only Small Value Procurement-stage documents appear on this page.</p>
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
                                <div class="action-row"><a class="btn-link" href="<?= ViewHelper::escape($row['file_url']); ?>" target="_blank" rel="noopener">PDF</a></div>
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
        <div class="page-head"><div><h2>Archive Metadata</h2></div></div>
        <dl class="detail-grid">
            <div><dt>Archived at</dt><dd><?= ViewHelper::escape(!empty($parent['archived_at']) ? date('Y-m-d H:i', strtotime((string) $parent['archived_at'])) : ''); ?></dd></div>
            <div><dt>Archive reason</dt><dd><?= ViewHelper::escape((string) ($parent['archive_reason'] ?? '')); ?></dd></div>
            <div><dt>Approval reference</dt><dd><?= ViewHelper::escape((string) ($parent['archive_approval_reference'] ?? '')); ?></dd></div>
        </dl>
    </div>
<?php endif; ?>
