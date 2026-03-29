<?php

use App\Helpers\RegionBranchHelper;

$regionBranchMapJson = json_encode(RegionBranchHelper::mapping(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var regionBranchMap = <?= $regionBranchMapJson ?: '{}'; ?>;
    var groups = document.querySelectorAll('[data-region-branch-group]');

    groups.forEach(function (group) {
        var regionSelect = group.querySelector('[data-region-select]');
        var branchSelect = group.querySelector('[data-branch-select]');
        var initialBranch = group.getAttribute('data-selected-branch') || '';

        if (!regionSelect || !branchSelect) {
            return;
        }

        var renderBranches = function (selectedRegion, selectedBranch) {
            var branches = regionBranchMap[selectedRegion] || [];

            branchSelect.innerHTML = '';

            if (!selectedRegion || branches.length === 0) {
                branchSelect.disabled = true;
                branchSelect.appendChild(new Option('Select region first', ''));
                branchSelect.value = '';
                return;
            }

            branchSelect.disabled = false;
            branchSelect.appendChild(new Option('Select branch', ''));

            branches.forEach(function (branch) {
                var option = new Option(branch, branch);

                if (branch === selectedBranch) {
                    option.selected = true;
                }

                branchSelect.appendChild(option);
            });

            if (!branches.includes(selectedBranch)) {
                branchSelect.value = '';
            }
        };

        renderBranches(regionSelect.value, initialBranch);

        regionSelect.addEventListener('change', function () {
            renderBranches(regionSelect.value, '');
        });
    });
});
</script>
