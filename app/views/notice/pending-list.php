<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
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
?>
<div class="page-head">
    <div>
        <h1>Procurement Postings</h1>
        <p>Each project uses one official public procurement record, then all signed documents are posted in strict legal sequence under that record.</p>
    </div>
</div>

<div class="panel stack-sm">
    <div class="page-head">
        <div>
            <h2>Actions</h2>
            <p>Create the official bid notice first, then add downstream documents only when the legal prerequisite stage and chronology are satisfied.</p>
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
                <h2>Scheduled</h2>
                <p>Official public procurement postings whose opening date has not yet arrived.</p>
            </div>
        </div>
        <?php if (empty($scheduledNotices)): ?>
            <p>No scheduled procurement postings.</p>
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
                        <?php foreach ($scheduledNotices as $notice): ?>
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
                        <th>Posting Status</th>
                        <th>Workflow Stage</th>
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
                            <?php $postingStatus = (string) ($notice['posting_status'] ?? 'scheduled'); ?>
                            <td><span class="status-badge <?= ViewHelper::escape($postingStatus); ?>"><?= ViewHelper::escape($postingStatusLabels[$postingStatus] ?? ucfirst($postingStatus)); ?></span></td>
                            <td><?= ViewHelper::escape($workflowStageLabels[(string) ($notice['current_stage'] ?? 'bid_notice')] ?? ucwords(str_replace('_', ' ', (string) ($notice['current_stage'] ?? 'bid_notice')))); ?></td>
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
                    <p>Archived records remain publicly traceable and read-only. Archiving is one-way and does not rewrite workflow stages or legal dates.</p>
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
