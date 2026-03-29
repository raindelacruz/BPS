<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;

$canEdit = ($notice['status'] ?? null) === 'pending'
    && (((int) $notice['uploaded_by'] === (int) ($currentUser['id'] ?? 0)) || (($currentUser['role'] ?? null) === 'admin'));
$canDelete = (int) $notice['uploaded_by'] === (int) ($currentUser['id'] ?? 0);
$isOwner = (int) $notice['uploaded_by'] === (int) ($currentUser['id'] ?? 0);
?>
<div class="page-head">
    <div>
        <h1><?= ViewHelper::escape($notice['title']); ?></h1>
        <p><?= ViewHelper::escape(strtoupper((string) $notice['type'])); ?> notice details for workflow set <?= ViewHelper::escape($notice['reference_code']); ?>.</p>
    </div>
    <span class="status-badge <?= ViewHelper::escape((string) ($notice['status'] ?? '')); ?>"><?= ViewHelper::escape((string) ($notice['status'] ?? '')); ?></span>
</div>

<dl class="detail-grid">
    <div>
        <dt>Reference code</dt>
        <dd><?= ViewHelper::escape($notice['reference_code']); ?></dd>
    </div>
    <div>
        <dt>Status</dt>
        <dd><?= ViewHelper::escape($notice['status']); ?></dd>
    </div>
    <div>
        <dt>Procurement type</dt>
        <dd><?= ViewHelper::escape(ProcurementTypeHelper::label((string) $notice['procurement_type'])); ?></dd>
    </div>
    <div>
        <dt>Region</dt>
        <dd><?= ViewHelper::escape($notice['region']); ?></dd>
    </div>
    <div>
        <dt>Branch</dt>
        <dd><?= ViewHelper::escape($notice['branch'] ?? 'Not assigned'); ?></dd>
    </div>
    <div>
        <dt>Start date</dt>
        <dd><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['start_date']))); ?></dd>
    </div>
    <div>
        <dt>End date</dt>
        <dd><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['end_date']))); ?></dd>
    </div>
    <div>
        <dt>Uploader</dt>
        <dd><?= ViewHelper::escape(trim(($notice['firstname'] ?? '') . ' ' . ($notice['lastname'] ?? '')) ?: ($notice['uploader_username'] ?? '')); ?></dd>
    </div>
    <div>
        <dt>Uploaded at</dt>
        <dd><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['upload_date']))); ?></dd>
    </div>
</dl>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Description</h2>
        </div>
    </div>
    <div class="panel"><?= nl2br(ViewHelper::escape($notice['description'] ?? '')); ?></div>
</div>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Actions</h2>
            <p>Open files, move back to the list, or continue the workflow.</p>
        </div>
    </div>
    <div class="action-row">
        <?php if (!empty($notice['file_path'])): ?>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/file')); ?>" target="_blank" rel="noopener">Open PDF</a>
        <?php endif; ?>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Back to notices</a>
        <?php if ($canEdit): ?>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/edit')); ?>">Edit pending notice</a>
        <?php endif; ?>
    </div>

    <?php if (($notice['type'] ?? null) === 'bid'): ?>
        <div class="action-row">
            <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=sbb&bid_id=' . (int) $notice['id'])); ?>">Add SBB</a>
            <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=resolution&bid_id=' . (int) $notice['id'])); ?>">Add Resolution</a>
            <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=award&bid_id=' . (int) $notice['id'])); ?>">Add Award</a>
            <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=contract&bid_id=' . (int) $notice['id'])); ?>">Add Contract</a>
            <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=proceed&bid_id=' . (int) $notice['id'])); ?>">Add Proceed</a>
        </div>
    <?php endif; ?>

    <div class="action-row">
        <?php if ($canDelete): ?>
            <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/delete')); ?>" class="inline-form danger-form">
                <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                <button type="submit">Delete notice</button>
            </form>
        <?php endif; ?>

        <?php if ((int) ($notice['is_archived'] ?? 0) === 1 && $isOwner): ?>
            <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/unarchive')); ?>" class="inline-form">
                <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                <button type="submit">Unarchive workflow set</button>
            </form>
        <?php elseif ($isOwner && $canArchive): ?>
            <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/archive')); ?>" class="inline-form">
                <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                <button type="submit">Archive workflow set</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!$canArchive && !empty($archiveErrors) && $isOwner): ?>
        <p class="text-danger"><?= ViewHelper::escape(implode(' ', $archiveErrors)); ?></p>
    <?php endif; ?>
</div>

<?php if (!empty($workflowSet)): ?>
    <div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Workflow Set</h2>
            <p>All linked notices under the same procurement workflow.</p>
        </div>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Status</th>
                <th>Start</th>
                <th>End</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($workflowSet as $workflowNotice): ?>
                <tr>
                    <td><?= ViewHelper::escape(strtoupper((string) $workflowNotice['type'])); ?></td>
                    <td><strong><?= ViewHelper::escape($workflowNotice['title']); ?></strong></td>
                    <td><span class="status-badge <?= ViewHelper::escape((string) ($workflowNotice['status'] ?? '')); ?>"><?= ViewHelper::escape($workflowNotice['status']); ?></span></td>
                    <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $workflowNotice['start_date']))); ?></td>
                    <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $workflowNotice['end_date']))); ?></td>
                    <td><a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $workflowNotice['id'])); ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </div>
<?php endif; ?>
