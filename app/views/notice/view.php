<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;

$bidNotice = $documents[ProcurementDocument::TYPE_BID_NOTICE][0] ?? null;
$resolution = $documents[ProcurementDocument::TYPE_RESOLUTION][0] ?? null;
$award = $documents[ProcurementDocument::TYPE_AWARD][0] ?? null;
$contract = $documents[ProcurementDocument::TYPE_CONTRACT][0] ?? null;
$noticeToProceed = $documents[ProcurementDocument::TYPE_NOTICE_TO_PROCEED][0] ?? null;
$sbbs = $documents[ProcurementDocument::TYPE_SBB] ?? [];
$isAdmin = (($currentUser['role'] ?? '') === 'admin');
$bidNoticeEdit = $editability[ProcurementDocument::TYPE_BID_NOTICE] ?? ['allowed' => false];
$documentRows = [];

foreach ($sbbs as $sbb) {
    $documentRows[] = [
        'label' => ProcurementDocument::label(ProcurementDocument::TYPE_SBB),
        'status' => (int) ($sbb['is_locked'] ?? 0) === 1 && (int) ($sbb['is_reopened'] ?? 0) !== 1 ? 'Locked' : 'Posted',
        'posted_at' => $sbb['posted_at'] ?? null,
        'file_url' => ResponseHelper::url('documents/' . ProcurementDocument::TYPE_SBB . '/' . (int) $sbb['id'] . '/file'),
        'edit_url' => ResponseHelper::url('documents/' . ProcurementDocument::TYPE_SBB . '/' . (int) $sbb['id'] . '/edit'),
        'can_edit' => (bool) (($editability[ProcurementDocument::TYPE_SBB][(int) $sbb['id']]['allowed'] ?? false)),
        'reopen_url' => ResponseHelper::url('documents/' . ProcurementDocument::TYPE_SBB . '/' . (int) $sbb['id'] . '/reopen'),
        'show_reopen' => $isAdmin && (int) ($sbb['is_locked'] ?? 0) === 1 && (int) ($sbb['is_reopened'] ?? 0) !== 1,
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
        'status' => $document
            ? ((int) ($document['is_locked'] ?? 0) === 1 && (int) ($document['is_reopened'] ?? 0) !== 1 ? 'Locked' : 'Posted')
            : 'Not yet posted',
        'posted_at' => $document['posted_at'] ?? null,
        'file_url' => $document ? ResponseHelper::url('documents/' . $type . '/' . (int) $document['id'] . '/file') : null,
        'edit_url' => $document ? ResponseHelper::url('documents/' . $type . '/' . (int) $document['id'] . '/edit') : null,
        'can_edit' => $document ? (bool) ($editability[$type]['allowed'] ?? false) : false,
        'reopen_url' => $document ? ResponseHelper::url('documents/' . $type . '/' . (int) $document['id'] . '/reopen') : null,
        'show_reopen' => $document && $isAdmin && (int) ($document['is_locked'] ?? 0) === 1 && (int) ($document['is_reopened'] ?? 0) !== 1,
    ];
}
?>
<div class="page-head">
    <div>
        <h1><?= ViewHelper::escape($parent['procurement_title']); ?></h1>
        <p>Procurement record for reference <?= ViewHelper::escape($parent['reference_number']); ?>.</p>
    </div>
    <span class="status-badge <?= ViewHelper::escape((string) ($parent['status'] ?? '')); ?>"><?= ViewHelper::escape((string) ($parent['status'] ?? '')); ?></span>
</div>

<dl class="detail-grid">
    <div>
        <dt>Reference number</dt>
        <dd><?= ViewHelper::escape($parent['reference_number']); ?></dd>
    </div>
    <div>
        <dt>Current workflow stage</dt>
        <dd><?= ViewHelper::escape(ucwords(str_replace('_', ' ', (string) ($parent['current_stage'] ?? 'draft')))); ?></dd>
    </div>
    <div>
        <dt>ABC</dt>
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
        <dt>Region</dt>
        <dd><?= ViewHelper::escape($parent['region']); ?></dd>
    </div>
    <div>
        <dt>Branch</dt>
        <dd><?= ViewHelper::escape($parent['branch'] ?? 'Not assigned'); ?></dd>
    </div>
</dl>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Posting Actions</h2>
        </div>
    </div>
    <div class="action-row">
        <?php foreach ([ProcurementDocument::TYPE_SBB, ProcurementDocument::TYPE_RESOLUTION, ProcurementDocument::TYPE_AWARD, ProcurementDocument::TYPE_CONTRACT, ProcurementDocument::TYPE_NOTICE_TO_PROCEED] as $type): ?>
            <?php $action = $actions[$type] ?? ['allowed' => false]; ?>
            <?php if ($action['allowed']): ?>
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
            <h2>Bid Notice / Invitation to Bid</h2>
        </div>
    </div>
    <?php if ($bidNotice): ?>
        <div class="panel stack-sm">
            <strong><?= ViewHelper::escape($bidNotice['title']); ?></strong>
            <p><?= nl2br(ViewHelper::escape($bidNotice['description'] ?? '')); ?></p>
            <div class="action-row">
                <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $parent['id'] . '/file')); ?>" target="_blank" rel="noopener">Open PDF</a>
                <?php if ($bidNoticeEdit['allowed'] ?? false): ?>
                    <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $parent['id'] . '/edit')); ?>">Edit</a>
                <?php else: ?>
                    <span class="btn-link is-disabled">Edit</span>
                <?php endif; ?>
                <?php if ($isAdmin && (int) ($bidNotice['is_locked'] ?? 0) === 1 && (int) ($bidNotice['is_reopened'] ?? 0) !== 1): ?>
                    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('documents/' . ProcurementDocument::TYPE_BID_NOTICE . '/' . (int) $bidNotice['id'] . '/reopen')); ?>" class="inline-form">
                        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                        <button type="submit">Reopen</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Documents</h2>
        </div>
    </div>
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
                                    <?php if ($row['can_edit']): ?>
                                        <a class="btn-link" href="<?= ViewHelper::escape((string) $row['edit_url']); ?>">Edit</a>
                                    <?php else: ?>
                                        <span class="btn-link is-disabled">Edit</span>
                                    <?php endif; ?>
                                    <?php if ($row['show_reopen']): ?>
                                        <form method="POST" action="<?= ViewHelper::escape((string) $row['reopen_url']); ?>" class="inline-form">
                                            <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                                            <button type="submit">Reopen</button>
                                        </form>
                                    <?php endif; ?>
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
