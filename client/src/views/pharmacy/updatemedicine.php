<?php ob_start(); ?>

<div class="container mt-4">
  <h2><i class="fas fa-edit"></i> Cập nhật thông tin thuốc</h2>

  <form method="POST" action="/pharmacy/medicines/update" class="card p-4 mt-3 shadow-sm">
    <input type="hidden" name="MaThuoc" value="<?= htmlspecialchars($medicine['MaThuoc']) ?>">

    <div class="mb-3">
      <label class="form-label">Tên thuốc</label>
      <input type="text" name="TenThuoc" class="form-control" 
             value="<?= htmlspecialchars($medicine['TenThuoc'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Đơn vị tính</label>
      <input type="text" name="DonViTinh" class="form-control" 
             value="<?= htmlspecialchars($medicine['DonViTinh'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Chỉ định</label>
      <textarea name="ChiDinh" class="form-control" rows="3"><?= htmlspecialchars($medicine['ChiDinh'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Số lượng tồn kho</label>
      <input type="number" name="SoLuongTonKho" class="form-control" 
             value="<?= htmlspecialchars($medicine['SoLuongTonKho'] ?? 0) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Giá tiền</label>
      <input type="number" step="0.01" name="GiaTien" class="form-control" 
             value="<?= htmlspecialchars($medicine['GiaTien'] ?? 0.0) ?>" required>
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
      <a href="/pharmacy/medicines" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Cập nhật thuốc';
include __DIR__ . '/../layouts/main.php';
?>
