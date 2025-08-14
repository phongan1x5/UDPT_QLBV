<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// echo '<pre>';
// print_r($usedServices);
// echo '</pre>'

?>

<div class="container mt-4">
  <h2 class="mb-4"><i class="fas fa-hospital-user"></i> Lab Staff Dashboard</h2>

  <!-- Enhanced Tabs Navigation -->
  <ul class="nav nav-tabs mb-4" id="staffTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-panel" type="button">
        📋 Services
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-panel" type="button">
        ⏳ Pending Results
        <?php
        // Count pending medical records
        $pendingCount = 0;
        if (!empty($usedServices)) {
          $groupedServices = [];
          foreach ($usedServices as $us) {
            $recordId = $us['MaGiayKhamBenh'];
            if (!isset($groupedServices[$recordId])) {
              $groupedServices[$recordId] = [];
            }
            $groupedServices[$recordId][] = $us;
          }

          foreach ($groupedServices as $recordId => $services) {
            $hasPendingResults = false;
            foreach ($services as $service) {
              $isPaid = !empty($service['TrangThai']) &&
                ($service['TrangThai'] === 'DaThuTien' || $service['TrangThai'] === 'DaCoKetQua');
              $hasResults = ($service['TrangThai'] === 'DaCoKetQua') || !empty($service['KetQua']);

              if ($isPaid && !$hasResults) {
                $hasPendingResults = true;
                break;
              }
            }
            if ($hasPendingResults) $pendingCount++;
          }
        }
        if ($pendingCount > 0): ?>
          <span class="badge bg-warning text-dark ms-1"><?= $pendingCount ?></span>
        <?php endif; ?>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-panel" type="button">
        ✅ Completed Results
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-panel" type="button">
        📊 All Records
      </button>
    </li>
  </ul>

  <div class="tab-content" id="staffTabContent">
    <!-- Panel 1: Services -->
    <div class="tab-pane fade show active" id="services-panel" role="tabpanel">
      <div class="search-form mb-4">
        <form method="GET" action="<?php echo url('lab/services/search'); ?>" class="row g-2">
          <div class="col-md-6">
            <input type="text" name="query" class="form-control" placeholder="Search by ID" />
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
          </div>
          <div class="col-md-3 text-end">
            <a href="<?php echo url('lab/addservice'); ?>" class="btn btn-success w-100">➕ Add New Service</a>
          </div>
        </form>
      </div>

      <?php if (!empty($services)): ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($services as $service): ?>
                <tr>
                  <td><?= $service['MaDichVu'] ?></td>
                  <td><?= htmlspecialchars($service['TenDichVu']) ?></td>
                  <td><?= htmlspecialchars($service['NoiDungDichVu']) ?></td>
                  <td><?= number_format($service['DonGia'], 0, ',', '.') ?> đ</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No services found.</div>
      <?php endif; ?>
    </div>

    <?php
    // Prepare grouped services for all tabs
    $groupedServices = [];
    $totalPrices = [];
    $pendingRecords = [];
    $completedRecords = [];

    if (!empty($usedServices)) {
      foreach ($usedServices as $us) {
        $recordId = $us['MaGiayKhamBenh'];
        if (!isset($groupedServices[$recordId])) {
          $groupedServices[$recordId] = [];
          $totalPrices[$recordId] = 0;
        }
        $groupedServices[$recordId][] = $us;
        $totalPrices[$recordId] += $us['DonGia'] ?? 0;
      }

      // Categorize records
      foreach ($groupedServices as $recordId => $services) {
        $hasPendingResults = false;
        $allHaveResults = true;
        $paidServicesCount = 0;

        foreach ($services as $service) {
          $isPaid = !empty($service['TrangThai']) &&
            ($service['TrangThai'] === 'DaThuTien' || $service['TrangThai'] === 'DaCoKetQua');
          $hasResults = ($service['TrangThai'] === 'DaCoKetQua') || !empty($service['KetQua']);

          if ($isPaid) {
            $paidServicesCount++;
            if (!$hasResults) {
              $hasPendingResults = true;
              $allHaveResults = false;
            }
          }
        }

        if ($paidServicesCount > 0) {
          if ($hasPendingResults) {
            $pendingRecords[$recordId] = $services;
          } elseif ($allHaveResults) {
            $completedRecords[$recordId] = $services;
          }
        }
      }
    }

    // Function to render medical record card
    function renderMedicalRecordCard($recordId, $services, $totalPrices, $showCompletionStatus = true)
    {
      global $completedRecords, $pendingRecords;

      $isCompleted = isset($completedRecords[$recordId]);
      $isPending = isset($pendingRecords[$recordId]);
    ?>
      <div class="card mb-4 shadow-sm <?= $isCompleted ? 'border-success' : ($isPending ? 'border-warning' : '') ?>">
        <div class="card-header <?= $isCompleted ? 'bg-success' : ($isPending ? 'bg-warning' : 'bg-primary') ?> text-white">
          <div class="row align-items-center">
            <div class="col-md-6">
              <h6 class="mb-0">
                <i class="fas fa-clipboard-list"></i> Medical Record ID: <?= $recordId ?>
                <?php if ($showCompletionStatus): ?>
                  <?php if ($isCompleted): ?>
                    <i class="fas fa-check-circle ms-2" title="All results completed"></i>
                  <?php elseif ($isPending): ?>
                    <i class="fas fa-exclamation-triangle ms-2" title="Has pending results"></i>
                  <?php endif; ?>
                <?php endif; ?>
              </h6>
            </div>
            <div class="col-md-3">
              <strong>Total Price: <?= number_format($totalPrices[$recordId], 0, ',', '.') ?> đ</strong>
            </div>
            <div class="col-md-3 text-end">
              <?php
              $allPaid = true;
              foreach ($services as $service) {
                if (
                  empty($service['TrangThai']) ||
                  ($service['TrangThai'] !== 'DaThuTien' && $service['TrangThai'] !== 'DaCoKetQua')
                ) {
                  $allPaid = false;
                  break;
                }
              }
              ?>

              <?php if (!$allPaid): ?>
                <button class="btn btn-warning btn-sm" onclick="markAllPaid(<?= $recordId ?>)">
                  💳 Mark All Paid
                </button>
              <?php else: ?>
                <span class="badge bg-light text-dark">✅ All Paid</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Service ID</th>
                  <th>Service Name</th>
                  <th>Price</th>
                  <th>Date</th>
                  <th>Payment Status</th>
                  <th>Result Status</th>
                  <th>File</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($services as $us): ?>
                  <?php
                  $isPaid = !empty($us['TrangThai']) &&
                    ($us['TrangThai'] === 'DaThuTien' || $us['TrangThai'] === 'DaCoKetQua');
                  $hasResults = ($us['TrangThai'] === 'DaCoKetQua') || !empty($us['KetQua']);
                  ?>
                  <tr class="<?= $isPaid ? ($hasResults ? 'table-success' : '') : 'table-warning' ?>">
                    <td><?= $us['MaDVSD'] ?></td>
                    <td><?= htmlspecialchars($us['TenDichVu'] ?? '-') ?></td>
                    <td class="fw-bold"><?= number_format($us['DonGia'] ?? 0, 0, ',', '.') ?> đ</td>
                    <td>
                      <?php
                      // ✅ Enhanced date handling for multiple formats
                      $dateTime = $us['ThoiGian'];

                      // Handle different date formats
                      if (strpos($dateTime, 'T') !== false) {
                        // ISO format: 2025-08-13T15:44:46+00:00
                        $formattedDate = date('Y-m-d H:i', strtotime($dateTime));
                      } else {
                        // Old format: 08/03/2025
                        $formattedDate = date('Y-m-d H:i', strtotime($dateTime));
                      }

                      echo $formattedDate;
                      ?>
                    </td>
                    <td>
                      <?php if ($us['TrangThai'] === 'DaCoKetQua'): ?>
                        <span class="badge bg-success">✅ Completed</span>
                      <?php elseif ($us['TrangThai'] === 'DaThuTien'): ?>
                        <span class="badge bg-info">💰 Paid</span>
                      <?php else: ?>
                        <span class="badge bg-danger">❌ Unpaid</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($hasResults): ?>
                        <span class="badge bg-success">📋 Available</span>
                      <?php else: ?>
                        <span class="badge bg-warning text-dark">⏳ Pending</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($us['FileKetQua'])): ?>
                        <?php $filePath = rawurlencode($us['FileKetQua']); ?>
                        <a href="<?php echo url('lab/used-services/results/' . $filePath); ?>" target="_blank" class="btn btn-outline-info btn-sm">
                          📄 View PDF
                        </a>
                      <?php else: ?>
                        <span class="text-muted">No file</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($isPaid): ?>
                        <button class="btn btn-outline-primary btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#editModal"
                          data-id="<?= $us['MaDVSD'] ?>"
                          data-ketqua="<?= htmlspecialchars($us['KetQua'] ?? "") ?>"
                          data-file="<?= htmlspecialchars($us['FileKetQua']) ?>"
                          data-service="<?= htmlspecialchars($us['TenDichVu'] ?? '') ?>"
                          data-has-results="<?= $hasResults ? 'true' : 'false' ?>">
                          ✏️ Update Result
                        </button>
                      <?php else: ?>
                        <span class="text-muted">
                          <i class="fas fa-lock"></i> Payment Required
                        </span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php } ?>

    <!-- Panel 2: Pending Results -->
    <div class="tab-pane fade" id="pending-panel" role="tabpanel">
      <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Pending Results:</strong> Medical records with paid services that still need results.
      </div>

      <div class="search-form mb-4">
        <form method="GET" action="<?php echo url('lab/used-services/search'); ?>#pending-panel" class="row g-2">
          <div class="col-md-6">
            <input type="text" name="medicalRecordId" class="form-control" placeholder="Search by Medical Record ID..." />
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
          </div>
          <div class="col-md-3 text-end">
            <span class="badge bg-warning text-dark fs-6">
              <?= count($pendingRecords) ?> Records Pending
            </span>
          </div>
        </form>
      </div>

      <?php if (!empty($pendingRecords)): ?>
        <?php foreach ($pendingRecords as $recordId => $services): ?>
          <?php renderMedicalRecordCard($recordId, $services, $totalPrices); ?>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <strong>Great!</strong> No medical records are waiting for results.
        </div>
      <?php endif; ?>
    </div>

    <!-- Panel 3: Completed Results -->
    <div class="tab-pane fade" id="completed-panel" role="tabpanel">
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>Completed Results:</strong> Medical records where all paid services have results.
      </div>

      <div class="search-form mb-4">
        <form method="GET" action="<?php echo url('lab/used-services/search'); ?>#completed-panel" class="row g-2">
          <div class="col-md-6">
            <input type="text" name="medicalRecordId" class="form-control" placeholder="Search by Medical Record ID..." />
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
          </div>
          <div class="col-md-3 text-end">
            <span class="badge bg-success fs-6">
              <?= count($completedRecords) ?> Records Completed
            </span>
          </div>
        </form>
      </div>

      <?php if (!empty($completedRecords)): ?>
        <?php foreach ($completedRecords as $recordId => $services): ?>
          <?php renderMedicalRecordCard($recordId, $services, $totalPrices); ?>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i>
          No completed medical records found.
        </div>
      <?php endif; ?>
    </div>

    <!-- Panel 4: All Records -->
    <div class="tab-pane fade" id="all-panel" role="tabpanel">
      <div class="search-form mb-4">
        <form method="GET" action="<?php echo url('lab/used-services/search'); ?>#all-panel" class="row g-2">
          <div class="col-md-6">
            <input type="text" name="medicalRecordId" class="form-control" placeholder="Search by Medical Record ID..." />
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
          </div>
          <div class="col-md-3 text-end">
            <a href="<?php echo url('lab/addusedservice'); ?>" class="btn btn-success w-100">➕ Add Used Service</a>
          </div>
        </form>
      </div>

      <?php if (!empty($groupedServices)): ?>
        <?php foreach ($groupedServices as $recordId => $services): ?>
          <?php renderMedicalRecordCard($recordId, $services, $totalPrices); ?>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i> No used services found.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Enhanced Edit Used Service Modal with Warning -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="<?php echo url('lab/used-services/update'); ?>" enctype="multipart/form-data" class="modal-content" id="editUsedServiceForm">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">✏️ Update Result</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="MaDVSD" id="editMaDVSD" />

        <!-- Warning for existing results -->
        <div id="existingResultWarning" class="alert alert-warning d-none">
          <i class="fas fa-exclamation-triangle"></i>
          <strong>Warning:</strong> This service already has results. Updating will overwrite the existing data.
        </div>

        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i>
          <strong>Service:</strong> <span id="serviceName"></span>
        </div>

        <div class="mb-3">
          <label class="form-label">Test Result</label>
          <textarea name="KetQua" id="editKetQua" class="form-control" rows="4" placeholder="Enter test results here..."></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Result File (Optional PDF)</label>
          <input type="file" name="file_upload" id="editFileUpload" class="form-control" accept=".pdf" />
          <input type="hidden" name="FileKetQua" id="editFileKetQua" />
          <small class="text-muted">Current file: <span id="currentFileName">None</span></small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">💾 Save Result</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="paymentModalLabel">💳 Confirm Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i>
          Are you sure you want to mark all services for Medical Record ID <strong id="paymentRecordId"></strong> as paid?
        </div>
        <div class="text-center">
          <p><strong>Total Amount: <span id="paymentTotalAmount"></span> đ</strong></p>
        </div>
      </div>
      <div class="modal-footer">
        <form method="POST" action="<?php echo url('lab/used-services/paid'); ?>" id="paymentForm">
          <input type="hidden" name="medicalRecordId" id="paymentMedicalRecordId" />
          <button type="submit" class="btn btn-success">✅ Confirm Payment</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Staff Dashboard';
include __DIR__ . '/../layouts/main.php';
?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Restore active tab from hash
    const hash = window.location.hash;
    if (hash) {
      const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
      if (tabTrigger) {
        new bootstrap.Tab(tabTrigger).show();
      }
    }

    // Update hash when tab is changed
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tabBtn) {
      tabBtn.addEventListener('shown.bs.tab', function(event) {
        window.location.hash = event.target.getAttribute('data-bs-target');
      });
    });

    // Enhanced modal fill for editing results with warning
    const modal = document.getElementById('editModal');
    if (modal) {
      modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const ketqua = button.getAttribute('data-ketqua') || '';
        const file = button.getAttribute('data-file') || '';
        const service = button.getAttribute('data-service') || '';
        const hasResults = button.getAttribute('data-has-results') === 'true';

        modal.querySelector('#editMaDVSD').value = id;
        modal.querySelector('#editKetQua').value = ketqua;
        modal.querySelector('#editFileKetQua').value = file;
        modal.querySelector('#serviceName').textContent = service;
        modal.querySelector('#currentFileName').innerText = file ? file.split('/').pop() : 'None';

        // ✅ Show warning if results already exist
        const warningDiv = modal.querySelector('#existingResultWarning');
        if (hasResults && (ketqua.trim() !== '' || file.trim() !== '')) {
          warningDiv.classList.remove('d-none');
        } else {
          warningDiv.classList.add('d-none');
        }
      });
    }

    // Handle form submission with immediate reload
    const editForm = document.getElementById('editUsedServiceForm');
    if (editForm) {
      editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = editForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;

        const formData = new FormData(editForm);

        fetch(editForm.action, {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (response.ok) {
              const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
              if (modal) {
                modal.hide();
              }
              window.location.reload();
            } else {
              throw new Error('Network response was not ok');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error updating result. Please try again.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
          });
      });
    }

    // Handle payment form submission
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
      paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = paymentForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;

        const formData = new FormData(paymentForm);

        fetch(paymentForm.action, {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (response.ok) {
              const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
              if (modal) {
                modal.hide();
              }
              window.location.reload();
            } else {
              throw new Error('Network response was not ok');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error processing payment. Please try again.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
          });
      });
    }
  });

  function markAllPaid(medicalRecordId) {
    const recordCards = document.querySelectorAll('.card');
    let totalPrice = 0;

    recordCards.forEach(card => {
      const cardHeader = card.querySelector('.card-header');
      if (cardHeader && cardHeader.textContent.includes(`Medical Record ID: ${medicalRecordId}`)) {
        const priceText = cardHeader.querySelector('strong').textContent;
        const priceMatch = priceText.match(/Total Price: ([\d,]+)/);
        if (priceMatch) {
          totalPrice = priceMatch[1];
        }
      }
    });

    document.getElementById('paymentRecordId').textContent = medicalRecordId;
    document.getElementById('paymentMedicalRecordId').value = medicalRecordId;
    document.getElementById('paymentTotalAmount').textContent = totalPrice;

    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    paymentModal.show();
  }
</script>

<style>
  .table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
  }

  .table-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
  }

  .border-success {
    border-color: #198754 !important;
    border-width: 2px !important;
  }

  .border-warning {
    border-color: #ffc107 !important;
    border-width: 2px !important;
  }

  .card {
    transition: all 0.3s ease;
  }

  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .badge {
    font-size: 0.75em;
  }

  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }

  .nav-tabs .nav-link {
    color: #495057;
  }

  .nav-tabs .nav-link.active {
    color: #007bff;
    font-weight: 600;
  }

  @media (max-width: 768px) {
    .container {
      padding: 0 10px;
    }

    .card-header .row {
      text-align: center;
    }

    .card-header .col-md-3 {
      margin-top: 10px;
    }
  }
</style>