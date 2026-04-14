<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Create Small Value Procurement Record</h1>
        <p>Enter the SVP root record first. The posting workflow is document-driven: RFQ, Abstract of Quotations or Canvass, Award, and optional Contract or Purchase Order.</p>
    </div>
</div>

<form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('procurements/svp')); ?>" class="form-grid two-col">
    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
    <input type="hidden" name="procurement_mode" value="svp">

    <div>
        <label for="procurement_title">Procurement title</label>
        <input id="procurement_title" name="procurement_title" type="text" value="<?= ViewHelper::escape($old['procurement_title'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'procurement_title')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'procurement_title')): ?><div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'procurement_title')); ?></div><?php endif; ?>
    </div>

    <div>
        <label for="reference_number">Reference number</label>
        <input id="reference_number" name="reference_number" type="text" value="<?= ViewHelper::escape($old['reference_number'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'reference_number')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'reference_number')): ?><div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'reference_number')); ?></div><?php endif; ?>
    </div>

    <div>
        <label for="abc">ABC</label>
        <input id="abc" name="abc" type="number" min="0" step="0.01" value="<?= ViewHelper::escape((string) ($old['abc'] ?? '')); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'abc')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'abc')): ?><div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'abc')); ?></div><?php endif; ?>
    </div>

    <div>
        <label for="category">Category</label>
        <select id="category" name="category" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'category')); ?>" required>
            <option value="">Select category</option>
            <?php foreach (['goods' => 'Goods', 'infrastructure' => 'Infrastructure', 'consulting' => 'Consulting'] as $value => $label): ?>
                <option value="<?= ViewHelper::escape($value); ?>" <?= ($old['category'] ?? '') === $value ? 'selected' : ''; ?>><?= ViewHelper::escape($label); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (ValidationHelper::first($errors, 'category')): ?><div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'category')); ?></div><?php endif; ?>
    </div>

    <div>
        <label for="end_user_unit">End-user unit</label>
        <input id="end_user_unit" name="end_user_unit" type="text" value="<?= ViewHelper::escape($old['end_user_unit'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'end_user_unit')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'end_user_unit')): ?><div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'end_user_unit')); ?></div><?php endif; ?>
    </div>

    <div>
        <label>Assigned region</label>
        <input type="text" value="<?= ViewHelper::escape($assignedRegion ?? ''); ?>" readonly>
    </div>

    <div>
        <label>Assigned branch</label>
        <input type="text" value="<?= ViewHelper::escape($assignedBranch ?? ''); ?>" readonly>
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'description')); ?>" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>
        <?php if (ValidationHelper::first($errors, 'description')): ?><div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'description')); ?></div><?php endif; ?>
    </div>
    <?php if (ValidationHelper::first($errors, '_global')): ?><div class="field-error" style="grid-column: 1 / -1;"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div><?php endif; ?>

    <div class="btn-row" style="grid-column: 1 / -1;">
        <button type="submit">Create Small Value Procurement Record</button>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('procurements/create')); ?>">Change procurement mode</a>
    </div>
</form>
