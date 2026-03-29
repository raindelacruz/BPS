<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;

$startDateValue = isset($old['start_date']) ? str_replace(' ', 'T', substr((string) $old['start_date'], 0, 16)) : '';
$endDateValue = isset($old['end_date']) ? str_replace(' ', 'T', substr((string) $old['end_date'], 0, 16)) : '';
?>
<h1>Create Related Notice</h1>
<p>Create a workflow notice linked to an existing active bid. The related notice inherits the bid's reference code, region, and procurement type.</p>

<?php if (!empty($errors)): ?>
    <div class="flash">
        <?php foreach ($errors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/related')); ?>" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

    <label for="type">Related notice type</label>
    <select id="type" name="type" required>
        <option value="">Select type</option>
        <?php foreach ($relatedTypes as $type): ?>
            <option value="<?= ViewHelper::escape($type); ?>" <?= ($old['type'] ?? '') === $type ? 'selected' : ''; ?>>
                <?= ViewHelper::escape(strtoupper($type)); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="selected_bid_id">Eligible active bid</label>
    <select id="selected_bid_id" name="selected_bid_id" required>
        <option value="">Select bid</option>
        <?php foreach ($eligibleBids as $bid): ?>
            <option value="<?= ViewHelper::escape((string) $bid['id']); ?>" <?= (int) ($old['selected_bid_id'] ?? 0) === (int) $bid['id'] ? 'selected' : ''; ?>>
                <?= ViewHelper::escape($bid['reference_code'] . ' | ' . $bid['title'] . ' | ' . $bid['region']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="title">Title</label>
    <input id="title" name="title" type="text" value="<?= ViewHelper::escape($old['title'] ?? ''); ?>" required>

    <label for="start_date">Start date</label>
    <input id="start_date" name="start_date" type="datetime-local" value="<?= ViewHelper::escape($startDateValue); ?>" required>

    <label for="end_date">End date</label>
    <input id="end_date" name="end_date" type="datetime-local" value="<?= ViewHelper::escape($endDateValue); ?>" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="6" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>

    <label for="notice_pdf">PDF file</label>
    <input id="notice_pdf" name="notice_pdf" type="file" accept="application/pdf,.pdf" required>

    <button type="submit">Create related notice</button>
</form>

<?php if ($selectedType !== '' && empty($eligibleBids)): ?>
    <p>No eligible active bids are currently available for <?= ViewHelper::escape(strtoupper($selectedType)); ?>.</p>
<?php endif; ?>
