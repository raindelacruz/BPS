<?php

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
        <div class="metric-card"><dt>Total bids</dt><strong><?= ViewHelper::escape((string) ($overview['total_bids'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Pending</dt><strong><?= ViewHelper::escape((string) ($overview['pending'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Active</dt><strong><?= ViewHelper::escape((string) ($overview['active'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Expired</dt><strong><?= ViewHelper::escape((string) ($overview['expired'] ?? 0)); ?></strong></div>
        <div class="metric-card"><dt>Archived</dt><strong><?= ViewHelper::escape((string) ($overview['archived'] ?? 0)); ?></strong></div>
        <?php if (isset($overview['users'])): ?>
            <div class="metric-card"><dt>Users</dt><strong><?= ViewHelper::escape((string) $overview['users']); ?></strong></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Quick Actions</h2>
            <p>Common tasks for day-to-day notice management.</p>
        </div>
    </div>
    <div class="action-row">
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Manage bid notices</a>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('profile')); ?>">My account</a>
        <?php if (($currentUser['role'] ?? null) === 'admin'): ?>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('users')); ?>">User management</a>
        <?php endif; ?>
    </div>
</div>
