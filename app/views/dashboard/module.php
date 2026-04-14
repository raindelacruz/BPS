<?php

use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1><?= ViewHelper::escape($title ?? 'Module Dashboard'); ?></h1>
        <p>Counts and recent documents for this procurement mode only.</p>
    </div>
</div>

<div class="metric-grid">
    <div class="metric-card"><dt>Total Postings</dt><strong><?= ViewHelper::escape((string) ($overview['total_procurements'] ?? 0)); ?></strong></div>
    <div class="metric-card"><dt>Scheduled</dt><strong><?= ViewHelper::escape((string) ($overview['scheduled'] ?? 0)); ?></strong></div>
    <div class="metric-card"><dt>Open</dt><strong><?= ViewHelper::escape((string) ($overview['open'] ?? 0)); ?></strong></div>
    <div class="metric-card"><dt>Closed</dt><strong><?= ViewHelper::escape((string) ($overview['closed'] ?? 0)); ?></strong></div>
    <div class="metric-card"><dt>Archived</dt><strong><?= ViewHelper::escape((string) ($overview['archived'] ?? 0)); ?></strong></div>
</div>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Recent Documents</h2>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Procurement</th>
                    <th>Posted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($overview['recent_documents'])): ?>
                    <tr><td colspan="3">No recent documents for this module yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($overview['recent_documents'] as $document): ?>
                        <tr>
                            <td><?= ViewHelper::escape(ucwords(str_replace('_', ' ', (string) ($document['document_type'] ?? '')))); ?></td>
                            <td><?= ViewHelper::escape((string) ($document['procurement_title'] ?? '')); ?></td>
                            <td><?= ViewHelper::escape(!empty($document['posted_at']) ? date('Y-m-d H:i', strtotime((string) $document['posted_at'])) : '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="action-row">
    <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('dashboard')); ?>">Back to Dashboard</a>
</div>
