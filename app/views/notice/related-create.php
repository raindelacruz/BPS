<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;

$startDateValue = isset($old['start_date']) ? str_replace(' ', 'T', substr((string) $old['start_date'], 0, 16)) : '';
$endDateValue = isset($old['end_date']) ? str_replace(' ', 'T', substr((string) $old['end_date'], 0, 16)) : '';
$selectedBidId = (int) ($old['selected_bid_id'] ?? 0);
$selectedBidLabel = '';

foreach ($eligibleBids as $bid) {
    if ((int) $bid['id'] === $selectedBidId) {
        $selectedBidLabel = $bid['reference_code'] . ' | ' . $bid['title'] . ' | ' . ($bid['branch'] ?? 'No branch');
        break;
    }
}
?>
<style>
    .related-create-page {
        display: grid;
        gap: 14px;
    }
    .related-create-head {
        display: grid;
        gap: 6px;
    }
    .related-create-head h1,
    .related-create-head p {
        margin: 0;
    }
    .related-create-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .related-create-form {
        display: grid;
        gap: 10px;
    }
    .related-create-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 12px;
    }
    .related-create-field {
        display: grid;
        gap: 6px;
        min-width: 0;
    }
    .related-create-field.full {
        grid-column: 1 / -1;
    }
    .related-create-combobox {
        position: relative;
    }
    .related-create-combobox-panel {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 20;
        max-height: 220px;
        overflow-y: auto;
        padding: 6px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
    }
    .related-create-combobox-panel[hidden] {
        display: none;
    }
    .related-create-option {
        width: 100%;
        padding: 8px 10px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: var(--text);
        text-align: left;
        font-weight: 600;
    }
    .related-create-option:hover,
    .related-create-option:focus {
        background: var(--panel-soft);
    }
    .related-create-empty {
        padding: 8px 10px;
        color: var(--muted);
        font-size: 0.92rem;
    }
    .related-create-actions {
        display: flex;
        justify-content: flex-start;
        gap: 8px;
        margin-top: 2px;
    }
    @media (max-width: 760px) {
        .related-create-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="related-create-page">
    <div class="related-create-head">
        <h1>Create Related Notice</h1>
        <p>Create a workflow notice linked to an active bid from your branch. The related notice inherits the bid's reference code, region, and procurement type.</p>
        <div class="related-create-meta">
            <span class="status-badge"><?= ViewHelper::escape(strtoupper($selectedType !== '' ? $selectedType : 'type required')); ?></span>
            <span class="status-badge"><?= ViewHelper::escape(($assignedBranch ?? '') !== '' ? 'Branch: ' . ($assignedBranch ?? '') : 'No branch assigned'); ?></span>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="flash">
            <?php foreach ($errors as $error): ?>
                <div><?= ViewHelper::escape($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="related-create-form" method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/related')); ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
        <div class="related-create-grid">
            <div class="related-create-field">
                <label for="type">Related notice type</label>
                <select id="type" name="type" required data-related-type-select data-base-url="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create')); ?>">
                    <option value="">Select type</option>
                    <?php foreach ($relatedTypes as $type): ?>
                        <option value="<?= ViewHelper::escape($type); ?>" <?= ($old['type'] ?? '') === $type ? 'selected' : ''; ?>>
                            <?= ViewHelper::escape(strtoupper($type)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="related-create-field">
                <label for="selected_bid_search">Eligible active bid</label>
                <div class="related-create-combobox" data-bid-combobox>
                    <input
                        id="selected_bid_search"
                        type="search"
                        value="<?= ViewHelper::escape($selectedBidLabel); ?>"
                        placeholder="<?= ViewHelper::escape($selectedType !== '' ? 'Search branch bid by title or reference code' : 'Select a related notice type first'); ?>"
                        autocomplete="off"
                        <?= $selectedType === '' || ($assignedBranch ?? '') === '' ? 'disabled' : ''; ?>
                        data-bid-search
                    >
                    <input type="hidden" id="selected_bid_id" name="selected_bid_id" value="<?= ViewHelper::escape((string) $selectedBidId); ?>" required data-bid-hidden>
                    <div class="related-create-combobox-panel" hidden data-bid-panel>
                        <?php if ($eligibleBids === []): ?>
                            <div class="related-create-empty">No eligible active bids found in your branch for this related notice type.</div>
                        <?php else: ?>
                            <?php foreach ($eligibleBids as $bid): ?>
                                <button
                                    class="related-create-option"
                                    type="button"
                                    data-bid-option
                                    data-id="<?= ViewHelper::escape((string) $bid['id']); ?>"
                                    data-label="<?= ViewHelper::escape($bid['reference_code'] . ' | ' . $bid['title'] . ' | ' . ($bid['branch'] ?? 'No branch')); ?>"
                                >
                                    <?= ViewHelper::escape($bid['reference_code'] . ' | ' . $bid['title'] . ' | ' . ($bid['branch'] ?? 'No branch')); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="related-create-field full">
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="<?= ViewHelper::escape($old['title'] ?? ''); ?>" required>
            </div>

            <div class="related-create-field">
                <label for="start_date">Start date</label>
                <input id="start_date" name="start_date" type="datetime-local" value="<?= ViewHelper::escape($startDateValue); ?>" required>
            </div>

            <div class="related-create-field">
                <label for="end_date">End date</label>
                <input id="end_date" name="end_date" type="datetime-local" value="<?= ViewHelper::escape($endDateValue); ?>" required>
            </div>

            <div class="related-create-field full">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" required><?= ViewHelper::escape($old['description'] ?? ''); ?></textarea>
            </div>

            <div class="related-create-field full">
                <label for="notice_pdf">PDF file</label>
                <input id="notice_pdf" name="notice_pdf" type="file" accept="application/pdf,.pdf" required>
            </div>
        </div>

        <div class="related-create-actions">
            <button type="submit">Create related notice</button>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Back to notices</a>
        </div>
    </form>

    <?php if (($assignedBranch ?? '') === ''): ?>
        <p class="muted">Your account needs a branch assignment before you can create related notices.</p>
    <?php elseif ($selectedType !== '' && empty($eligibleBids)): ?>
        <p class="muted">No eligible active bids are currently available in branch <?= ViewHelper::escape($assignedBranch ?? ''); ?> for <?= ViewHelper::escape(strtoupper($selectedType)); ?>.</p>
    <?php endif; ?>
</div>

<script>
    (function () {
        var typeSelect = document.querySelector('[data-related-type-select]');
        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                var baseUrl = typeSelect.getAttribute('data-base-url') || '';
                var selectedType = typeSelect.value || '';
                if (!baseUrl) {
                    return;
                }

                if (!selectedType) {
                    window.location.href = baseUrl;
                    return;
                }

                window.location.href = baseUrl + '?type=' + encodeURIComponent(selectedType);
            });
        }

        var combobox = document.querySelector('[data-bid-combobox]');
        if (!combobox) {
            return;
        }

        var searchInput = combobox.querySelector('[data-bid-search]');
        var hiddenInput = combobox.querySelector('[data-bid-hidden]');
        var panel = combobox.querySelector('[data-bid-panel]');
        var options = Array.prototype.slice.call(combobox.querySelectorAll('[data-bid-option]'));

        if (!searchInput || !hiddenInput || !panel) {
            return;
        }

        var filterOptions = function () {
            var query = (searchInput.value || '').toLowerCase().trim();
            var visibleCount = 0;

            options.forEach(function (option) {
                var label = (option.getAttribute('data-label') || '').toLowerCase();
                var matches = query === '' || label.indexOf(query) !== -1;
                option.hidden = !matches;
                if (matches) {
                    visibleCount += 1;
                }
            });

            if (options.length > 0) {
                panel.hidden = false;
            }

            var emptyState = panel.querySelector('.related-create-empty');
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'related-create-empty';
                emptyState.textContent = 'No matching bids found in your branch.';
                panel.appendChild(emptyState);
            }
            emptyState.hidden = visibleCount !== 0;
        };

        var chooseOption = function (option) {
            hiddenInput.value = option.getAttribute('data-id') || '';
            searchInput.value = option.getAttribute('data-label') || '';
            panel.hidden = true;
        };

        searchInput.addEventListener('focus', function () {
            if (options.length > 0) {
                filterOptions();
            }
        });

        searchInput.addEventListener('input', function () {
            hiddenInput.value = '';
            filterOptions();
        });

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                chooseOption(option);
            });
        });

        document.addEventListener('click', function (event) {
            if (!combobox.contains(event.target)) {
                panel.hidden = true;
            }
        });
    }());
</script>
