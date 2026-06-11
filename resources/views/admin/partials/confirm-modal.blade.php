<!-- Action Confirmation Modal -->
<div class="modal fade" id="actionConfirmModal" tabindex="-1" aria-labelledby="actionConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" id="actionConfirmModalHeader">
                <h5 class="modal-title" id="actionConfirmModalLabel">
                    <i class="fa fa-exclamation-triangle me-2" id="actionConfirmModalIcon"></i>
                    <span id="actionConfirmModalTitle">Confirm Action</span>
                </h5>
                <button type="button" class="btn-close" id="actionConfirmModalClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="actionConfirmMessage"></p>
                <p class="text-danger small fw-semibold mt-2 mb-0 d-none" id="actionConfirmWarning"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="actionConfirmForm" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="_method" id="actionConfirmMethod" value="POST">
                    <button type="submit" class="btn" id="actionConfirmSubmitBtn">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.showActionConfirmModal = function(options) {
        const modalEl = document.getElementById('actionConfirmModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const header = document.getElementById('actionConfirmModalHeader');
        const closeBtn = document.getElementById('actionConfirmModalClose');
        const form = document.getElementById('actionConfirmForm');
        const methodInput = document.getElementById('actionConfirmMethod');
        const submitBtn = document.getElementById('actionConfirmSubmitBtn');
        const warningEl = document.getElementById('actionConfirmWarning');

        document.getElementById('actionConfirmModalTitle').textContent = options.title || 'Confirm Action';
        document.getElementById('actionConfirmMessage').textContent = options.message || 'Are you sure?';
        form.action = options.action || '#';
        methodInput.value = options.method || 'POST';

        const headerClass = options.headerClass || 'bg-danger text-white';
        header.className = 'modal-header ' + headerClass;
        closeBtn.className = headerClass.includes('text-white') ? 'btn-close btn-close-white' : 'btn-close';

        const btnClass = options.btnClass || 'btn-danger';
        submitBtn.className = 'btn ' + btnClass;
        submitBtn.innerHTML = options.btnHtml || '<i class="fa fa-check me-1"></i>Confirm';

        if (options.warning) {
            warningEl.textContent = options.warning;
            warningEl.classList.remove('d-none');
        } else {
            warningEl.classList.add('d-none');
        }

        modal.show();
    };
</script>
