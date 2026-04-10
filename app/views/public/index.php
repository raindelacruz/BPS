<?php

use App\Helpers\RegionBranchHelper;
use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;

$workflowStageLabels = [
    'bid_notice' => 'Bid Notice / Invitation to Bid',
    'supplemental_bid_bulletin' => 'Supplemental/Bid Bulletin',
    'resolution' => 'Resolution',
    'award' => 'Notice of Award / Award',
    'contract' => 'Contract',
    'notice_to_proceed' => 'Notice to Proceed',
];

$postingStatusLabels = [
    'scheduled' => 'Scheduled',
    'open' => 'Open for Bids',
    'closed' => 'Closed',
    'archived' => 'Archived',
];
?>
<div class="section-head">
    <div>
        <h2>Official Procurement Postings</h2>
        <p>All official public procurement postings are listed here, including scheduled, open, closed, and archived lifecycle records.</p>
    </div>
    <span class="status-pill"><?= ViewHelper::escape((string) count($bids)); ?> listed</span>
</div>

<form method="GET" action="<?= ViewHelper::escape(ResponseHelper::url()); ?>" class="public-tools">
    <div class="field search">
        <label for="search">Search</label>
        <input id="search" name="search" type="text" placeholder="Title, reference number, or keyword" value="<?= ViewHelper::escape($filters['search'] ?? ''); ?>">
    </div>

    <div class="field">
        <label for="region">Region</label>
        <select id="region" name="region">
            <option value="">All regions</option>
            <?php foreach (RegionBranchHelper::regions() as $region): ?>
                <option value="<?= ViewHelper::escape($region); ?>" <?= ($filters['region'] ?? '') === $region ? 'selected' : ''; ?>>
                    <?= ViewHelper::escape($region); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field">
        <label for="procurement_type">Procurement Type</label>
        <select id="procurement_type" name="procurement_type">
            <option value="">All procurement types</option>
            <?php foreach (ProcurementTypeHelper::all() as $procurementType => $label): ?>
                <option value="<?= ViewHelper::escape($procurementType); ?>" <?= ($filters['procurement_type'] ?? '') === $procurementType ? 'selected' : ''; ?>>
                    <?= ViewHelper::escape($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="actions">
        <button type="submit">Apply Filters</button>
        <a href="<?= ViewHelper::escape(ResponseHelper::url()); ?>">Reset</a>
    </div>
</form>

<div class="results-meta">
    <span>Official public procurement history</span>
    <span>Posting status and workflow stage are shown separately</span>
    <span><?= ViewHelper::escape((string) count($bids)); ?> result(s)</span>
</div>

<?php if (empty($bids)): ?>
    <p>No procurement postings match the current filters.</p>
<?php else: ?>
    <div class="public-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Region</th>
                    <th>Procurement</th>
                    <th>Workflow Stage</th>
                    <th>Bid Deadline</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bids as $bid): ?>
                    <tr>
                        <td><strong><?= ViewHelper::escape($bid['procurement_title']); ?></strong></td>
                        <td><code><?= ViewHelper::escape($bid['reference_number']); ?></code></td>
                        <td><?= ViewHelper::escape($postingStatusLabels[(string) ($bid['posting_status'] ?? 'scheduled')] ?? ucfirst((string) ($bid['posting_status'] ?? 'scheduled'))); ?></td>
                        <td><?= ViewHelper::escape($bid['region']); ?></td>
                        <td><?= ViewHelper::escape(ProcurementTypeHelper::label((string) $bid['mode_of_procurement'])); ?></td>
                        <td><?= ViewHelper::escape($workflowStageLabels[(string) ($bid['current_stage'] ?? 'bid_notice')] ?? ucwords(str_replace('_', ' ', (string) ($bid['current_stage'] ?? 'bid_notice')))); ?></td>
                        <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $bid['bid_submission_deadline']))); ?></td>
                        <td><a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('public/notices/' . (int) $bid['id'])); ?>">Open Notice</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
