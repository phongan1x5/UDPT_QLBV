<?php ob_start(); ?>

<div class="container mt-4">
  <h2 class="mb-4"><i class="fas fa-hospital-user"></i> Staff Dashboard</h2>

  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs mb-4" id="staffTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-panel" type="button">📋 Services</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="used-tab" data-bs-toggle="tab" data-bs-target="#used-panel" type="button">🧪 Used Services</button>
    </li>
    <li class="nav-item" role="presentation">
  <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid-panel" type="button">💳 Mark Paid</button>
</li>

  </ul>

  <div class="tab-content" id="staffTabContent">
    <!-- Panel 1: Services -->
    <div class="tab-pane fade show active" id="services-panel" role="tabpanel">
      <div class="search-form mb-4">
        <form method="GET" action="/lab/services/search" class="row g-2">
          <div class="col-md-6">
            <input type="text" name="query" class="form-control" placeholder="Search by ID" />
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
          </div>
          <div class="col-md-3 text-end">
            <a href="/lab/addservice" class="btn btn-success w-100">➕ Add New Service</a>
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

    <!-- Panel 2: Used Services -->
    <div class="tab-pane fade" id="used-panel" role="tabpanel">
      <div class="search-form mb-4">
        <form method="GET" action="/lab/used-services/search#used-panel" class="row g-2">
          <div class="col-md-6">
            <input type="text" name="medicalRecordId" class="form-control" placeholder="Search by Medical Record ID..." />
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
          </div>
            <div class="col-md-3 text-end">
            <a href="/lab/addusedservice" class="btn btn-success w-100">➕ Add Used Service</a>
          </div>
        </form>
      </div>

      <?php if (!empty($usedServices)): ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Service Name</th>
                <th>Record ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Result</th>
                <th>File</th>
                <th>Update</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usedServices as $us): ?>
                <tr>
                  <td><?= $us['MaDVSD'] ?></td>
                  <td><?= htmlspecialchars($us['TenDichVu'] ?? '-') ?></td>
                  <td><?= $us['MaGiayKhamBenh'] ?></td>
                  <td><?= date('Y-m-d H:i', strtotime($us['ThoiGian'])) ?></td>
                    <td>
                <?php if (!empty($us['TrangThai']) && $us['TrangThai'] === 'DaThuTien'): ?>
                  <span class="badge bg-success">Paid</span>
                <?php else: ?>
                  <span class="badge bg-danger">Unpaid</span>
                <?php endif; ?>
              </td>

                  <td>
                    <?php if (!empty($us['KetQua'])): ?>
                      <span class="badge bg-success">Available</span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($us['FileKetQua'])): ?>
                      <?php $filePath = rawurlencode($us['FileKetQua']); ?>
                      <a href="/lab/used-services/results/<?= $filePath ?>" target="_blank">
                        📄 View PDF
                      </a>
                    <?php else: ?>
                      <span class="no-file">No file available</span>
                    <?php endif; ?>
                  </td>


  
                <td>
                <button class="btn btn-outline-warning btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editModal" 
                        data-id="<?= $us['MaDVSD'] ?>"
                        data-ketqua="<?= htmlspecialchars($us['KetQua']) ?>"
                        data-file="<?= htmlspecialchars($us['FileKetQua']) ?>">
                    ✏️ Edit
                </button>
                </td>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <!-- Edit Used Service Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <form method="POST" action="/lab/used-services/update" enctype="multipart/form-data" class="modal-content" id="editUsedServiceForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">✏️ Edit Used Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="MaDVSD" id="editMaDVSD" />

                    <div class="mb-3">
                        <label class="form-label">Result (KetQua)</label>
                        <textarea name="KetQua" id="editKetQua" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Result File (Optional)</label>
                        <input type="file" name="file_upload" id="editFileUpload" class="form-control" />
                        <input type="hidden" name="FileKetQua" id="editFileKetQua" />
                        <small class="text-muted">Current: <span id="currentFileName">None</span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">💾 Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>

            </div>
            </div>

        </div>
      <?php else: ?>
        <div class="alert alert-info">No used services found.</div>
      <?php endif; ?>
    </div>

    <div class="tab-pane fade" id="paid-panel" role="tabpanel">
  <div class="card p-4">
    <h5 class="mb-3"><i class="fas fa-cash-register"></i> Mark All Services as Paid</h5>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php elseif (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="/lab/used-services/paid" class="row g-3">
      <div class="col-md-8">
        <label for="medicalRecordId" class="visually-hidden">Medical Record ID</label>
        <input
          type="number"
          name="medicalRecordId"
          id="medicalRecordId"
          class="form-control"
          placeholder="Enter Medical Record ID..."
          required
        >
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-success w-100">
          ✅ Confirm Mark as Paid
        </button>
      </div>
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
document.addEventListener('DOMContentLoaded', function () {
  // Restore active tab from hash
  const hash = window.location.hash;
  if (hash) {
    const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
    if (tabTrigger) {
      new bootstrap.Tab(tabTrigger).show();
    }
  }

  // Update hash when tab is changed
  document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function (tabBtn) {
    tabBtn.addEventListener('shown.bs.tab', function (event) {
      window.location.hash = event.target.getAttribute('data-bs-target');
    });
  });

  // Modal fill
  const modal = document.getElementById('editModal');
  if (modal) {
modal.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  const id = button.getAttribute('data-id');
  const ketqua = button.getAttribute('data-ketqua') || '';
  const file = button.getAttribute('data-file') || '';

  modal.querySelector('#editMaDVSD').value = id;
  modal.querySelector('#editKetQua').value = ketqua;
  modal.querySelector('#editFileKetQua').value = file;
  modal.querySelector('#currentFileName').innerText = file ? file.split('/').pop() : 'None';
});

  }
});

// Reload page after update (edit form submit)
document.getElementById('editUsedServiceForm').addEventListener('submit', function() {
  setTimeout(function() {
    window.location.reload();
  }, 100);
});
</script>