<?php ob_start(); ?>

<div class="container mt-5">
    <h2><i class="fas fa-capsules"></i> ➕ Add New Medicine</h2>

    <form method="POST" action="/pharmacy/medicine/create" class="card p-4 mt-4 shadow-sm">
        <div class="mb-3">
            <label for="TenThuoc" class="form-label">Medicine Name</label>
            <input type="text" name="TenThuoc" id="TenThuoc" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="DonViTinh" class="form-label">Unit</label>
            <input type="text" name="DonViTinh" id="DonViTinh" class="form-control" placeholder="e.g., tablets, ml..." required>
        </div>

        <div class="mb-3">
            <label for="ChiDinh" class="form-label">Usage Instruction (Optional)</label>
            <textarea name="ChiDinh" id="ChiDinh" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="SoLuongTonKho" class="form-label">Stock Quantity</label>
            <input type="number" name="SoLuongTonKho" id="SoLuongTonKho" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="GiaTien" class="form-label">Price (VND)</label>
            <input type="number" name="GiaTien" id="GiaTien" class="form-control" step="0.01" required>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success">💾 Add Medicine</button>
            <a href="/prescriptions/staff" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Add Medicine';
include __DIR__ . '/../layouts/main.php';
?>
