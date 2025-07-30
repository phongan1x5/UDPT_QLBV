<?php ob_start(); ?>

<div class="container mt-4">
  <h2><i class="fas fa-file-medical"></i> Prescription Staff Dashboard</h2>

  <!-- Tabs -->
  <ul class="nav nav-tabs mt-4" id="prescriptionTab" role="tablist">
    <li class="nav-item">
      <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions-panel" type="button">📄 Prescriptions</button>
    </li>
    <li class="nav-item">
      <button class="nav-link active" id="medicines-tab" data-bs-toggle="tab" data-bs-target="#medicines-panel" type="button">💊 Medicines</button>
    </li>
  </ul>

  <div class="tab-content mt-3">
    <!-- Prescriptions -->
    <div class="tab-pane fade show active" id="prescriptions-panel">
      <?php if (!empty($prescriptions)): ?>
<table class="table table-bordered align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>Medical Record</th>
      <th>Status</th>
      <th>Update</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($prescriptions as $p): ?>
      <tr>
        <td><?= $p['MaToaThuoc'] ?></td>
        <td><?= $p['MaGiayKhamBenh'] ?></td>
        <td>
          <span class="badge <?= $p['TrangThaiToaThuoc'] === 'Đã phát' ? 'bg-success' : 'bg-warning text-dark' ?>">
            <?= htmlspecialchars($p['TrangThaiToaThuoc']) ?>
          </span>
        </td>
        <td>
          <form method="POST" action="/pharmacy/update-status" class="d-flex align-items-center gap-2">
            <input type="hidden" name="MaToaThuoc" value="<?= $p['MaToaThuoc'] ?>">

            <div class="form-check form-switch m-0">
              <input 
                class="form-check-input" 
                type="checkbox" 
                role="switch" 
                id="toggleStatus<?= $p['MaToaThuoc'] ?>" 
                name="TrangThai" 
                value="Đã phát"
                <?= $p['TrangThaiToaThuoc'] === 'Đã phát' ? 'checked' : '' ?>
              >
            </div>

            <button class="btn btn-sm btn-outline-primary">💾</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

      <?php else: ?>
        <div class="alert alert-info">No prescriptions available.</div>
      <?php endif; ?>
    </div>

    <!-- Medicines -->
    <div class="tab-pane fade" id="medicines-panel">
      <div class="d-flex justify-content-end mb-3">
        <a href="/pharmacy/addmedicine" class="btn btn-success">➕ Add Medicine</a>
      </div>

      <?php if (!empty($medicines)): ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>MaThuoc</th>
              <th>TenThuoc</th>
              <th>Donvi</th>
              <th>ChiDinh</th>
              <th>TonKho</th>
              <th>GiaTien</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($medicines as $med): ?>
            <tr>
                <td><?= $med['MaThuoc'] ?></td>
                <td><?= htmlspecialchars($med['TenThuoc']) ?></td>
                <td><?= htmlspecialchars($med['DonViTinh']) ?></td>
                <td><?= htmlspecialchars($med['ChiDinh']) ?></td>
                <td><?= $med['SoLuongTonKho'] ?></td>
                <td><?= number_format($med['GiaTien'], 0, ',', '.') ?> VND</td>
            </tr>
            <?php endforeach; ?>

          </tbody>
        </table>
      <?php else: ?>
        <div class="alert alert-info">No medicines found.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Prescription Staff Dashboard';
include __DIR__ . '/../layouts/main.php';
?>
