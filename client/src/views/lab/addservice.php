<?php ob_start(); ?>

<div class="container mt-4">
    <h2><i class="fas fa-plus-circle"></i> Add New Service</h2>
    <form method="POST" action="/lab/services/create" class="card p-4 mt-3 shadow-sm">
        <div class="mb-3">
            <label for="TenDichVu" class="form-label">Service Name</label>
            <input type="text" name="TenDichVu" id="TenDichVu" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="NoiDungDichVu" class="form-label">Service Description</label>
            <textarea name="NoiDungDichVu" id="NoiDungDichVu" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="DonGia" class="form-label">Price (VND)</label>
            <input type="number" name="DonGia" id="DonGia" class="form-control" required>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-success">➕ Add Service</button>
            <a href="/lab/staff" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Add New Service';
include __DIR__ . '/../layouts/main.php';
?>
