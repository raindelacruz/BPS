<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;

$startDateValue = isset($old['start_date']) ? str_replace(' ', 'T', substr((string) $old['start_date'], 0, 16)) : '';
$endDateValue = isset($old['end_date']) ? str_replace(' ', 'T', substr((string) $old['end_date'], 0, 16)) : '';
?>
<div class="page-head">
    <div>
        <h1>Create Bid Notice</h1>
        <p>Create the root bid notice with its own reference code, procurement details, schedule, description, and PDF attachment.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="flash">
        <?php foreach ($errors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>" enctype="multipart/form-data" class="form-grid two-col">
    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

    <div>
        <label for="title">Title</label>
        <input id="title" name="title" type="text" value="<?= ViewHelper::escape($old['title'] ?? ''); ?>" required>
    </div>

    <div>
        <label for="reference_code">Reference code</label>
        <input id="reference_code" name="reference_code" type="text" value="<?= ViewHelper::escape($old['reference_code'] ?? ''); ?>" required>
    </div>

    <div>
        <label for="procurement_type">Procurement type</label>
        <select id="procurement_type" name="procurement_type" required>
            <option value="">Select procurement type</option>
            <?php foreach ($procurementTypes as $procurementType => $label): ?>
                <option value="<?= ViewHelper::escape($procurementType); ?>" <?= ($old['procurement_type'] ?? '') === $procurementType ? 'selected' : ''; ?>>
                    <?= ViewHelper::escape($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="start_date">Start date</label>
        <input id="start_date" name="start_date" type="datetime-local" value="<?= ViewHelper::escape($startDateValue); ?>" required>
    </div>

    <div>
        <label for="end_date">End date</label>
        <input id="end_date" name="end_date" type="datetime-local" value="<?= ViewHelper::escape($endDateValue); ?>" required>
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>
    </div>

    <div style="grid-column: 1 / -1;">
        <label for="notice_pdf">PDF file</label>
        <input id="notice_pdf" name="notice_pdf" type="file" accept="application/pdf,.pdf" required>
    </div>

    <div class="btn-row" style="grid-column: 1 / -1;">
        <button type="submit">Create bid notice</button>
        <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Back to notices</a>
    </div>
</form>
