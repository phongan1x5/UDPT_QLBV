<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container mt-4">
    <h2><i class="fas fa-flask"></i> Add Used Lab Service</h2>
    <form method="POST" action="<?php echo url('lab/used-services/create'); ?>" enctype="multipart/form-data" class="card p-4 mt-3 shadow-sm">
        <div class="mb-3">
            <label for="MaDichVu" class="form-label">Service ID</label>
            <input type="number" name="MaDichVu" id="MaDichVu" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="MaGiayKhamBenh" class="form-label">Medical Record ID</label>
            <input type="number" name="MaGiayKhamBenh" id="MaGiayKhamBenh" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="ThoiGian" class="form-label">Used At</label>
            <input type="datetime-local" name="ThoiGian" id="ThoiGian" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="YeuCau" class="form-label">Request (optional)</label>
            <textarea name="YeuCau" id="YeuCau" class="form-control" rows="3"></textarea>
            <div class="mb-3">
                <label for="KetQua" class="form-label">Result (optional)</label>
                <textarea name="KetQua" id="KetQua" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label for="FileKetQua" class="form-label">Upload Result File (optional)</label>
                <input type="file" name="FileKetQua" id="FileKetQua" class="form-control" accept=".pdf,.jpg,.png">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">➕ Add Used Service</button>
                <a href="<?php echo url('lab/staff#used-panel'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = 'Add Used Lab Service';
include __DIR__ . '/../layouts/main.php';
?>