<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
?>
<h1><?= ViewHelper::escape($bid['title']); ?></h1>
<p>Public bid notice details and related supporting procurement documents.</p>

<dl class="detail-grid">
    <div>
        <dt>Reference code</dt>
        <dd><?= ViewHelper::escape($bid['reference_code']); ?></dd>
    </div>
    <div>
        <dt>Region</dt>
        <dd><?= ViewHelper::escape($bid['region']); ?></dd>
    </div>
    <div>
        <dt>Procurement type</dt>
        <dd><?= ViewHelper::escape(ProcurementTypeHelper::label((string) $bid['procurement_type'])); ?></dd>
    </div>
    <div>
        <dt>Active until</dt>
        <dd><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $bid['end_date']))); ?></dd>
    </div>
</dl>

<h2>Description</h2>
<p><?= nl2br(ViewHelper::escape($bid['description'] ?? '')); ?></p>

<p><a href="<?= ViewHelper::escape(ResponseHelper::url('public/notices/' . (int) $bid['id'] . '/file')); ?>" target="_blank" rel="noopener">Open bid PDF</a></p>
<p><a href="<?= ViewHelper::escape(ResponseHelper::url()); ?>">Back to public notices</a></p>

<h2>Related Documents</h2>
<?php if (empty($relatedNotices)): ?>
    <p>No related documents are currently public for this bid.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Status</th>
                <th>End date</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($relatedNotices as $notice): ?>
                <tr>
                    <td><?= ViewHelper::escape(strtoupper((string) $notice['type'])); ?></td>
                    <td><?= ViewHelper::escape($notice['title']); ?></td>
                    <td><?= ViewHelper::escape($notice['status']); ?></td>
                    <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['end_date']))); ?></td>
                    <td><a href="<?= ViewHelper::escape(ResponseHelper::url('public/notices/' . (int) $notice['id'] . '/file')); ?>" target="_blank" rel="noopener">Open PDF</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
