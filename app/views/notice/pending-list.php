<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;
?>
<div class="page-head">
    <div>
        <h1>Procurement Postings</h1>
        <p>Each project uses one procurement record, then all signed documents are posted in strict legal sequence under that record.</p>
    </div>
</div>

<div class="panel stack-sm">
    <div class="page-head">
        <div>
            <h2>Actions</h2>
            <p>Create the root record first, then add downstream posting documents as prerequisites become available.</p>
        </div>
    </div>
    <div class="action-row">
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/create')); ?>">Create procurement posting</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=' . ProcurementDocument::TYPE_SBB)); ?>">Add Supplemental/Bid Bulletin</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=' . ProcurementDocument::TYPE_RESOLUTION)); ?>">Add Resolution</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=' . ProcurementDocument::TYPE_AWARD)); ?>">Add Award</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=' . ProcurementDocument::TYPE_CONTRACT)); ?>">Add Contract</a>
        <a class="chip-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create?type=' . ProcurementDocument::TYPE_NOTICE_TO_PROCEED)); ?>">Add Notice to Proceed</a>
    </div>
</div>

<?php if (empty($notices)): ?>
    <p>No procurement postings found yet.</p>
<?php else: ?>
    <div class="card-section stack-sm">
        <div class="page-head">
            <div>
                <h2>Pending</h2>
                <p>Procurement postings whose Bid Notice is scheduled but not yet active.</p>
            </div>
        </div>
        <?php if (empty($pendingNotices)): ?>
            <p>No pending procurement postings.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Reference</th>
                            <th>Posting Date</th>
                            <th>Deadline</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingNotices as $notice): ?>
                            <tr>
                                <td><strong><?= ViewHelper::escape($notice['procurement_title']); ?></strong></td>
                                <td><code><?= ViewHelper::escape($notice['reference_number']); ?></code></td>
                                <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['posting_date']))); ?></td>
                                <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['bid_submission_deadline']))); ?></td>
                                <td><a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'])); ?>">View</a></td>
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
                <h2>All Procurement Records</h2>
                <p>Workflow stage, submission deadline, and mode of procurement are tracked on the root record.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Current Stage</th>
                        <th>Region</th>
                        <th>Branch</th>
                        <th>Mode</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notices as $notice): ?>
                        <tr>
                            <td><strong><?= ViewHelper::escape($notice['procurement_title']); ?></strong></td>
                            <td><code><?= ViewHelper::escape($notice['reference_number']); ?></code></td>
                            <td><span class="status-badge <?= ViewHelper::escape((string) ($notice['status'] ?? '')); ?>"><?= ViewHelper::escape($notice['status']); ?></span></td>
                            <td><?= ViewHelper::escape(ucwords(str_replace('_', ' ', (string) ($notice['current_stage'] ?? 'draft')))); ?></td>
                            <td><?= ViewHelper::escape($notice['region']); ?></td>
                            <td><?= ViewHelper::escape($notice['branch'] ?? ''); ?></td>
                            <td><?= ViewHelper::escape(ProcurementTypeHelper::label((string) $notice['mode_of_procurement'])); ?></td>
                            <td><a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'])); ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($archivedNotices)): ?>
        <div class="card-section stack-sm">
            <div class="page-head">
                <div>
                    <h2>Archived</h2>
                    <p>Retained only for compatibility. New postings in this module do not use archive actions.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Reference</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($archivedNotices as $notice): ?>
                            <tr>
                                <td><strong><?= ViewHelper::escape($notice['procurement_title']); ?></strong></td>
                                <td><code><?= ViewHelper::escape($notice['reference_number']); ?></code></td>
                                <td><a href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'])); ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
