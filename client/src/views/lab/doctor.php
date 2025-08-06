<?php ob_start(); ?>

<div class="container mt-4">
  <h2 class="mb-4"><i class="fas fa-hospital-user"></i> Doctor Lab</h2>

  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs mb-4" id="staffTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-panel" type="button">📋 Services</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="used-tab" data-bs-toggle="tab" data-bs-target="#used-panel" type="button">🧪 Used Services</button>
    </li>
  </ul>

  <div class="tab-content" id="staffTabContent">
    <!-- Panel 1: Services -->
    <div class="tab-pane fade show active" id="services-panel" role="tabpanel">
      <form method="GET" action="/lab/services/search" class="d-flex justify-content-center align-items-center gap-3 mb-4">
        <input type="text" name="query" class="form-control w-50" placeholder="🔍 Search by Service ID..." />
        <button type="submit" class="btn btn-primary px-4">Search</button>
      </form>

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
      <form method="GET" action="/lab/used-services/search#used-panel" class="d-flex justify-content-center align-items-center gap-3 mb-4">
        <input type="text" name="medicalRecordId" class="form-control w-50" placeholder="🔍 Search by Medical Record ID..." />
        <button type="submit" class="btn btn-primary px-4">Search</button>
      </form>

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
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No used services found.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Doctor Lab';
include __DIR__ . '/../layouts/main.php';
?>
