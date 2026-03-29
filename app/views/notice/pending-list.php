<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Bid Notices</h1>
        <p>Manage your bid notices. Pending notices remain editable; active and expired records are view-only in this phase.</p>
    </div>
</div>

<div class="panel stack-sm">
    <div class="page-head">
        <div>
            <h2>Actions</h2>
            <p>Create a root bid notice or add follow-up workflow records.</p>
        </div>
    </div>
    <div class="action-row">
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/create')); ?>">Create bid notice</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=sbb')); ?>">Create SBB</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=resolution')); ?>">Create Resolution</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=award')); ?>">Create Award</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=contract')); ?>">Create Contract</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=proceed')); ?>">Create Proceed</a>
    </div>
</div>

<?php if (empty($notices)): ?>
    <p>No bid notices found yet.</p>
<?php else: ?>
    <div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Pending</h2>
            <p>Records still open for edits before activation.</p>
        </div>
    </div>
    <?php if (empty($pendingNotices)): ?>
        <p>No pending bid notices.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Dates</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingNotices as $notice): ?>
                    <tr>
                        <td><strong><?= ViewHelper::escape($notice['title']); ?></strong></td>
                        <td><code><?= ViewHelper::escape($notice['reference_code']); ?></code></td>
                        <td><span class="status-badge pending"><?= ViewHelper::escape($notice['status']); ?></span></td>
                        <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['start_date']))); ?> to <?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['end_date']))); ?></td>
                        <td>
                            <div class="action-row">
                                <a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'])); ?>">View</a>
                                <a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/edit')); ?>">Edit</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
    </div>

    <div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>All Bid Notices</h2>
            <p>Full list across statuses, uploader ownership, and workflow types.</p>
        </div>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Reference</th>
                <th>Status</th>
                <th>Region</th>
                <th>Branch</th>
                <th>Procurement</th>
                <th>Uploader</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notices as $notice): ?>
                <tr>
                    <td><strong><?= ViewHelper::escape($notice['title']); ?></strong></td>
                    <td><code><?= ViewHelper::escape($notice['reference_code']); ?></code></td>
                    <td><span class="status-badge <?= ViewHelper::escape((string) ($notice['status'] ?? '')); ?>"><?= ViewHelper::escape($notice['status']); ?></span></td>
                    <td><?= ViewHelper::escape($notice['region']); ?></td>
                    <td><?= ViewHelper::escape($notice['branch'] ?? ''); ?></td>
                    <td><?= ViewHelper::escape(ProcurementTypeHelper::label((string) $notice['procurement_type'])); ?></td>
                    <td><?= ViewHelper::escape(trim(($notice['firstname'] ?? '') . ' ' . ($notice['lastname'] ?? '')) ?: ($notice['uploader_username'] ?? '')); ?></td>
                    <td>
                        <div class="action-row">
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'])); ?>">View</a>
                            <?php if (($notice['status'] ?? null) === 'pending' && ((int) $notice['uploaded_by'] === (int) ($currentUser['id'] ?? 0) || ($currentUser['role'] ?? null) === 'admin')): ?>
                                <a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/edit')); ?>">Edit</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </div>

    <div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Archived</h2>
            <p>Workflow sets kept for reference after archival.</p>
        </div>
    </div>
    <?php if (empty($archivedNotices)): ?>
        <p>No archived bid notices.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Reference</th>
                    <th>Archived At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archivedNotices as $notice): ?>
                    <tr>
                        <td><strong><?= ViewHelper::escape($notice['title']); ?></strong></td>
                        <td><code><?= ViewHelper::escape($notice['reference_code']); ?></code></td>
                        <td><?= ViewHelper::escape($notice['archived_at'] ? date('Y-m-d H:i', strtotime((string) $notice['archived_at'])) : ''); ?></td>
                        <td><a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'])); ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
    </div>
<?php endif; ?>
