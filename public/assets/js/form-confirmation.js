(function () {
    var modal = document.getElementById('globalFormConfirmationModal');
    if (!modal) {
        return;
    }

    var summaryContainer = document.getElementById('globalFormConfirmationSummary');
    var titleElement = document.getElementById('globalFormConfirmationTitle');
    var subtitleElement = modal.querySelector('.modal-subtitle');
    var verifiedCheckbox = document.getElementById('globalFormConfirmationVerified');
    var confirmButton = modal.querySelector('[data-confirm-submit]');
    var cancelButton = modal.querySelector('[data-confirm-cancel]');
    var dismissButton = modal.querySelector('[data-confirm-dismiss]');
    var state = {
        form: null,
        submitter: null,
        restoreFocus: null,
        isSubmitting: false
    };
    var focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

    function escapeSelector(value) {
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(value);
        }

        return String(value).replace(/(["\\])/g, '\\$1');
    }

    function normalizeText(value) {
        return String(value || '').replace(/\s+/g, ' ').trim();
    }

    function isEligibleForm(form) {
        if (!(form instanceof HTMLFormElement)) {
            return false;
        }

        if ((form.method || '').toUpperCase() !== 'POST') {
            return false;
        }

        if (form.dataset.confirmation === 'off') {
            return false;
        }

        return true;
    }

    function findSubmitterLabel(submitter) {
        if (!submitter) {
            return 'Submit';
        }

        return normalizeText(
            submitter.getAttribute('data-confirm-action-label') ||
            submitter.value ||
            submitter.textContent ||
            submitter.getAttribute('aria-label') ||
            'Submit'
        );
    }

    function isSubmitControl(element) {
        if (!(element instanceof HTMLElement)) {
            return false;
        }

        if (element.tagName === 'BUTTON') {
            return (element.getAttribute('type') || 'submit').toLowerCase() === 'submit';
        }

        if (element.tagName === 'INPUT') {
            return ['submit', 'image'].indexOf((element.type || '').toLowerCase()) !== -1;
        }

        return false;
    }

    function resolveSubmitter(form, candidate) {
        if (isSubmitControl(candidate) && candidate.form === form) {
            return candidate;
        }

        return form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled]), button:not([type]):not([disabled]), input[type="image"]:not([disabled])');
    }

    function findFieldLabel(field) {
        if (field.dataset.confirmLabel) {
            return field.dataset.confirmLabel;
        }

        var id = field.id;
        if (id) {
            var explicitLabel = document.querySelector('label[for="' + escapeSelector(id) + '"]');
            if (explicitLabel) {
                return normalizeText(explicitLabel.textContent);
            }
        }

        var closestLabel = field.closest('label');
        if (closestLabel) {
            return normalizeText(closestLabel.textContent);
        }

        return normalizeText(field.name || field.getAttribute('aria-label') || 'Field');
    }

    function resolveTextFromSelector(field) {
        if (!field.dataset.confirmDisplaySelector) {
            return '';
        }

        var source = document.querySelector(field.dataset.confirmDisplaySelector);
        if (!source) {
            return '';
        }

        return normalizeText(source.textContent || source.value || '');
    }

    function fieldValue(field) {
        if (field.dataset.confirmValue) {
            return normalizeText(field.dataset.confirmValue);
        }

        if (field.type === 'file') {
            if (!field.files || field.files.length === 0) {
                return '';
            }

            return Array.prototype.map.call(field.files, function (file) {
                return file.name;
            }).join(', ');
        }

        if (field.type === 'checkbox') {
            return field.checked ? 'Yes' : 'No';
        }

        if (field.type === 'radio') {
            if (!field.checked) {
                return '';
            }

            return normalizeText(field.dataset.confirmValue || field.value);
        }

        if (field.tagName === 'SELECT') {
            if (field.multiple) {
                return Array.prototype.map.call(field.selectedOptions, function (option) {
                    return normalizeText(option.textContent);
                }).filter(Boolean).join(', ');
            }

            var selectedOption = field.selectedOptions[0];
            return normalizeText(selectedOption ? selectedOption.textContent : field.value);
        }

        if (field.type === 'password') {
            return field.value ? 'Entered securely' : '';
        }

        return normalizeText(field.value);
    }

    function shouldIncludeField(field) {
        if (!field.name && !field.dataset.confirmDisplaySelector) {
            return false;
        }

        if (field.disabled) {
            return false;
        }

        if (field.dataset.confirmInclude === 'false') {
            return false;
        }

        if (field.type === 'hidden' && field.dataset.confirmInclude !== 'true') {
            return false;
        }

        if (field.type === 'submit' || field.type === 'button' || field.type === 'reset' || field.tagName === 'BUTTON') {
            return false;
        }

        if ((field.type === 'radio' || field.type === 'checkbox') && field.dataset.confirmGroupProcessed === 'true') {
            return false;
        }

        return true;
    }

    function collectSummaryItems(form) {
        var fields = Array.prototype.slice.call(form.elements);
        var items = [];
        var radioGroups = new Set();
        var checkboxGroups = new Map();

        fields.forEach(function (field) {
            if (!(field instanceof HTMLElement)) {
                return;
            }

            if (field.type === 'radio' && field.name) {
                radioGroups.add(field.name);
            }

            if (field.type === 'checkbox' && field.name) {
                if (!checkboxGroups.has(field.name)) {
                    checkboxGroups.set(field.name, []);
                }

                checkboxGroups.get(field.name).push(field);
            }
        });

        fields.forEach(function (field) {
            if (!(field instanceof HTMLElement) || !shouldIncludeField(field)) {
                return;
            }

            if (field.type === 'radio' && field.name) {
                var checkedRadio = form.querySelector('input[type="radio"][name="' + escapeSelector(field.name) + '"]:checked');
                if (!checkedRadio) {
                    return;
                }

                if (checkedRadio.dataset.confirmGroupProcessed === 'true') {
                    return;
                }

                checkedRadio.dataset.confirmGroupProcessed = 'true';
                items.push({
                    label: findFieldLabel(checkedRadio),
                    value: fieldValue(checkedRadio),
                    group: checkedRadio.dataset.confirmGroup || 'Form Details'
                });
                return;
            }

            if (field.type === 'checkbox' && field.name && checkboxGroups.get(field.name) && checkboxGroups.get(field.name).length > 1) {
                var groupedCheckboxes = checkboxGroups.get(field.name);
                if (groupedCheckboxes[0] !== field) {
                    return;
                }

                groupedCheckboxes.forEach(function (checkbox) {
                    checkbox.dataset.confirmGroupProcessed = 'true';
                });

                var selectedValues = groupedCheckboxes.filter(function (checkbox) {
                    return checkbox.checked;
                }).map(function (checkbox) {
                    return normalizeText(checkbox.dataset.confirmValue || checkbox.value || findFieldLabel(checkbox));
                }).filter(Boolean);

                items.push({
                    label: findFieldLabel(field),
                    value: selectedValues.length ? selectedValues.join(', ') : 'None selected',
                    group: field.dataset.confirmGroup || 'Form Details'
                });
                return;
            }

            var value = resolveTextFromSelector(field) || fieldValue(field);
            if (!value) {
                return;
            }

            items.push({
                label: findFieldLabel(field),
                value: value,
                group: field.dataset.confirmGroup || field.closest('[data-confirm-group]') && field.closest('[data-confirm-group]').getAttribute('data-confirm-group') || 'Form Details'
            });
        });

        fields.forEach(function (field) {
            if (field instanceof HTMLElement && field.dataset) {
                delete field.dataset.confirmGroupProcessed;
            }
        });

        return items;
    }

    function renderSummary(items) {
        summaryContainer.innerHTML = '';

        if (!items.length) {
            summaryContainer.innerHTML = '<p class="muted">No editable submission details were detected for this action.</p>';
            return;
        }

        var groups = {};
        items.forEach(function (item) {
            if (!groups[item.group]) {
                groups[item.group] = [];
            }

            groups[item.group].push(item);
        });

        Object.keys(groups).forEach(function (groupName) {
            var section = document.createElement('section');
            section.className = 'confirm-summary-group';

            var heading = document.createElement('h3');
            heading.className = 'confirm-summary-group-title';
            heading.textContent = groupName;
            section.appendChild(heading);

            var table = document.createElement('table');
            table.className = 'confirm-summary-table';

            var tbody = document.createElement('tbody');
            groups[groupName].forEach(function (item) {
                var row = document.createElement('tr');
                var labelCell = document.createElement('th');
                labelCell.scope = 'row';
                labelCell.textContent = item.label;

                var valueCell = document.createElement('td');
                valueCell.textContent = item.value;

                row.appendChild(labelCell);
                row.appendChild(valueCell);
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            section.appendChild(table);
            summaryContainer.appendChild(section);
        });
    }

    function updateModalCopy() {
        var actionLabel = findSubmitterLabel(state.submitter);
        titleElement.textContent = 'Review ' + actionLabel.toLowerCase() + ' details';
        subtitleElement.textContent = 'Check the information carefully before you ' + actionLabel.toLowerCase() + '.';
        confirmButton.textContent = actionLabel === 'Submit' ? 'Confirm and Submit' : 'Confirm and ' + actionLabel;
    }

    function setSubmittingState(disableSubmitter) {
        state.isSubmitting = true;
        confirmButton.disabled = true;
        confirmButton.classList.add('btn-loading');
        confirmButton.textContent = 'Submitting...';
        cancelButton.disabled = true;
        dismissButton.disabled = true;

        if (disableSubmitter && state.submitter) {
            state.submitter.disabled = true;
            state.submitter.classList.add('btn-loading');
            if (!state.submitter.dataset.originalLabel) {
                state.submitter.dataset.originalLabel = state.submitter.tagName === 'INPUT' ? state.submitter.value : state.submitter.textContent;
            }

            if ('value' in state.submitter && state.submitter.tagName === 'INPUT') {
                state.submitter.value = 'Submitting...';
            } else {
                state.submitter.textContent = 'Submitting...';
            }
        }
    }

    function openModal(form, submitter) {
        var resolvedSubmitter = resolveSubmitter(form, submitter);

        state.form = form;
        state.submitter = resolvedSubmitter;
        state.restoreFocus = submitter || resolvedSubmitter || document.activeElement;
        state.isSubmitting = false;

        verifiedCheckbox.checked = false;
        confirmButton.disabled = true;
        confirmButton.classList.remove('btn-loading');
        cancelButton.disabled = false;
        dismissButton.disabled = false;

        updateModalCopy();
        renderSummary(collectSummaryItems(form));

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        window.setTimeout(function () {
            verifiedCheckbox.focus();
        }, 0);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        summaryContainer.innerHTML = '';
        verifiedCheckbox.checked = false;
        confirmButton.disabled = true;

        if (state.restoreFocus && typeof state.restoreFocus.focus === 'function') {
            state.restoreFocus.focus();
        }

        state.form = null;
        state.submitter = null;
        state.restoreFocus = null;
        state.isSubmitting = false;
    }

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!isEligibleForm(form)) {
            return;
        }

        if (form.dataset.confirmationSubmitted === 'true') {
            state.submitter = resolveSubmitter(form, event.submitter || state.submitter);
            setSubmittingState(true);
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var items = collectSummaryItems(form);
        if (!items.length) {
            return;
        }

        event.preventDefault();
        openModal(form, event.submitter || null);
    }, true);

    verifiedCheckbox.addEventListener('change', function () {
        confirmButton.disabled = !verifiedCheckbox.checked || state.isSubmitting;
    });

    confirmButton.addEventListener('click', function () {
        if (!state.form || !verifiedCheckbox.checked || state.isSubmitting) {
            return;
        }

        state.form.dataset.confirmationSubmitted = 'true';
        setSubmittingState(false);
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');

        if (typeof state.form.requestSubmit === 'function') {
            if (state.submitter) {
                state.form.requestSubmit(state.submitter);
            } else {
                state.form.requestSubmit();
            }
            return;
        }

        setSubmittingState(true);
        state.form.submit();
    });

    [cancelButton, dismissButton].forEach(function (button) {
        button.addEventListener('click', function () {
            if (state.isSubmitting) {
                return;
            }

            closeModal();
        });
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal && !state.isSubmitting) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (!modal.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape' && !state.isSubmitting) {
            event.preventDefault();
            closeModal();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        var focusableElements = Array.prototype.slice.call(modal.querySelectorAll(focusableSelector)).filter(function (element) {
            return !element.disabled && element.offsetParent !== null;
        });

        if (!focusableElements.length) {
            return;
        }

        var firstElement = focusableElements[0];
        var lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    });
}());
