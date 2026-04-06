<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
use App\Models\ProcurementDocument;

$isParentEdit = (bool) ($isParentEdit ?? false);
$postingDateValue = isset($old['posting_date']) ? str_replace(' ', 'T', substr((string) $old['posting_date'], 0, 16)) : '';
$deadlineValue = isset($old['bid_submission_deadline']) ? str_replace(' ', 'T', substr((string) $old['bid_submission_deadline'], 0, 16)) : '';
$documentPostedAt = isset($old['posted_at']) ? str_replace(' ', 'T', substr((string) $old['posted_at'], 0, 16)) : '';
?>
<h1><?= ViewHelper::escape($isParentEdit ? 'Edit Procurement Posting' : 'Edit ' . ProcurementDocument::label((string) ($documentType ?? ''))); ?></h1>
<p><?= ViewHelper::escape($isParentEdit ? 'The procurement record and Bid Notice remain editable until the next sequential document is posted.' : 'This document remains editable only while it is not locked by the next required stage.'); ?></p>

<?php if ($isParentEdit): ?>
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) ($notice['id'] ?? 0) . '/update')); ?>" enctype="multipart/form-data" class="form-grid two-col">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <div>
            <label for="procurement_title">Procurement title</label>
            <input id="procurement_title" name="procurement_title" type="text" value="<?= ViewHelper::escape($old['procurement_title'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'procurement_title')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'procurement_title')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'procurement_title')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="reference_number">Reference number</label>
            <input id="reference_number" name="reference_number" type="text" value="<?= ViewHelper::escape($old['reference_number'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'reference_number')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'reference_number')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'reference_number')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="abc">ABC</label>
            <input id="abc" name="abc" type="number" min="0" step="0.01" value="<?= ViewHelper::escape((string) ($old['abc'] ?? '')); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'abc')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'abc')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'abc')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="mode_of_procurement">Mode of procurement</label>
            <select id="mode_of_procurement" name="mode_of_procurement" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'mode_of_procurement')); ?>" required>
                <option value="">Select mode of procurement</option>
                <?php foreach ($procurementTypes as $procurementType => $label): ?>
                    <option value="<?= ViewHelper::escape($procurementType); ?>" <?= ($old['mode_of_procurement'] ?? '') === $procurementType ? 'selected' : ''; ?>>
                        <?= ViewHelper::escape($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (ValidationHelper::first($errors, 'mode_of_procurement')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'mode_of_procurement')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="posting_date">Bid Notice posting date</label>
            <input id="posting_date" name="posting_date" type="datetime-local" value="<?= ViewHelper::escape($postingDateValue); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'posting_date')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'posting_date')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'posting_date')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="bid_submission_deadline">Bid submission deadline</label>
            <input id="bid_submission_deadline" name="bid_submission_deadline" type="datetime-local" value="<?= ViewHelper::escape($deadlineValue); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'bid_submission_deadline')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'bid_submission_deadline')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'bid_submission_deadline')); ?></div>
            <?php endif; ?>
        </div>

        <div style="grid-column: 1 / -1;">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'description')); ?>" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>
            <?php if (ValidationHelper::first($errors, 'description')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'description')); ?></div>
            <?php endif; ?>
        </div>

        <div style="grid-column: 1 / -1;">
            <label for="notice_pdf">Replace Bid Notice PDF</label>
            <input id="notice_pdf" name="notice_pdf" type="file" accept="application/pdf,.pdf" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'notice_pdf')); ?>">
            <?php if (ValidationHelper::first($errors, 'notice_pdf')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'notice_pdf')); ?></div>
            <?php endif; ?>
        </div>

        <?php if (ValidationHelper::first($errors, '_global')): ?>
            <div class="field-error" style="grid-column: 1 / -1;"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
        <?php endif; ?>

        <div class="btn-row" style="grid-column: 1 / -1;">
            <button type="submit">Update procurement posting</button>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) ($notice['id'] ?? 0))); ?>">Back to record</a>
        </div>
    </form>
<?php else: ?>
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('documents/' . ($documentType ?? '') . '/' . (int) ($notice['id'] ?? 0) . '/update')); ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
        <input type="hidden" name="type" value="<?= ViewHelper::escape((string) ($documentType ?? '')); ?>">
        <input type="hidden" name="parent_procurement_id" value="<?= ViewHelper::escape((string) ($old['parent_procurement_id'] ?? 0)); ?>">

        <label for="title">Document title</label>
        <input id="title" name="title" type="text" value="<?= ViewHelper::escape($old['title'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'title')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'title')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'title')); ?></div>
        <?php endif; ?>

        <label for="posted_at">Posted at</label>
        <input id="posted_at" name="posted_at" type="datetime-local" value="<?= ViewHelper::escape($documentPostedAt); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'posted_at')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'posted_at')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'posted_at')); ?></div>
        <?php endif; ?>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'description')); ?>" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>
        <?php if (ValidationHelper::first($errors, 'description')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'description')); ?></div>
        <?php endif; ?>

        <label for="notice_pdf">Replace PDF</label>
        <input id="notice_pdf" name="notice_pdf" type="file" accept="application/pdf,.pdf" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'notice_pdf')); ?>">
        <?php if (ValidationHelper::first($errors, 'notice_pdf')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'notice_pdf')); ?></div>
        <?php endif; ?>

        <?php if (ValidationHelper::first($errors, '_global')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
        <?php endif; ?>

        <button type="submit">Update document</button>
    </form>
<?php endif; ?>
