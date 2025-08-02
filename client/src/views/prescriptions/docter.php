<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-prescription"></i> Create Prescription</h1>
                <div>
                    <button class="btn btn-secondary" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i> Back to Consultation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Patient & Appointment Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5"><strong>Patient ID:</strong></div>
                        <div class="col-7"><?php echo htmlspecialchars($appointment['MaBenhNhan']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Date:</strong></div>
                        <div class="col-7"><?php echo date('M j, Y', strtotime($appointment['Ngay'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Time:</strong></div>
                        <div class="col-7"><?php echo date('g:i A', strtotime($appointment['Gio'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Type:</strong></div>
                        <div class="col-7">
                            <span class="badge bg-info"><?php echo htmlspecialchars($appointment['LoaiLichHen']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescription Form -->
        <div class="col-md-8">
            <form id="prescriptionForm">
                <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                <input type="hidden" name="medical_record_id" value="<?php echo $medical_record_id; ?>">
                <input type="hidden" name="patient_id" value="<?php echo $appointment['MaBenhNhan']; ?>">

                <!-- Diagnosis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-notes-medical"></i> Diagnosis & Notes</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Primary Diagnosis *</label>
                            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"
                                placeholder="Enter primary diagnosis..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="symptoms" class="form-label">Symptoms</label>
                            <textarea class="form-control" id="symptoms" name="symptoms" rows="2"
                                placeholder="Patient reported symptoms..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Doctor's Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                placeholder="Additional notes and observations..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Lab Services -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-flask"></i> Lab Services</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addLabService">
                            <i class="fas fa-plus"></i> Add Lab Test
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="labServicesContainer">
                            <p class="text-muted">No lab services added yet. Click "Add Lab Test" to add services.</p>
                        </div>
                    </div>
                </div>

                <!-- Medications -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-pills"></i> Medications</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addMedication">
                            <i class="fas fa-plus"></i> Add Medication
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="medicationsContainer">
                            <p class="text-muted">No medications added yet. Click "Add Medication" to prescribe medicines.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check"></i> Submit Prescription
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg ms-3" onclick="window.history.back()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lab Service Modal -->
<div class="modal fade" id="labServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Lab Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="labServiceForm">
                    <div class="mb-3">
                        <label for="labServiceType" class="form-label">Lab Service Type *</label>
                        <select class="form-select" id="labServiceType" required>
                            <option value="">Select lab service...</option>
                            <option value="blood_test">Blood Test</option>
                            <option value="urine_test">Urine Test</option>
                            <option value="x_ray">X-Ray</option>
                            <option value="ct_scan">CT Scan</option>
                            <option value="mri">MRI</option>
                            <option value="ultrasound">Ultrasound</option>
                            <option value="ecg">ECG</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="labServiceNotes" class="form-label">Instructions</label>
                        <textarea class="form-control" id="labServiceNotes" rows="2"
                            placeholder="Special instructions for the lab..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="labUrgency" class="form-label">Urgency</label>
                        <select class="form-select" id="labUrgency">
                            <option value="routine">Routine</option>
                            <option value="urgent">Urgent</option>
                            <option value="stat">STAT</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveLabService">Add Service</button>
            </div>
        </div>
    </div>
</div>

<!-- Medication Modal -->
<div class="modal fade" id="medicationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Medication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="medicationForm">
                    <div class="mb-3">
                        <label for="medicationName" class="form-label">Medication Name *</label>
                        <input type="text" class="form-control" id="medicationName" required
                            placeholder="Enter medication name...">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dosage" class="form-label">Dosage *</label>
                                <input type="text" class="form-control" id="dosage" required
                                    placeholder="e.g., 500mg">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="frequency" class="form-label">Frequency *</label>
                                <select class="form-select" id="frequency" required>
                                    <option value="">Select frequency...</option>
                                    <option value="once_daily">Once daily</option>
                                    <option value="twice_daily">Twice daily</option>
                                    <option value="three_times_daily">Three times daily</option>
                                    <option value="four_times_daily">Four times daily</option>
                                    <option value="as_needed">As needed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration"
                                    placeholder="e.g., 7 days">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity"
                                    placeholder="e.g., 30">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="medicationInstructions" class="form-label">Instructions</label>
                        <textarea class="form-control" id="medicationInstructions" rows="2"
                            placeholder="Take with food, before meals, etc..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveMedication">Add Medication</button>
            </div>
        </div>
    </div>
</div>

<script>
    let labServiceCounter = 0;
    let medicationCounter = 0;

    document.addEventListener('DOMContentLoaded', function() {
        // Add lab service
        document.getElementById('addLabService').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('labServiceModal'));
            modal.show();
        });

        // Add medication
        document.getElementById('addMedication').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('medicationModal'));
            modal.show();
        });

        // Save lab service
        document.getElementById('saveLabService').addEventListener('click', function() {
            const form = document.getElementById('labServiceForm');
            const formData = new FormData(form);

            const serviceType = document.getElementById('labServiceType').value;
            const notes = document.getElementById('labServiceNotes').value;
            const urgency = document.getElementById('labUrgency').value;

            if (!serviceType) {
                alert('Please select a lab service type.');
                return;
            }

            addLabServiceToList(serviceType, notes, urgency);

            // Reset form and close modal
            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('labServiceModal')).hide();
        });

        // Save medication
        document.getElementById('saveMedication').addEventListener('click', function() {
            const form = document.getElementById('medicationForm');

            const name = document.getElementById('medicationName').value;
            const dosage = document.getElementById('dosage').value;
            const frequency = document.getElementById('frequency').value;
            const duration = document.getElementById('duration').value;
            const quantity = document.getElementById('quantity').value;
            const instructions = document.getElementById('medicationInstructions').value;

            if (!name || !dosage || !frequency) {
                alert('Please fill in required fields (name, dosage, frequency).');
                return;
            }

            addMedicationToList(name, dosage, frequency, duration, quantity, instructions);

            // Reset form and close modal
            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('medicationModal')).hide();
        });

        // Handle prescription form submission
        document.getElementById('prescriptionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitPrescription();
        });
    });

    function addLabServiceToList(serviceType, notes, urgency) {
        const container = document.getElementById('labServicesContainer');

        // Remove "no services" message if this is the first service
        if (labServiceCounter === 0) {
            container.innerHTML = '';
        }

        labServiceCounter++;

        const serviceDiv = document.createElement('div');
        serviceDiv.className = 'border rounded p-3 mb-3';
        serviceDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="mb-1">${formatLabServiceType(serviceType)}</h6>
                ${notes ? `<p class="mb-1 text-muted">${notes}</p>` : ''}
                <span class="badge bg-${urgency === 'stat' ? 'danger' : urgency === 'urgent' ? 'warning' : 'secondary'}">${urgency.toUpperCase()}</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLabService(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <input type="hidden" name="lab_services[]" value='${JSON.stringify({type: serviceType, notes: notes, urgency: urgency})}'>
    `;

        container.appendChild(serviceDiv);
    }

    function addMedicationToList(name, dosage, frequency, duration, quantity, instructions) {
        const container = document.getElementById('medicationsContainer');

        // Remove "no medications" message if this is the first medication
        if (medicationCounter === 0) {
            container.innerHTML = '';
        }

        medicationCounter++;

        const medicationDiv = document.createElement('div');
        medicationDiv.className = 'border rounded p-3 mb-3';
        medicationDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="mb-1">${name}</h6>
                <p class="mb-1"><strong>Dosage:</strong> ${dosage} | <strong>Frequency:</strong> ${formatFrequency(frequency)}</p>
                ${duration ? `<p class="mb-1"><strong>Duration:</strong> ${duration}</p>` : ''}
                ${quantity ? `<p class="mb-1"><strong>Quantity:</strong> ${quantity}</p>` : ''}
                ${instructions ? `<p class="mb-1 text-muted"><em>${instructions}</em></p>` : ''}
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMedication(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <input type="hidden" name="medications[]" value='${JSON.stringify({name: name, dosage: dosage, frequency: frequency, duration: duration, quantity: quantity, instructions: instructions})}'>
    `;

        container.appendChild(medicationDiv);
    }

    function removeLabService(button) {
        button.closest('.border').remove();
        labServiceCounter--;

        if (labServiceCounter === 0) {
            document.getElementById('labServicesContainer').innerHTML =
                '<p class="text-muted">No lab services added yet. Click "Add Lab Test" to add services.</p>';
        }
    }

    function removeMedication(button) {
        button.closest('.border').remove();
        medicationCounter--;

        if (medicationCounter === 0) {
            document.getElementById('medicationsContainer').innerHTML =
                '<p class="text-muted">No medications added yet. Click "Add Medication" to prescribe medicines.</p>';
        }
    }

    function formatLabServiceType(type) {
        const types = {
            'blood_test': 'Blood Test',
            'urine_test': 'Urine Test',
            'x_ray': 'X-Ray',
            'ct_scan': 'CT Scan',
            'mri': 'MRI',
            'ultrasound': 'Ultrasound',
            'ecg': 'ECG',
            'other': 'Other'
        };
        return types[type] || type;
    }

    function formatFrequency(frequency) {
        const frequencies = {
            'once_daily': 'Once daily',
            'twice_daily': 'Twice daily',
            'three_times_daily': 'Three times daily',
            'four_times_daily': 'Four times daily',
            'as_needed': 'As needed'
        };
        return frequencies[frequency] || frequency;
    }

    function submitPrescription() {
        const form = document.getElementById('prescriptionForm');
        const formData = new FormData(form);

        // Validate required fields
        const diagnosis = formData.get('diagnosis');
        if (!diagnosis.trim()) {
            alert('Please enter a diagnosis.');
            return;
        }

        // Collect all form data
        const prescriptionData = {
            appointment_id: formData.get('appointment_id'),
            medical_record_id: formData.get('medical_record_id'),
            patient_id: formData.get('patient_id'),
            diagnosis: diagnosis,
            symptoms: formData.get('symptoms'),
            notes: formData.get('notes'),
            lab_services: [],
            medications: []
        };

        // Collect lab services
        const labServiceInputs = document.querySelectorAll('input[name="lab_services[]"]');
        labServiceInputs.forEach(input => {
            prescriptionData.lab_services.push(JSON.parse(input.value));
        });

        // Collect medications
        const medicationInputs = document.querySelectorAll('input[name="medications[]"]');
        medicationInputs.forEach(input => {
            prescriptionData.medications.push(JSON.parse(input.value));
        });

        // Show loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        // Submit prescription
        fetch('/consultation/submit-prescription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(prescriptionData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Prescription submitted successfully!');
                    window.location.href = '/consultation';
                } else {
                    alert('Failed to submit prescription: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the prescription.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    }
</script>

<style>
    .border {
        border: 1px solid #dee2e6 !important;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .badge {
        font-size: 0.75em;
    }

    .text-muted {
        font-style: italic;
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Create Prescription - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>