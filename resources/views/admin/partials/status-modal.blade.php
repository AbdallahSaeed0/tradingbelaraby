<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">{{ custom_trans('Change Status', 'admin') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="statusModalMessage">{{ custom_trans('Select the new status:', 'admin') }}</p>
                <div class="mb-3">
                    <label for="statusSelect" class="form-label">{{ custom_trans('Status', 'admin') }}</label>
                    <select class="form-select" id="statusSelect">
                        <!-- Options will be populated dynamically -->
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">{{ custom_trans('Update Status', 'admin') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global status change function
    window.showStatusModal = function(itemId, currentStatus, availableStatuses, updateUrl) {
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        const statusSelect = document.getElementById('statusSelect');
        const confirmBtn = document.getElementById('confirmStatusChange');
        
        // Clear previous options
        statusSelect.innerHTML = '';
        
        // Populate status options
        availableStatuses.forEach(status => {
            const option = document.createElement('option');
            option.value = status.value;
            option.textContent = status.label;
            if (status.value === currentStatus) {
                option.selected = true;
            }
            statusSelect.appendChild(option);
        });
        
        // Remove previous event listeners by cloning the button
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Add click handler
        document.getElementById('confirmStatusChange').addEventListener('click', function() {
            const newStatus = statusSelect.value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }
            
            // Update button to show loading state
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>{{ custom_trans('Updating...', 'admin') }}';
            
            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => {
                if (response.ok) {
                    return response.json().catch(() => ({}));
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .then(data => {
                statusModal.hide();
                // Reload page to show updated status
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ custom_trans('Error updating status. Please try again.', 'admin') }}');
                this.disabled = false;
                this.innerHTML = '{{ custom_trans('Update Status', 'admin') }}';
            });
        });
        
        statusModal.show();
    };
</script>

