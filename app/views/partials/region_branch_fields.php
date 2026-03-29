<?php

use App\Helpers\RegionBranchHelper;
use App\Helpers\ViewHelper;

$regionFieldId = $regionFieldId ?? 'region';
$branchFieldId = $branchFieldId ?? 'branch';
$regionName = $regionName ?? 'region';
$branchName = $branchName ?? 'branch';
$selectedRegion = (string) ($selectedRegion ?? '');
$selectedBranch = (string) ($selectedBranch ?? '');
$regions = $regions ?? RegionBranchHelper::regions();
$branches = RegionBranchHelper::branchesForRegion($selectedRegion);
$branchDisabled = $selectedRegion === '' || $branches === [];
?>
<div data-region-branch-group data-selected-branch="<?= ViewHelper::escape($selectedBranch); ?>">
    <label for="<?= ViewHelper::escape($regionFieldId); ?>">Region</label>
    <select
        id="<?= ViewHelper::escape($regionFieldId); ?>"
        name="<?= ViewHelper::escape($regionName); ?>"
        data-region-select
        required
    >
        <option value="">Select region</option>
        <?php foreach ($regions as $region): ?>
            <option value="<?= ViewHelper::escape($region); ?>" <?= $selectedRegion === $region ? 'selected' : ''; ?>>
                <?= ViewHelper::escape($region); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="<?= ViewHelper::escape($branchFieldId); ?>">Branch</label>
    <select
        id="<?= ViewHelper::escape($branchFieldId); ?>"
        name="<?= ViewHelper::escape($branchName); ?>"
        data-branch-select
        <?= $branchDisabled ? 'disabled' : ''; ?>
        required
    >
        <?php if ($branchDisabled): ?>
            <option value="">Select region first</option>
        <?php else: ?>
            <option value="">Select branch</option>
            <?php foreach ($branches as $branch): ?>
                <option value="<?= ViewHelper::escape($branch); ?>" <?= $selectedBranch === $branch ? 'selected' : ''; ?>>
                    <?= ViewHelper::escape($branch); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
