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
        $selectedParentLabel = $parent['reference_number'] . ' | ' . $parent['procurement_title'] . ' | ' . ($parent['branch'] ?? 'No branch');
        break;
    }
}
?>
<div class="related-create-page">
    <div class="related-create-head">
        <h1>Post Procurement Document</h1>
        <p>Post an approved signed document under an existing procurement record. Sequence rules are enforced both here and on submit.</p>
    </div>

    <form class="related-create-form" method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('notices/related')); ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
        <div class="related-create-grid">
            <div class="related-create-field">
                <label for="type">Document type</label>
                <select id="type" name="type" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'type')); ?>" required data-related-type-select data-base-url="<?= ViewHelper::escape(ResponseHelper::url('notices/related/create')); ?>">
                    <option value="">Select type</option>
                    <?php foreach ($relatedTypes as $type): ?>
                        <option value="<?= ViewHelper::escape($type); ?>" <?= ($old['type'] ?? '') === $type ? 'selected' : ''; ?>>
                            <?= ViewHelper::escape(ProcurementDocument::label($type)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (ValidationHelper::first($errors, 'type')): ?>
                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'type')); ?></div>
                <?php endif; ?>
            </div>

            <div class="related-create-field">
                <label for="parent_procurement_search">Eligible procurement record</label>
                <div class="related-create-combobox" data-bid-combobox>
                    <input
                        id="parent_procurement_search"
                        type="search"
                        value="<?= ViewHelper::escape($selectedParentLabel); ?>"
                        placeholder="<?= ViewHelper::escape($selectedType !== '' ? 'Search by reference number or procurement title' : 'Select a document type first'); ?>"
                        autocomplete="off"
                        <?= $selectedType === '' ? 'disabled' : ''; ?>
                        data-bid-search
                    >
                    <input type="hidden" id="parent_procurement_id" name="parent_procurement_id" value="<?= ViewHelper::escape((string) $selectedParentId); ?>" required data-bid-hidden>
                    <div class="related-create-combobox-panel" hidden data-bid-panel>
                        <?php if ($eligibleParents === []): ?>
                            <div class="related-create-empty">No procurement records are currently eligible for this document type.</div>
                        <?php else: ?>
                            <?php foreach ($eligibleParents as $parent): ?>
                                <button
                                    class="related-create-option"
                                    type="button"
                                    data-bid-option
                                    data-id="<?= ViewHelper::escape((string) $parent['id']); ?>"
                                    data-label="<?= ViewHelper::escape($parent['reference_number'] . ' | ' . $parent['procurement_title'] . ' | ' . ($parent['branch'] ?? 'No branch')); ?>"
                                >
                                    <?= ViewHelper::escape($parent['reference_number'] . ' | ' . $parent['procurement_title'] . ' | ' . ($parent['branch'] ?? 'No branch')); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
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
                window.location.href = selectedType ? baseUrl + '?type=' + encodeURIComponent(selectedType) : baseUrl;
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
                emptyState.textContent = 'No matching procurement records found.';
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
