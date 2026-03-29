<?php

use App\Helpers\ProcurementTypeHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;

$startDateValue = isset($old['start_date']) ? str_replace(' ', 'T', substr((string) $old['start_date'], 0, 16)) : '';
$endDateValue = isset($old['end_date']) ? str_replace(' ', 'T', substr((string) $old['end_date'], 0, 16)) : '';
?>
<h1>Edit Notice</h1>
<p>Only pending notices may be edited. Replacing the PDF is optional.</p>

<?php if (!empty($errors)): ?>
    <div class="flash">
        <?php foreach ($errors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/' . (int) $notice['id'] . '/update')); ?>" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

    <label for="title">Title</label>
    <input id="title" name="title" type="text" value="<?= ViewHelper::escape($old['title'] ?? ''); ?>" required>

    <?php if (($notice['type'] ?? null) === 'bid'): ?>
        <label for="reference_code">Reference code</label>
        <input id="reference_code" name="reference_code" type="text" value="<?= ViewHelper::escape($old['reference_code'] ?? ''); ?>" required>

        <label for="procurement_type">Procurement type</label>
        <select id="procurement_type" name="procurement_type" required>
            <option value="">Select procurement type</option>
            <?php foreach ($procurementTypes as $procurementType => $label): ?>
                <option value="<?= ViewHelper::escape($procurementType); ?>" <?= ($old['procurement_type'] ?? '') === $procurementType ? 'selected' : ''; ?>>
                    <?= ViewHelper::escape($label); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>Assigned region: <?= ViewHelper::escape($assignedRegion ?? ($old['assigned_region'] ?? 'Not assigned')); ?></p>
        <p>Assigned branch: <?= ViewHelper::escape($assignedBranch ?? ($old['assigned_branch'] ?? 'Not assigned')); ?></p>
    <?php else: ?>
        <input type="hidden" name="type" value="<?= ViewHelper::escape($old['type'] ?? ''); ?>">
        <input type="hidden" name="selected_bid_id" value="<?= ViewHelper::escape((string) ($old['selected_bid_id'] ?? 0)); ?>">
        <p>Workflow reference: <?= ViewHelper::escape($old['reference_code'] ?? ''); ?></p>
        <p>Procurement type: <?= ViewHelper::escape(ProcurementTypeHelper::label((string) ($old['procurement_type'] ?? ''))); ?></p>
        <p>Region: <?= ViewHelper::escape($old['assigned_region'] ?? ''); ?></p>
        <p>Branch: <?= ViewHelper::escape($old['assigned_branch'] ?? ''); ?></p>
    <?php endif; ?>

    <label for="start_date">Start date</label>
    <input id="start_date" name="start_date" type="datetime-local" value="<?= ViewHelper::escape($startDateValue); ?>" required>

    <label for="end_date">End date</label>
    <input id="end_date" name="end_date" type="datetime-local" value="<?= ViewHelper::escape($endDateValue); ?>" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="6" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>

    <label for="notice_pdf">Replace PDF</label>
    <input id="notice_pdf" name="notice_pdf" type="file" accept="application/pdf,.pdf">

    <button type="submit">Update bid notice</button>
</form>
