<?php

use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Missing Document Files</h1>
        <p>Admin diagnostics for procurement records whose stored `file_path` no longer resolves to a file on disk.</p>
    </div>
</div>

<div class="panel stack-sm">
    <div class="action-row">
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Back to Procurement Postings</a>
    </div>
</div>

<?php if (empty($rows)): ?>
    <p>All stored procurement document paths currently resolve to files on disk.</p>
<?php else: ?>
    <div class="card-section stack-sm">
        <div class="page-head">
            <div>
                <h2>Broken File Links</h2>
                <p><?= ViewHelper::escape((string) count($rows)); ?> document record(s) are pointing to missing files.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Notice</th>
                        <th>Reference</th>
                        <th>Posted At</th>
                        <th>Saved Path</th>
                        <th>Expected Disk Path</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= ViewHelper::escape($row['document_label']); ?></td>
                            <td><strong><?= ViewHelper::escape($row['procurement_title'] !== '' ? $row['procurement_title'] : $row['title']); ?></strong></td>
                            <td><code><?= ViewHelper::escape($row['reference_number'] !== '' ? $row['reference_number'] : ('Parent #' . (int) $row['parent_procurement_id'])); ?></code></td>
                            <td><?= ViewHelper::escape($row['posted_at'] !== '' ? date('Y-m-d H:i', strtotime((string) $row['posted_at'])) : '-'); ?></td>
                            <td><code><?= ViewHelper::escape($row['file_path']); ?></code></td>
                            <td><code><?= ViewHelper::escape($row['absolute_path']); ?></code></td>
                            <td>
                                <?php if (!empty($row['workflow_url'])): ?>
                                    <a href="<?= ViewHelper::escape(ResponseHelper::url((string) $row['workflow_url'])); ?>">Workflow</a>
                                <?php endif; ?>
                                <?php if (!empty($row['public_file_url'])): ?>
                                    <br>
                                    <a href="<?= ViewHelper::escape(ResponseHelper::url((string) $row['public_file_url'])); ?>" target="_blank" rel="noopener">Public Link</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
