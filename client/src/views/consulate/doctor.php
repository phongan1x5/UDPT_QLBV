<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Determine if any lab services are still pending
$hasPendingLabServices = false;
foreach ($currentLabServices as $service) {
	if (empty($service['KetQua'])) {
		$hasPendingLabServices = true;
		break;
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
												<small class="text-muted">Requested: <?php echo date('M j, Y H:i', strtotime($service['ThoiGian'])); ?></small>
											</div>
										</div>
										<div class="col-md-4 text-end">
											<?php if ($service['TrangThai'] === "ChoThuTien"): ?>
												<button
													class="badge bg-danger border-0"
													onclick="removeService('<?php echo url('consultation/remove-lab-service/' . $service['MaDVSD']); ?>')">
													Remove
												</button>
											<?php endif; ?>
											<?php if (empty($service['KetQua'])): ?>
												<span class="badge bg-warning">Pending</span>
											<?php else: ?>
												<span class="badge bg-success">Completed</span>
												<?php if (!empty($service['FileKetQua'])): ?>
													<br>
													<small>
														<a href="<?php echo $service['FileKetQua']; ?>" target="_blank" class="text-decoration-none">
															<i class="fas fa-file-alt"></i> View Results
														</a>
													</small>
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

	<!-- Medical Record & Prescription Section -->
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
						<input type="hidden" name="appointment_id" value="<?php echo $appointmentId ?? ''; ?>">

						<div class="mb-3">
							<label for="diagnosis" class="form-label"><strong>Diagnosis *</strong></label>
							<textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" placeholder="Enter diagnosis..." required><?php
								$currentDiagnosis = $medicalRecord['MedicalRecord']['ChanDoan'] ?? '';
								echo $currentDiagnosis === '@Pending examination' ? '' : htmlspecialchars($currentDiagnosis);
							?></textarea>
							<div class="form-text text-muted">
								<i class="fas fa-info-circle"></i> The "@" character is not allowed in diagnosis.
							</div>
                        </div>

						<div class="mb-3">
							<label for="notes" class="form-label"><strong>Doctor's Notes</strong></label>
							<textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Enter notes and recommendations..."><?php echo htmlspecialchars($medicalRecord['MedicalRecord']['LuuY'] ?? ''); ?></textarea>
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
	<!-- Pending Lab Results Message -->
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
				<form id="labServiceForm" method="POST" action="<?php echo url('consultation/request-lab-service'); ?>">
					<input type="hidden" name="MaGiayKhamBenh" value="<?php echo $medicalRecord['MedicalRecord']['MaGiayKhamBenh'] ?? ''; ?>">

					<div class="mb-3">
						<label for="labServiceSelect" class="form-label">Select Lab Service *</label>
						<select class="form-select" id="labServiceSelect" name="MaDichVu" required>
							<option value="">Choose a service...</option>
							<?php foreach ($labServices as $service): ?>
								<?php
									$alreadyRequested = false;
									foreach ($currentLabServices as $currentService) {
										if ($currentService['MaDichVu'] == $service['MaDichVu']) {
											$alreadyRequested = true;
											break;
										}
									}
								?>
								<option
									value="<?php echo $service['MaDichVu']; ?>"
									data-name="<?php echo htmlspecialchars($service['TenDichVu']); ?>"
									data-description="<?php echo htmlspecialchars($service['NoiDungDichVu']); ?>"
									data-price="<?php echo $service['DonGia']; ?>"
									<?php echo $alreadyRequested ? 'disabled' : ''; ?>
								>
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
						<textarea class="form-control" id="serviceRequest" name="YeuCauCuThe" rows="2" placeholder="Any special instructions or notes for this lab service..."></textarea>
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
					<!-- Selection Method Tabs -->
					<ul class="nav nav-tabs mb-3" id="medicineSelectionTabs" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="dropdown-tab" data-bs-toggle="tab" data-bs-target="#dropdown-pane" type="button" role="tab">
								<i class="fas fa-list"></i> Select from List
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="search-tab" data-bs-toggle="tab" data-bs-target="#search-pane" type="button" role="tab">
								<i class="fas fa-search"></i> Search Medicine
							</button>
						</li>
					</ul>

					<div class="tab-content" id="medicineSelectionContent">
						<!-- Dropdown Selection Tab -->
						<div class="tab-pane fade show active" id="dropdown-pane" role="tabpanel">
							<div class="mb-3">
								<label for="medicineSelect" class="form-label">Select Medicine *</label>
								<select class="form-select" id="medicineSelect">
									<option value="">Choose a medicine...</option>
									<?php foreach ($medicines as $medicine): ?>
										<option
											value="<?php echo $medicine['MaThuoc']; ?>"
											data-name="<?php echo htmlspecialchars($medicine['TenThuoc']); ?>"
											data-unit="<?php echo htmlspecialchars($medicine['DonViTinh']); ?>"
											data-indication="<?php echo htmlspecialchars($medicine['ChiDinh']); ?>"
											data-price="<?php echo $medicine['GiaTien']; ?>"
											data-stock="<?php echo $medicine['SoLuongTonKho']; ?>"
										>
											<?php echo htmlspecialchars($medicine['TenThuoc']); ?> (<?php echo $medicine['DonViTinh']; ?>) - $<?php echo $medicine['GiaTien']; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<!-- Search Tab -->
						<div class="tab-pane fade" id="search-pane" role="tabpanel">
							<div class="mb-3">
								<label for="medicineSearch" class="form-label">Search Medicine by Name *</label>
								<div class="input-group">
									<input type="text" class="form-control" id="medicineSearch" placeholder="Type medicine name (e.g., paracetamol, ibuprofen...)">
									<button class="btn btn-outline-primary" type="button" id="searchMedicineBtn">
										<i class="fas fa-search"></i> Search
									</button>
								</div>
								<div class="form-text">
									<i class="fas fa-info-circle"></i> Search requires at least 2 characters
								</div>
							</div>

							<div id="searchResults" class="mb-3" style="display: none;">
								<label class="form-label">Search Results</label>
								<div id="searchResultsList" class="list-group" style="max-height: 200px; overflow-y: auto;"></div>
							</div>

							<div id="searchLoading" class="text-center py-3" style="display: none;">
								<div class="spinner-border spinner-border-sm text-primary me-2"></div>
								Searching medicines...
							</div>

							<div id="noResults" class="alert alert-info" style="display: none;">
								<i class="fas fa-info-circle"></i> No medicines found matching your search.
							</div>
						</div>
					</div>

					<!-- Selected Medicine Information -->
					<div id="selectedMedicineInfo" style="display: none;">
						<hr>
						<h6 class="text-primary"><i class="fas fa-pill"></i> Selected Medicine Details</h6>

						<div class="row mb-3">
							<div class="col-md-6">
								<label class="form-label"><strong>Medicine Name</strong></label>
								<input type="text" class="form-control" id="selectedMedicineName" readonly>
							</div>
							<div class="col-md-6">
								<label class="form-label"><strong>Unit</strong></label>
								<input type="text" class="form-control" id="selectedMedicineUnit" readonly>
							</div>
						</div>

						<div class="mb-3">
							<label for="medicineIndication" class="form-label"><strong>Indication</strong></label>
							<input type="text" class="form-control" id="medicineIndication" readonly>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label"><strong>Price per Unit</strong></label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<input type="text" class="form-control" id="selectedMedicinePrice" readonly>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label for="quantity" class="form-label"><strong>Quantity *</strong></label>
									<input type="number" class="form-control" id="quantity" required min="1" placeholder="e.g., 30">
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label"><strong>Available Stock</strong></label>
									<input type="text" class="form-control" id="availableStock" readonly>
								</div>
							</div>
						</div>

						<div class="mb-3">
							<label for="medicineInstructions" class="form-label"><strong>Instructions</strong></label>
							<textarea class="form-control" id="medicineInstructions" rows="2" placeholder="Take with food, before meals, etc..."></textarea>
						</div>
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
	let prescribedMedicinesInfo = [];
	let selectedMedicineData = null;

	document.addEventListener('DOMContentLoaded', function() {
		// Lab service elements
		const labServiceSelect = document.getElementById('labServiceSelect');
		const serviceDescription = document.getElementById('serviceDescription');
		const servicePrice = document.getElementById('servicePrice');
		const requestServiceBtn = document.getElementById('requestServiceBtn');

		// Medicine elements
		const medicineSelect = document.getElementById('medicineSelect');
		const addMedicineBtn = document.getElementById('addMedicineBtn');
		const submitBtn = document.getElementById('submitPrescriptionBtn');

		// Tabs
		const searchTab = document.getElementById('search-tab');
		const dropdownTab = document.getElementById('dropdown-tab');

		// Lab service selection handler
		if (labServiceSelect && serviceDescription && servicePrice && requestServiceBtn) {
			labServiceSelect.addEventListener('change', function() {
				const opt = this.options[this.selectedIndex];
				if (this.value) {
					serviceDescription.value = opt.dataset.description || '';
					servicePrice.value = parseFloat(opt.dataset.price || 0).toFixed(2);
					requestServiceBtn.disabled = false;
				} else {
					serviceDescription.value = '';
					servicePrice.value = '';
					requestServiceBtn.disabled = true;
				}
			});

			const labServiceForm = document.getElementById('labServiceForm');
			if (labServiceForm) {
				labServiceForm.addEventListener('submit', function(e) {
					e.preventDefault();
					const formData = new FormData(this);
					if (!formData.get('MaDichVu') || !formData.get('MaGiayKhamBenh')) {
						alert('Please select a service and ensure medical record is available.');
						return;
					}

					requestServiceBtn.disabled = true;
					requestServiceBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Requesting...';

					fetch(this.action, { method: 'POST', body: formData })
						.then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
						.then(data => {
							if (data.success) {
								showAlert('success', 'Lab service requested successfully!');
								setTimeout(() => window.location.reload(), 1500);
							} else {
								showAlert('danger', 'Failed to request lab service: ' + (data.message || 'Unknown error'));
							}
						})
						.catch(() => showAlert('danger', 'An error occurred while requesting the service.'))
						.finally(() => {
							requestServiceBtn.disabled = false;
							requestServiceBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Request Service';
						});
				});
			}
		}

		// Medicine dropdown handlers
		if (medicineSelect && addMedicineBtn) {
			medicineSelect.addEventListener('change', function() {
				const opt = this.options[this.selectedIndex];
				if (this.value) {
					selectedMedicineData = {
						id: opt.value,
						name: opt.dataset.name,
						unit: opt.dataset.unit,
						indication: opt.dataset.indication,
						price: opt.dataset.price,
						stock: opt.dataset.stock
					};
					updateSelectedMedicineDisplay(selectedMedicineData);
					addMedicineBtn.disabled = false;
				} else {
					clearMedicineSelection();
				}
			});
			addMedicineBtn.addEventListener('click', addMedicine);
		}

		// Submit prescription
		if (submitBtn) submitBtn.addEventListener('click', submitPrescription);

		// Bind search events when modal is shown (prevents early DOM lookups)
		const medicineModal = document.getElementById('medicineModal');
		if (medicineModal) {
			medicineModal.addEventListener('shown.bs.modal', function() {
				const searchBtn = medicineModal.querySelector('#searchMedicineBtn');
				const searchInput = medicineModal.querySelector('#medicineSearch');
				if (searchBtn && searchInput) {
					searchBtn.onclick = performMedicineSearch;
					searchInput.onkeypress = function(e) {
						if (e.key === 'Enter') {
							e.preventDefault();
							performMedicineSearch();
						}
					}
				}
				// Ensure containers exist even if markup was altered
				ensureSearchElements(medicineModal);
			});
		}

		// Tab switching clears selections
		if (searchTab) searchTab.addEventListener('click', clearMedicineSelection);
		if (dropdownTab) dropdownTab.addEventListener('click', clearMedicineSelection);
	});

	function ensureSearchElements(modal) {
		const pane = modal.querySelector('#search-pane') || modal;
		let searchResults = modal.querySelector('#searchResults');
		let searchResultsList = modal.querySelector('#searchResultsList');
		let searchLoading = modal.querySelector('#searchLoading');
		let noResults = modal.querySelector('#noResults');

		if (!searchResults) {
			searchResults = document.createElement('div');
			searchResults.id = 'searchResults';
			searchResults.className = 'mb-3';
			searchResults.style.display = 'none';
			searchResults.innerHTML = `
				<label class="form-label">Search Results</label>
				<div id="searchResultsList" class="list-group" style="max-height: 200px; overflow-y: auto;"></div>
			`;
			pane.appendChild(searchResults);
			searchResultsList = searchResults.querySelector('#searchResultsList');
		}
		if (!searchLoading) {
			searchLoading = document.createElement('div');
			searchLoading.id = 'searchLoading';
			searchLoading.className = 'text-center py-3';
			searchLoading.style.display = 'none';
			searchLoading.innerHTML = `
				<div class="spinner-border spinner-border-sm text-primary me-2"></div>
				Searching medicines...
			`;
			pane.appendChild(searchLoading);
		}
		if (!noResults) {
			noResults = document.createElement('div');
			noResults.id = 'noResults';
			noResults.className = 'alert alert-info';
			noResults.style.display = 'none';
			noResults.innerHTML = `
				<i class="fas fa-info-circle"></i> No medicines found matching your search.
			`;
			pane.appendChild(noResults);
		}
		return { searchResults, searchResultsList, searchLoading, noResults };
	}

	function performMedicineSearch() {
		const modal = document.getElementById('medicineModal');
		if (!modal) return;

		const medicineSearch = modal.querySelector('#medicineSearch');
		if (!medicineSearch) {
			console.error('Search input not found in modal');
			showAlert('danger', 'Search UI not available.');
		 return;
		}

		const { searchResults, searchResultsList, searchLoading, noResults } = ensureSearchElements(modal);

		const searchTerm = medicineSearch.value.trim();
		if (searchTerm.length < 2) {
			showAlert('warning', 'Please enter at least 2 characters to search.');
			return;
		}

		searchLoading.style.display = 'block';
		searchResults.style.display = 'none';
		noResults.style.display = 'none';

		fetch(`<?php echo url('consultation/search-medicine'); ?>/${encodeURIComponent(searchTerm)}`)
			.then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
			.then(data => {
				searchLoading.style.display = 'none';
				if (Array.isArray(data) && data[1] == 200) {
					displaySearchResults(data[0]);
				} else {
					noResults.style.display = 'block';
				}
			})
			.catch(err => {
				console.error('Search error:', err);
				searchLoading.style.display = 'none';
				showAlert('danger', 'Error searching for medicines. Please try again.');
			});
	}

	function displaySearchResults(medicines) {
		const modal = document.getElementById('medicineModal');
		const { searchResults, searchResultsList } = ensureSearchElements(modal);
		searchResultsList.innerHTML = '';

		medicines.forEach(med => {
			const item = document.createElement('button');
			item.type = 'button';
			item.className = 'list-group-item list-group-item-action';
			item.innerHTML = `
				<div class="d-flex justify-content-between align-items-start">
					<div>
						<h6 class="mb-1">${med.TenThuoc}</h6>
						<p class="mb-1 text-muted small">${med.ChiDinh || 'No indication specified'}</p>
						<small class="text-muted">Unit: ${med.DonViTinh} | Stock: ${med.SoLuongTonKho}</small>
					</div>
					<div class="text-end">
						<span class="badge bg-primary">$${parseFloat(med.GiaTien).toFixed(2)}</span>
					</div>
				</div>
			`;
			item.addEventListener('click', () => selectMedicineFromSearch(med));
			searchResultsList.appendChild(item);
		});
		searchResults.style.display = 'block';
	}

	function selectMedicineFromSearch(medicine) {
		selectedMedicineData = {
			id: medicine.MaThuoc,
			name: medicine.TenThuoc,
			unit: medicine.DonViTinh,
			indication: medicine.ChiDinh || '',
			price: medicine.GiaTien,
			stock: medicine.SoLuongTonKho
		};
		updateSelectedMedicineDisplay(selectedMedicineData);

		const modal = document.getElementById('medicineModal');
		const { searchResults } = ensureSearchElements(modal);
		searchResults.style.display = 'none';

		const addMedicineBtn = document.getElementById('addMedicineBtn');
		if (addMedicineBtn) addMedicineBtn.disabled = false;

		showAlert('success', `Selected: ${medicine.TenThuoc}`);
	}

	function updateSelectedMedicineDisplay(medicineData) {
		const el = {
			name: document.getElementById('selectedMedicineName'),
			unit: document.getElementById('selectedMedicineUnit'),
			indication: document.getElementById('medicineIndication'),
			price: document.getElementById('selectedMedicinePrice'),
			stock: document.getElementById('availableStock'),
			wrap: document.getElementById('selectedMedicineInfo')
		};

		if (el.name) el.name.value = medicineData.name;
		if (el.unit) el.unit.value = medicineData.unit;
		if (el.indication) el.indication.value = medicineData.indication;
		if (el.price) el.price.value = parseFloat(medicineData.price).toFixed(2);
		if (el.stock) el.stock.value = medicineData.stock;
		if (el.wrap) el.wrap.style.display = 'block';
	}

	function clearMedicineSelection() {
		selectedMedicineData = null;

		const modal = document.getElementById('medicineModal');
		const el = {
			wrap: document.getElementById('selectedMedicineInfo'),
			addBtn: document.getElementById('addMedicineBtn'),
			results: modal?.querySelector('#searchResults'),
			noResults: modal?.querySelector('#noResults'),
			select: document.getElementById('medicineSelect'),
			search: modal?.querySelector('#medicineSearch'),
			quantity: document.getElementById('quantity'),
			instructions: document.getElementById('medicineInstructions')
		};

		if (el.wrap) el.wrap.style.display = 'none';
		if (el.addBtn) el.addBtn.disabled = true;
		if (el.results) el.results.style.display = 'none';
		if (el.noResults) el.noResults.style.display = 'none';
		if (el.select) el.select.value = '';
		if (el.search) el.search.value = '';
		if (el.quantity) el.quantity.value = '';
		if (el.instructions) el.instructions.value = '';
	}

	function addMedicine() {
		const quantity = parseInt(document.getElementById('quantity')?.value || 0, 10);
		const instructions = document.getElementById('medicineInstructions')?.value.trim() || '';

		if (!selectedMedicineData || !quantity) {
			showAlert('warning', 'Please select a medicine and enter quantity.');
			return;
		}

		const availableStock = parseInt(selectedMedicineData.stock, 10);
		if (quantity > availableStock) {
			showAlert('warning', `Requested quantity (${quantity}) exceeds available stock (${availableStock}).`);
			return;
		}

		if (prescribedMedicines.find(m => m.MaThuoc === selectedMedicineData.id)) {
			showAlert('warning', 'This medicine is already added to the prescription.');
			return;
		}

		const medicine = { MaThuoc: selectedMedicineData.id, SoLuong: quantity, GhiChu: instructions };
		const medicineInfo = {
			id: selectedMedicineData.id,
			name: selectedMedicineData.name,
			unit: selectedMedicineData.unit,
			quantity: quantity,
			instructions: instructions,
			price: parseFloat(selectedMedicineData.price)
		};

		prescribedMedicines.push(medicine);
		prescribedMedicinesInfo.push(medicineInfo);

		displayMedicine(medicineInfo);
		updateMedicineCount();

		clearMedicineSelection();
		const modalEl = document.getElementById('medicineModal');
		if (modalEl) {
			const modalInstance = bootstrap.Modal.getInstance(modalEl);
			if (modalInstance) modalInstance.hide();
		}

		showAlert('success', 'Medicine added to prescription!');
	}

	function displayMedicine(medicine) {
		const container = document.getElementById('prescribedMedicines');
		const noMsg = document.getElementById('noMedicinesMessage');
		if (!container) return;

		if (prescribedMedicines.length === 1 && noMsg) noMsg.remove();

		const div = document.createElement('div');
		div.className = 'border rounded p-3 mb-2';
		div.innerHTML = `
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<h6 class="mb-1">${medicine.name} (${medicine.unit})</h6>
					<p class="mb-1"><strong>Quantity:</strong> ${medicine.quantity}</p>
					${medicine.instructions ? `<p class="mb-1 text-muted"><em>${medicine.instructions}</em></p>` : ''}
					<span class="badge bg-success">$${(medicine.price * medicine.quantity).toFixed(2)}</span>
				</div>
				<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMedicine(this, '${medicine.id}')">
					<i class="fas fa-trash"></i>
				</button>
			</div>
		`;
		container.appendChild(div);
	}

	function removeMedicine(button, medicineId) {
		prescribedMedicines = prescribedMedicines.filter(m => m.MaThuoc !== medicineId);
		prescribedMedicinesInfo = prescribedMedicinesInfo.filter(m => m.id !== medicineId);
		button.closest('.border')?.remove();
		updateMedicineCount();

		if (prescribedMedicines.length === 0) {
			const container = document.getElementById('prescribedMedicines');
			if (container) {
				container.innerHTML = `
					<div class="text-muted text-center py-3" id="noMedicinesMessage">
						<i class="fas fa-pills fa-2x mb-2"></i>
						<p>No medicines prescribed yet.</p>
					</div>
				`;
			}
		}
	}

	function updateMedicineCount() {
		const el = document.getElementById('medicineCount');
		if (el) el.textContent = prescribedMedicines.length;
	}

	function removeService(url) {
		if (!confirm('Are you sure you want to remove this service?')) return;
		fetch(url, {
			method: 'POST',
			headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' }
		})
			.then(r => {
				if (r.ok) {
					alert('Service removed successfully.');
					setTimeout(() => window.location.reload(), 1000);
				} else {
					alert('Failed to remove service.');
				}
			})
			.catch(() => alert('An error occurred.'));
	}

	function submitPrescription() {
		const diagnosis = document.getElementById('diagnosis')?.value.trim();
		const notes = document.getElementById('notes')?.value.trim() || '';
		const recordId = document.querySelector('input[name="record_id"]')?.value;
		const appointmentId = document.querySelector('input[name="appointment_id"]')?.value;

		if (!diagnosis) {
			showAlert('warning', 'Please enter a diagnosis before submitting the prescription.');
		 return;
		}

		const btn = document.getElementById('submitPrescriptionBtn');
		if (btn) {
			btn.disabled = true;
			btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
		}

		const formData = new FormData();
		formData.append('MaGiayKhamBenh', recordId);
		formData.append('MaLichHen', appointmentId);
		formData.append('ChanDoan', diagnosis);
		formData.append('LuuY', notes);
		formData.append('medicines', JSON.stringify(prescribedMedicines));

		fetch("<?php echo url('consultation/submit-prescription'); ?>", { method: 'POST', body: formData })
			.then(r => r.json())
			.then(data => {
				if (data.success) {
					showAlert('success', 'Prescription submitted successfully!');
					setTimeout(() => { window.location.href = "<?php echo url('dashboard'); ?>"; }, 2000);
				} else {
					showAlert('danger', 'Failed to submit prescription: ' + data.message);
				}
			})
			.catch(() => showAlert('danger', 'An error occurred while submitting the prescription.'))
			.finally(() => {
				if (btn) {
					btn.disabled = false;
					btn.innerHTML = '<i class="fas fa-check-circle"></i> Submit Prescription';
				}
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
		setTimeout(() => { alertDiv.parentNode && alertDiv.parentNode.removeChild(alertDiv); }, 5000);
	}
</script>

<style>
	.position-fixed { position: fixed !important; }
	.list-group-item-action:hover { background-color: #f8f9fa; cursor: pointer; }
	.nav-tabs .nav-link { border: 1px solid transparent; border-top-left-radius: 0.375rem; border-top-right-radius: 0.375rem; }
	.nav-tabs .nav-link.active { color: #495057; background-color: #fff; border-color: #dee2e6 #dee2e6 #fff; }
	#searchResultsList { border: 1px solid #dee2e6; border-radius: 0.375rem; }
	#selectedMedicineInfo { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 0.375rem; padding: 1rem; }
	.spinner-border-sm { width: 1rem; height: 1rem; }
</style>

<?php
$content = ob_get_clean();
$title = 'Doctor Consultation - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>