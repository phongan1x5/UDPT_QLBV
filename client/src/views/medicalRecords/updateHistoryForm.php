<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-file-medical-alt"></i> Update Medical History</h1>
                    <p class="text-muted mb-0">Update patient medical history information</p>
                </div>
                <div>
                    <a href="<?php echo url('medical-records'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Medical Records
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer"></div>

    <?php if ($lookupStatus == 404): ?>
        <!-- 404 Error Display -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Medical Record Not Found
                        </h4>
                    </div>
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-medical fa-4x text-danger mb-4"></i>
                        <h5 class="text-danger mb-3">Error 404 - Record Not Found</h5>
                        <p class="text-muted mb-4">
                            The requested medical record could not be found in the system.
                            This could happen if:
                        </p>
                        <div class="text-start">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    The medical record ID is incorrect
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    The patient doesn't have a medical record yet
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    The record has been deleted or archived
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    You don't have permission to access this record
                                </li>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <a href="<?php echo url('medical-records'); ?>" class="btn btn-primary me-2">
                                <i class="fas fa-list"></i> View All Medical Records
                            </a>
                            <a href="<?php echo url('medical-records/search'); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Search Records
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($lookupStatus == 200 && isset($currentHistory)): ?>
        <!-- Medical History Update Form -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Current History Display -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Current Medical History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-file-medical fa-3x text-info mb-3"></i>
                                    <h6 class="text-info">Medical Record ID</h6>
                                    <h4 class="text-dark">#<?php echo htmlspecialchars($currentHistory['MaHSBA']); ?></h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                    <h6 class="text-primary">Patient ID</h6>
                                    <h4 class="text-dark">BN<?php echo htmlspecialchars($currentHistory['MaBenhNhan']); ?></h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-<?php echo $currentHistory['is_active'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> fa-3x mb-3"></i>
                                    <h6 class="<?php echo $currentHistory['is_active'] ? 'text-success' : 'text-danger'; ?>">Status</h6>
                                    <h4 class="text-dark"><?php echo $currentHistory['is_active'] ? 'Active' : 'Inactive'; ?></h4>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <h6 class="text-dark mb-2">
                                <i class="fas fa-history"></i> Current Medical History:
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <?php if (!empty($currentHistory['TienSu'])): ?>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($currentHistory['TienSu'])); ?></p>
                                <?php else: ?>
                                    <p class="text-muted mb-0"><em>No medical history recorded</em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Form -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Update Medical History
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="updateHistoryForm" class="needs-validation" novalidate>
                            <input type="hidden" id="recordId" value="<?php echo htmlspecialchars($currentHistory['MaHSBA']); ?>">
                            <input type="hidden" id="patientId" value="<?php echo htmlspecialchars($currentHistory['MaBenhNhan']); ?>">

                            <div class="mb-4">
                                <label for="medicalHistory" class="form-label">
                                    <i class="fas fa-notes-medical"></i> Medical History (Tiền Sử)
                                </label>
                                <textarea class="form-control"
                                    id="medicalHistory"
                                    name="medical_history"
                                    rows="6"
                                    placeholder="Enter patient's medical history, previous conditions, surgeries, allergies, etc."
                                    required><?php echo htmlspecialchars($currentHistory['TienSu']); ?></textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Include relevant medical history, allergies, previous surgeries, chronic conditions, etc.
                                </div>
                                <div class="invalid-feedback">
                                    Please provide medical history information.
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-success" id="updateBtn">
                                    <i class="fas fa-save"></i> Update Medical History
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Unknown Error -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-question-circle"></i> Unknown Error
                        </h4>
                    </div>
                    <div class="card-body text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                        <h5 class="mb-3">Something went wrong</h5>
                        <p class="text-muted mb-4">
                            An unexpected error occurred while loading the medical record.
                        </p>
                        <a href="<?php echo url('medical-records'); ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Medical Records
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (form.checkValidity() === true) {
                        updateMedicalHistory();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // Update medical history
    function updateMedicalHistory() {
        const updateBtn = document.getElementById('updateBtn');
        const originalText = updateBtn.innerHTML;

        // Get form data
        const recordId = document.getElementById('recordId').value;
        const patientId = document.getElementById('patientId').value;
        const medicalHistory = document.getElementById('medicalHistory').value;

        // Show loading
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        baseUrl = `<?php echo url('/medicalRecords/updateHistory'); ?>`
        updatedUrl = baseUrl + "/" + recordId

        // Make API call
        fetch(updatedUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    MaHSBA: recordId,
                    newMedicalHistory: medicalHistory
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 || data.success) {
                    showMessage('Medical history updated successfully!', 'success');
                    // Update current history display
                    updateCurrentHistoryDisplay(medicalHistory);
                } else {
                    throw new Error(data.message || 'Failed to update medical history');
                }
            })
            .catch(error => {
                console.error('Error updating medical history:', error);
                showMessage('Failed to update medical history. Please try again.', 'danger');
            })
            .finally(() => {
                // Restore button
                updateBtn.disabled = false;
                updateBtn.innerHTML = originalText;
            });
    }

    // Update current history display
    function updateCurrentHistoryDisplay(newHistory) {
        const currentHistoryDiv = document.querySelector('.bg-light.p-3.rounded');
        if (currentHistoryDiv) {
            currentHistoryDiv.innerHTML = newHistory ?
                `<p class="mb-0">${newHistory.replace(/\n/g, '<br>')}</p>` :
                `<p class="text-muted mb-0"><em>No medical history recorded</em></p>`;
        }
    }

    // Reset form
    function resetForm() {
        document.getElementById('medicalHistory').value = '<?php echo addslashes($currentHistory['TienSu'] ?? ''); ?>';
        document.getElementById('updateHistoryForm').classList.remove('was-validated');
    }

    // Show messages
    function showMessage(message, type = 'info') {
        const messageContainer = document.getElementById('messageContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        messageContainer.appendChild(alertDiv);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);

        // Scroll to message
        messageContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    textarea {
        resize: vertical;
    }

    @media print {

        .btn,
        .no-print {
            display: none !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Update Medical History - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>