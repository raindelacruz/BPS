<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;

$currentUser = SecurityHelper::currentUser();
?>
<div class="page-head">
    <div>
        <h1><?= ViewHelper::escape($title ?? 'Dashboard'); ?></h1>
        <p><?= ViewHelper::escape($message ?? 'Dashboard module is pending implementation.'); ?></p>
    </div>
    <?php if ($currentUser): ?>
        <span class="status-badge <?= ViewHelper::escape((string) ($currentUser['role'] ?? '')); ?>">
            <?= ViewHelper::escape((string) ($currentUser['role'] ?? 'user')); ?>
        </span>
    <?php endif; ?>
</div>
<?php if (!empty($overview)): ?>
    <div class="metric-grid">
        <div class="metric-card"><dt>Total Procurements</dt><strong><?= ViewHelper::escape((string) ($overview['total_procurements'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Scheduled</dt><strong><?= ViewHelper::escape((string) ($overview['scheduled'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Open for Bids</dt><strong><?= ViewHelper::escape((string) ($overview['open'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Closed for Bids</dt><strong><?= ViewHelper::escape((string) ($overview['closed'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Archived</dt><strong><?= ViewHelper::escape((string) ($overview['archived'] ?? 0)); ?></strong></div>
        <?php if (isset($overview['users'])): ?>
            <div class="metric-card"><dt>Users</dt><strong><?= ViewHelper::escape((string) $overview['users']); ?></strong></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>All Procurement</h2>
            <p>Unified procurement listing across Competitive Bidding and SVP.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Procurement</th>
                    <th>Reference</th>
                    <th>Posting Status</th>
                    <th>Stage</th>
                    <th>Region</th>
                    <th>Mode</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($procurements)): ?>
                    <tr><td colspan="7">No procurement records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($procurements as $procurement): ?>
                        <?php
                        $isSvp = (($procurement['procurement_mode'] ?? $procurement['mode_of_procurement'] ?? '') === 'svp');
                        $status = (string) ($procurement['posting_status'] ?? 'scheduled');
                        $stage = (string) ($procurement['current_stage'] ?? 'bid_notice');
                        ?>
                        <tr>
                            <td><?= ViewHelper::escape((string) ($procurement['procurement_title'] ?? '')); ?></td>
                            <td><code><?= ViewHelper::escape((string) ($procurement['reference_number'] ?? '')); ?></code></td>
                            <td><span class="status-badge <?= ViewHelper::escape($status); ?>"><?= ViewHelper::escape(ucwords(str_replace('_', ' ', $status))); ?></span></td>
                            <td><?= ViewHelper::escape(ucwords(str_replace('_', ' ', $stage))); ?></td>
                            <td><?= ViewHelper::escape((string) ($procurement['region'] ?? '')); ?></td>
                            <td><?= ViewHelper::escape(ProcurementTypeHelper::label((string) ($procurement['procurement_mode'] ?? $procurement['mode_of_procurement'] ?? ''))); ?></td>
                            <td><a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url($isSvp ? 'procurements/' . (int) $procurement['id'] . '/workflow/svp' : 'procurements/' . (int) $procurement['id'] . '/workflow/competitive-bidding')); ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="card-section stack-sm">
    <div class="page-head">
    <div>
        <h2>Quick Actions</h2>
        <p>Common tasks for official procurement posting and lifecycle management.</p>
    </div>
    </div>
    <div class="action-row">
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('procurements/create/competitive-bidding')); ?>">Create Competitive Bidding Posting</a>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('procurements/create/svp')); ?>">Create Small Value Procurement Record</a>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Manage bid notices</a>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('profile')); ?>">My account</a>
        <?php if (($currentUser['role'] ?? null) === 'admin'): ?>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('users')); ?>">User management</a>
        <?php endif; ?>
    </div>
</div>
