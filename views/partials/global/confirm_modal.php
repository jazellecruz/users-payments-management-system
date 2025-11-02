<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-body-tertiary">
                <p class="modal-title fs-6 fw-bold">CONFIRM ACTION</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="" id="confirmActionMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmActionModalBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    const showConfirmModal = (msg) => {
        const confirmModalMsgEl = document.getElementById('confirmActionMessage');
        const confirmActionModalBtn = document.getElementById('confirmActionModalBtn');
        
        return new Promise((resolve) => {
            confirmModalMsgEl.textContent = msg;
            confirmModal.show();

            const handleConfirm = () => {
                // this is necessary!! clean up first to remove previous listeners
                confirmActionModalBtn.removeEventListener('click', handleConfirm);
                resolve(true);
            };

            confirmActionModalBtn.addEventListener('click', handleConfirm);
        });
    };

    const hideConfirmModal = () => {
        confirmModal.hide();
    };
</script>