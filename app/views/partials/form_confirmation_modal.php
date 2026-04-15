<div
    id="globalFormConfirmationModal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-modal="true"
    aria-labelledby="globalFormConfirmationTitle"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 id="globalFormConfirmationTitle" class="modal-title">Review submission details</h2>
                    <p class="modal-subtitle">Check the information carefully before posting this record.</p>
                </div>
                <button type="button" class="btn-close" data-confirm-dismiss aria-label="Close review dialog">&times;</button>
            </div>
            <div class="modal-body">
                <div id="globalFormConfirmationSummary" class="confirm-summary" aria-live="polite"></div>
            </div>
            <div class="modal-footer">
                <div class="confirm-verification">
                    <label for="globalFormConfirmationVerified">
                        <input id="globalFormConfirmationVerified" type="checkbox">
                        <span>I verified that the information above is complete, accurate, and ready for submission.</span>
                    </label>
                    <p>The final submit button stays disabled until this acknowledgment is checked.</p>
                </div>
                <div class="btn-row">
                    <button type="button" class="btn-secondary" data-confirm-cancel>Back to Edit</button>
                    <button type="button" data-confirm-submit disabled>Confirm and Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
