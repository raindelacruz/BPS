<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
use App\Services\ProcurementPostingService;
use App\Services\SmallValueProcurementService;
$postingStatusLabels = [
    'scheduled' => 'Scheduled',
    'open' => 'Open',
    'closed' => 'Closed',
    'archived' => 'Archived',
];

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
            <p>Choose the procurement mode first. Competitive Bidding and Small Value Procurement now start from separate entry forms and separate workflow pages.</p>
        </div>
    </div>
    <div class="action-row">
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('procurements/create/competitive-bidding')); ?>">Create Competitive Bidding Posting</a>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('procurements/create/svp')); ?>">Create Small Value Procurement Record</a>
        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/diagnostics/missing-files')); ?>">
                Missing Files<?= !empty($missingDocumentFilesCount) ? ' (' . (int) $missingDocumentFilesCount . ')' : ''; ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($notices)): ?>
    <p>No procurement postings found yet.</p>
<?php else: ?>
    <div class="card-section stack-sm">
        <div class="page-head">
            <div>
                <h2>Scheduled</h2>
                <p>Competitive bidding records scheduled for future posting dates.</p>
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
                                <td><?= ViewHelper::escape(!empty($notice['posting_date']) ? date('Y-m-d H:i', strtotime((string) $notice['posting_date'])) : '-'); ?></td>
                                <td><?= ViewHelper::escape(!empty($notice['bid_submission_deadline']) ? date('Y-m-d H:i', strtotime((string) $notice['bid_submission_deadline'])) : '-'); ?></td>
                                <td><a href="<?= ViewHelper::escape(ResponseHelper::url((($notice['procurement_mode'] ?? $notice['mode_of_procurement'] ?? '') === SmallValueProcurementService::MODE) ? 'procurements/' . (int) $notice['id'] . '/workflow/svp' : 'procurements/' . (int) $notice['id'] . '/workflow/competitive-bidding')); ?>">View</a></td>
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
                <p>Competitive Bidding and SVP are tracked independently, with status computed from their own workflows.</p>
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
                            <td><?= ViewHelper::escape(ProcurementTypeHelper::label((string) ($notice['procurement_mode'] ?? $notice['mode_of_procurement']))); ?></td>
                            <td><a href="<?= ViewHelper::escape(ResponseHelper::url((($notice['procurement_mode'] ?? $notice['mode_of_procurement'] ?? '') === SmallValueProcurementService::MODE) ? 'procurements/' . (int) $notice['id'] . '/workflow/svp' : 'procurements/' . (int) $notice['id'] . '/workflow/competitive-bidding')); ?>">View</a></td>
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
                                <td><a href="<?= ViewHelper::escape(ResponseHelper::url((($notice['procurement_mode'] ?? $notice['mode_of_procurement'] ?? '') === SmallValueProcurementService::MODE) ? 'procurements/' . (int) $notice['id'] . '/workflow/svp' : 'procurements/' . (int) $notice['id'] . '/workflow/competitive-bidding')); ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
