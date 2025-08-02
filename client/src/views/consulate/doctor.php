<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
echo '<pre>';
print_r($medicalRecord['MedicalRecord']['MaGiayKhamBenh']);
echo '</pre>';
$hasPendingLabServices = false;
foreach ($currentLabServices as $service) {
    if (empty($service['KetQua'])) {
        $hasPendingLabServices = true;
        break; // Exit loop early since we found at least one pending service
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1><i class="fas fa-stethoscope"></i> Doctor Consultation</h1>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Doctor Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md"></i> Doctor Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($doctor['HoTen']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Doctor ID:</strong></div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($doctor['MaNhanVien']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Specialization:</strong></div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($doctor['ChuyenKhoa']); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4"><strong>Department ID:</strong></div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($doctor['IDPhongBan']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Patient ID:</strong></div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($patient['id']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($patient['HoTen']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Date of Birth:</strong></div>
                        <div class="col-sm-8"><?php echo date('M j, Y', strtotime($patient['NgaySinh'])); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Gender:</strong></div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($patient['GioiTinh']); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4"><strong>Health Insurance:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge bg-success"><?php echo htmlspecialchars($patient['BaoHiemYTe']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Consultation Session</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Date:</strong> <?php echo date('Y-m-d'); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Time:</strong> <?php echo date('H:i'); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong> <span class="badge bg-warning">In Progress</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Patient Age:</strong>
                            <?php
                            $birthDate = new DateTime($patient['NgaySinh']);
                            $today = new DateTime();
                            $age = $today->diff($birthDate)->y;
                            echo $age . ' years old';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lab Services Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-flask"></i> Lab Services
                        <span class="badge bg-light text-dark ms-2" id="serviceCount"><?php echo count($currentLabServices); ?></span>
                    </h5>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#labServiceModal">
                        <i class="fas fa-plus"></i> Request Lab Service
                    </button>
                </div>
                <div class="card-body">
                    <div id="currentLabServices">
                        <?php if (empty($currentLabServices)): ?>
                            <div class="text-muted text-center py-3" id="noServicesMessage">
                                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                <p>No lab services requested yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($currentLabServices as $service): ?>
                                <div class="border rounded p-3 mb-3" data-service-id="<?php echo $service['MaDVSD']; ?>">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($service['Service']['TenDichVu']); ?></h6>
                                            <p class="mb-1 text-muted small"><?php echo htmlspecialchars($service['Service']['NoiDungDichVu']); ?></p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-info me-2">$<?php echo number_format($service['Service']['DonGia'], 2); ?></span>
                                                <small class="text-muted">
                                                    Requested: <?php echo date('M j, Y H:i', strtotime($service['ThoiGian'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <?php if (empty($service['KetQua'])): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Completed</span>
                                                <?php if (!empty($service['FileKetQua'])): ?>
                                                    <br><small><a href="<?php echo $service['FileKetQua']; ?>" target="_blank" class="text-decoration-none">
                                                            <i class="fas fa-file-alt"></i> View Results
                                                        </a></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Record & Prescription Section - Only show if no pending lab services -->
    <?php if (!$hasPendingLabServices): ?>
        <div class="row mt-4">
            <!-- Medical Record -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-medical"></i> Medical Record</h5>
                    </div>
                    <div class="card-body">
                        <form id="medicalRecordForm">
                            <input type="hidden" name="record_id" value="<?php echo $medicalRecord['MedicalRecord']['MaGiayKhamBenh'] ?? ''; ?>">

                            <div class="mb-3">
                                <label for="diagnosis" class="form-label"><strong>Diagnosis *</strong></label>
                                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"
                                    placeholder="Enter diagnosis..." required><?php echo htmlspecialchars($medicalRecord['MedicalRecord']['ChanDoan'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label"><strong>Doctor's Notes</strong></label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"
                                    placeholder="Enter notes and recommendations..."><?php echo htmlspecialchars($medicalRecord['MedicalRecord']['LuuY'] ?? ''); ?></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Medicines Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-pills"></i> Prescribed Medicines
                            <span class="badge bg-light text-dark ms-2" id="medicineCount">0</span>
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#medicineModal">
                            <i class="fas fa-plus"></i> Add Medicine
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="prescribedMedicines">
                            <div class="text-muted text-center py-3" id="noMedicinesMessage">
                                <i class="fas fa-pills fa-2x mb-2"></i>
                                <p>No medicines prescribed yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Prescription Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-prescription"></i> Complete Prescription</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-3">
                            <i class="fas fa-info-circle"></i>
                            Review the diagnosis, notes, and prescribed medicines before submitting the prescription.
                        </p>
                        <button type="button" class="btn btn-success btn-lg" id="submitPrescriptionBtn">
                            <i class="fas fa-check-circle"></i> Submit Prescription
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg ms-3" onclick="window.history.back()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Message when there are pending lab services -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-clock"></i> Waiting for Lab Results</h6>
                    <p class="mb-0">
                        Cannot complete prescription while lab services are pending. Please wait for all lab results before proceeding with the prescription.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Lab Service Request Modal -->
<div class="modal fade" id="labServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-flask"></i> Request Lab Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- CHANGED: Regular form with POST method -->
                <form id="labServiceForm" method="POST" action="<?php echo url('consultation/request-lab-service'); ?>">
                    <!-- Hidden field for medical record ID -->
                    <input type="hidden" name="MaGiayKhamBenh" value="<?php echo $medicalRecord['MedicalRecord']['MaGiayKhamBenh'] ?? ''; ?>">

                    <div class="mb-3">
                        <label for="labServiceSelect" class="form-label">Select Lab Service *</label>
                        <select class="form-select" id="labServiceSelect" name="MaDichVu" required>
                            <option value="">Choose a service...</option>
                            <?php foreach ($labServices as $service): ?>
                                <?php
                                // Check if this service is already requested
                                $alreadyRequested = false;
                                foreach ($currentLabServices as $currentService) {
                                    if ($currentService['MaDichVu'] == $service['MaDichVu']) {
                                        $alreadyRequested = true;
                                        break;
                                    }
                                }
                                ?>
                                <option value="<?php echo $service['MaDichVu']; ?>"
                                    data-name="<?php echo htmlspecialchars($service['TenDichVu']); ?>"
                                    data-description="<?php echo htmlspecialchars($service['NoiDungDichVu']); ?>"
                                    data-price="<?php echo $service['DonGia']; ?>"
                                    <?php echo $alreadyRequested ? 'disabled' : ''; ?>>
                                    <?php echo htmlspecialchars($service['TenDichVu']); ?> - $<?php echo number_format($service['DonGia'], 2); ?>
                                    <?php echo $alreadyRequested ? ' (Already Requested)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="serviceDescription" class="form-label">Service Description</label>
                        <textarea class="form-control" id="serviceDescription" rows="3" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="servicePrice" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control" id="servicePrice" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="serviceRequest" class="form-label">Additional Requests</label>
                        <textarea class="form-control" id="serviceRequest" name="YeuCauCuThe" rows="2"
                            placeholder="Any special instructions or notes for this lab service..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="labServiceForm" class="btn btn-success" id="requestServiceBtn" disabled>
                    <i class="fas fa-paper-plane"></i> Request Service
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Medicine Modal -->
<div class="modal fade" id="medicineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pills"></i> Add Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="medicineForm">
                    <div class="mb-3">
                        <label for="medicineSelect" class="form-label">Select Medicine *</label>
                        <select class="form-select" id="medicineSelect" required>
                            <option value="">Choose a medicine...</option>
                            <?php foreach ($medicines as $medicine): ?>
                                <option value="<?php echo $medicine['MaThuoc']; ?>"
                                    data-name="<?php echo htmlspecialchars($medicine['TenThuoc']); ?>"
                                    data-unit="<?php echo htmlspecialchars($medicine['DonViTinh']); ?>"
                                    data-indication="<?php echo htmlspecialchars($medicine['ChiDinh']); ?>"
                                    data-price="<?php echo $medicine['GiaTien']; ?>"
                                    data-stock="<?php echo $medicine['SoLuongTonKho']; ?>">
                                    <?php echo htmlspecialchars($medicine['TenThuoc']); ?> (<?php echo $medicine['DonViTinh']; ?>) - $<?php echo $medicine['GiaTien']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="medicineIndication" class="form-label">Indication</label>
                        <input type="text" class="form-control" id="medicineIndication" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity *</label>
                                <input type="number" class="form-control" id="quantity" required min="1"
                                    placeholder="e.g., 30">
                                <div class="form-text">Available: <span id="availableStock">-</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="medicineInstructions" class="form-label">Instructions</label>
                        <textarea class="form-control" id="medicineInstructions" rows="2"
                            placeholder="Take with food, before meals, etc..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="addMedicineBtn" disabled>
                    <i class="fas fa-plus"></i> Add Medicine
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let prescribedMedicines = [];
    let prescribedMedicinesInfo = []; // This is information that is not using to create ThuocTheoToa

    document.addEventListener('DOMContentLoaded', function() {
        const labServiceSelect = document.getElementById('labServiceSelect');
        const serviceDescription = document.getElementById('serviceDescription');
        const servicePrice = document.getElementById('servicePrice');
        const requestServiceBtn = document.getElementById('requestServiceBtn');

        // Handle lab service selection
        labServiceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value) {
                serviceDescription.value = selectedOption.dataset.description || '';
                servicePrice.value = parseFloat(selectedOption.dataset.price || 0).toFixed(2);
                requestServiceBtn.disabled = false;
            } else {
                serviceDescription.value = '';
                servicePrice.value = '';
                requestServiceBtn.disabled = true;
            }
        });

        // CHANGED: Form submit handler instead of fetch
        document.getElementById('labServiceForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            const selectedService = labServiceSelect.options[labServiceSelect.selectedIndex];

            if (!formData.get('MaDichVu') || !formData.get('MaGiayKhamBenh')) {
                alert('Please select a service and ensure medical record is available.');
                return;
            }

            // Disable button and show loading
            requestServiceBtn.disabled = true;
            requestServiceBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Requesting...';

            // CHANGED: Use FormData and fetch with form data
            fetch(this.action, {
                    method: 'POST',
                    body: formData // Send as form data instead of JSON
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Add the new service to the current display

                        showAlert('success', 'Lab service requested successfully!');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500); // 1.5 seconds delay

                    } else {
                        showAlert('danger', 'Failed to request lab service: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while requesting the service.');
                })
                .finally(() => {
                    // Re-enable button
                    requestServiceBtn.disabled = false;
                    requestServiceBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Service';
                });
        });

        // Medicine modal handlers
        const medicineSelect = document.getElementById('medicineSelect');
        const addMedicineBtn = document.getElementById('addMedicineBtn');

        if (medicineSelect) {
            medicineSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                if (this.value) {
                    document.getElementById('medicineIndication').value = selectedOption.dataset.indication || '';
                    document.getElementById('availableStock').textContent = selectedOption.dataset.stock || 0;
                    addMedicineBtn.disabled = false;
                } else {
                    document.getElementById('medicineIndication').value = '';
                    document.getElementById('availableStock').textContent = '-';
                    addMedicineBtn.disabled = true;
                }
            });

            addMedicineBtn.addEventListener('click', addMedicine);
        }

        // Submit prescription handler
        const submitBtn = document.getElementById('submitPrescriptionBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', submitPrescription);
        }
    });

    function addMedicine() {
        const medicineSelect = document.getElementById('medicineSelect');
        const selectedOption = medicineSelect.options[medicineSelect.selectedIndex];
        const quantity = parseInt(document.getElementById('quantity').value);
        const instructions = document.getElementById('medicineInstructions').value.trim();

        if (!medicineSelect.value || !quantity) {
            showAlert('warning', 'Please fill in all required medicine fields.');
            return;
        }

        const availableStock = parseInt(selectedOption.dataset.stock);
        if (quantity > availableStock) {
            showAlert('warning', `Requested quantity (${quantity}) exceeds available stock (${availableStock}).`);
            return;
        }

        const medicine = {
            MaThuoc: selectedOption.value,
            SoLuong: quantity,
            GhiChu: instructions
        };

        const medicineInfo = {
            id: selectedOption.value,
            name: selectedOption.dataset.name,
            unit: selectedOption.dataset.unit,
            quantity: quantity,
            instructions: instructions,
            price: parseFloat(selectedOption.dataset.price)
        };

        prescribedMedicines.push(medicine);
        prescribedMedicinesInfo.push(medicineInfo)

        displayMedicine(medicineInfo);
        updateMedicineCount();

        // Reset form and close modal
        document.getElementById('medicineForm').reset();
        document.getElementById('medicineIndication').value = '';
        document.getElementById('availableStock').textContent = '-';
        bootstrap.Modal.getInstance(document.getElementById('medicineModal')).hide();

        showAlert('success', 'Medicine added to prescription!');
    }

    function displayMedicine(medicine) {
        const container = document.getElementById('prescribedMedicines');
        const noMedicinesMessage = document.getElementById('noMedicinesMessage');

        if (prescribedMedicines.length === 1) {
            if (noMedicinesMessage) noMedicinesMessage.remove();
        }

        const medicineDiv = document.createElement('div');
        medicineDiv.className = 'border rounded p-3 mb-2';
        medicineDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">${medicine.name} (${medicine.unit})</h6>
                    <p class="mb-1" <strong>Quantity:</strong> ${medicine.quantity}</p>
                    ${medicine.instructions ? `<p class="mb-1 text-muted"><em>${medicine.instructions}</em></p>` : ''}
                    <span class="badge bg-success">$${(medicine.price * medicine.quantity).toFixed(2)}</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMedicine(this, '${medicine.id}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        container.appendChild(medicineDiv);
    }

    function removeMedicine(button, medicineId) {
        prescribedMedicines = prescribedMedicines.filter(medicine => medicine.MaThuoc !== medicineId);
        prescribedMedicinesInfo = prescribedMedicinesInfo.filter(medicine => medicine.id !== medicineId);
        button.closest('.border').remove();
        updateMedicineCount();

        if (prescribedMedicines.length === 0) {
            document.getElementById('prescribedMedicines').innerHTML = `
                <div class="text-muted text-center py-3" id="noMedicinesMessage">
                    <i class="fas fa-pills fa-2x mb-2"></i>
                    <p>No medicines prescribed yet.</p>
                </div>
            `;
        }
    }

    function updateMedicineCount() {
        const countElement = document.getElementById('medicineCount');
        if (countElement) {
            countElement.textContent = prescribedMedicines.length;
        }
    }

    function submitPrescription() {
        const diagnosis = document.getElementById('diagnosis').value.trim();
        const notes = document.getElementById('notes').value.trim();
        const recordId = document.querySelector('input[name="record_id"]').value;

        if (!diagnosis) {
            showAlert('warning', 'Please enter a diagnosis before submitting the prescription.');
            return;
        }

        const submitBtn = document.getElementById('submitPrescriptionBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        // CHANGED: Use FormData instead of JSON
        const formData = new FormData();
        formData.append('MaGiayKhamBenh', recordId);
        formData.append('ChanDoan', diagnosis);
        formData.append('LuuY', notes);
        formData.append('medicines', JSON.stringify(prescribedMedicines));

        fetch("<?php echo url('consultation/submit-prescription'); ?>", {
                method: 'POST',
                body: formData // Send as form data instead of JSON
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Prescription submitted successfully!');
                    setTimeout(() => {
                        window.location.href = "<?php echo url('dashboard'); ?>";
                    }, 2000);
                } else {
                    showAlert('danger', 'Failed to submit prescription: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while submitting the prescription.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Submit Prescription';
            });
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';

        alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
</script>

<style>
    .position-fixed {
        position: fixed !important;
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Doctor Consultation - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>