<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;

$postedAtValue = isset($old['posted_at']) ? str_replace(' ', 'T', substr((string) $old['posted_at'], 0, 16)) : '';
$selectedParentId = (int) ($old['parent_procurement_id'] ?? 0);
$selectedParentLabel = '';

foreach ($eligibleParents as $parent) {
    if ((int) $parent['id'] === $selectedParentId) {
        $selectedParentLabel = $parent['reference_number'] . ' | ' . $parent['procurement_title'];
        break;
    }
}
?>
<div class="related-create-page">
    <style>
        .related-create-page {
            display: grid;
            gap: 14px;
        }
        .related-create-head {
            display: grid;
            gap: 4px;
        }
        .related-create-head h1,
        .related-create-head p {
            margin: 0;
        }
        .related-create-head h1 {
            line-height: 1.05;
        }
        .related-create-head p {
            max-width: 70rem;
            line-height: 1.35;
        }
        .related-create-form {
            display: grid;
            gap: 12px;
        }
        .related-create-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            align-items: start;
        }
        .related-create-field {
            display: grid;
            gap: 4px;
        }
        .related-create-field.full {
            grid-column: 1 / -1;
        }
        .related-create-static-field {
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: var(--text);
            min-height: 40px;
            display: flex;
            align-items: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .related-create-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        @media (max-width: 760px) {
            .related-create-grid {
                grid-template-columns: 1fr;
            }
            .related-create-field.full {
                grid-column: auto;
            }
        }
    </style>
    <div class="related-create-head">
        <h1>Post Procurement Document</h1>
        <p>Post an approved signed document under an existing official procurement record. Sequence and chronology rules are enforced both here and on submit.</p>
    </div>

    <form class="related-create-form" method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/related')); ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
        <div class="related-create-grid">
            <div class="related-create-field">
                <label for="type">Document type</label>
                <div id="type" class="related-create-static-field <?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'type')); ?>">
                    <?= ViewHelper::escape($selectedType !== '' ? ProcurementDocument::label($selectedType) : 'No document type selected'); ?>
                </div>
                <input type="hidden" name="type" value="<?= ViewHelper::escape($selectedType); ?>">
                <?php if (ValidationHelper::first($errors, 'type')): ?>
                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'type')); ?></div>
                <?php endif; ?>
            </div>

            <div class="related-create-field">
                <label for="parent_procurement_search">Eligible procurement record</label>
                <div id="parent_procurement_search" class="related-create-static-field <?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'parent_procurement_id')); ?>">
                    <?= ViewHelper::escape($selectedParentLabel !== '' ? $selectedParentLabel : 'No eligible procurement record selected'); ?>
                </div>
                <input type="hidden" id="parent_procurement_id" name="parent_procurement_id" value="<?= ViewHelper::escape((string) $selectedParentId); ?>" required>
                <?php if (ValidationHelper::first($errors, 'parent_procurement_id')): ?>
                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'parent_procurement_id')); ?></div>
                <?php endif; ?>
            </div>

            <div class="related-create-field full">
                <label for="title">Document title</label>
                <input id="title" name="title" type="text" value="<?= ViewHelper::escape($old['title'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'title')); ?>" required>
                <?php if (ValidationHelper::first($errors, 'title')): ?>
                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'title')); ?></div>
                <?php endif; ?>
            </div>

            <div class="related-create-field">
                <label for="posted_at">Posted at</label>
                <input id="posted_at" name="posted_at" type="datetime-local" value="<?= ViewHelper::escape($postedAtValue); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'posted_at')); ?>" required>
                <?php if (ValidationHelper::first($errors, 'posted_at')): ?>
                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'posted_at')); ?></div>
                <?php endif; ?>
            </div>

            <div class="related-create-field full">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'description')); ?>" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>
                <?php if (ValidationHelper::first($errors, 'description')): ?>
                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'description')); ?></div>
                <?php endif; ?>
            </div>

            <div class="related-create-field full">
                <label for="notice_pdf">PDF file</label>
                <input id="notice_pdf" name="notice_pdf" type="file" accept="application/pdf,.pdf" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'notice_pdf')); ?>" required>
                <?php if (ValidationHelper::first($errors, 'notice_pdf')): ?>
                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'notice_pdf')); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (ValidationHelper::first($errors, '_global')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
        <?php endif; ?>

        <div class="related-create-actions">
            <button type="submit">Post document</button>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Back to postings</a>
        </div>
    </form>

    <?php if ($selectedType !== '' && empty($eligibleParents)): ?>
        <p class="muted">No eligible procurement records are available for <?= ViewHelper::escape(ProcurementDocument::label($selectedType)); ?>.</p>
    <?php endif; ?>
</div>
