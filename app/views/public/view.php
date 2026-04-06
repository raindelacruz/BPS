<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;
?>
<h1><?= ViewHelper::escape($bid['procurement_title']); ?></h1>
<p>Public procurement posting details and posted supporting documents.</p>

<dl class="detail-grid">
    <div>
        <dt>Reference number</dt>
        <dd><?= ViewHelper::escape($bid['reference_number']); ?></dd>
    </div>
    <div>
        <dt>Region</dt>
        <dd><?= ViewHelper::escape($bid['region']); ?></dd>
    </div>
    <div>
        <dt>Procurement type</dt>
        <dd><?= ViewHelper::escape(ProcurementTypeHelper::label((string) $bid['mode_of_procurement'])); ?></dd>
    </div>
    <div>
        <dt>Current stage</dt>
        <dd><?= ViewHelper::escape(ucwords(str_replace('_', ' ', (string) ($bid['current_stage'] ?? 'draft')))); ?></dd>
    </div>
</dl>

<h2>Description</h2>
<p><?= nl2br(ViewHelper::escape($bid['description'] ?? '')); ?></p>

<?php if ($bidNotice): ?>
    <p><a href="<?= ViewHelper::escape(ResponseHelper::url('public/notices/' . (int) $bid['id'] . '/file')); ?>" target="_blank" rel="noopener">Open bid notice PDF</a></p>
<?php endif; ?>
<p><a href="<?= ViewHelper::escape(ResponseHelper::url()); ?>">Back to public notices</a></p>

<h2>Posted Documents</h2>
<?php if (empty($relatedNotices)): ?>
    <p>No downstream documents are currently public for this procurement.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Posted At</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($relatedNotices as $notice): ?>
                <tr>
                    <td><?= ViewHelper::escape(ProcurementDocument::label((string) $notice['document_type'])); ?></td>
                    <td><?= ViewHelper::escape($notice['title']); ?></td>
                    <td><?= ViewHelper::escape(date('Y-m-d H:i', strtotime((string) $notice['posted_at']))); ?></td>
                    <td><a href="<?= ViewHelper::escape(ResponseHelper::url('public/documents/' . $notice['document_type'] . '/' . (int) $notice['id'] . '/file')); ?>" target="_blank" rel="noopener">Open PDF</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
