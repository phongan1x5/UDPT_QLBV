<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container mt-4">
    <h2><i class="fas fa-plus-circle"></i> Add New Service</h2>

    <!-- Success Notification -->
    <div id="successNotification" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
        <i class="fas fa-check-circle"></i>
        <strong>Success!</strong> <span id="successMessage">Service created successfully!</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Error Notification -->
    <div id="errorNotification" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none;">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Error!</strong> <span id="errorMessage">Failed to create service.</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Form -->
    <form id="addServiceForm"
        class="card p-4 mt-3 shadow-sm"
        data-url="<?php echo url('lab/services/create'); ?>">
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
            <button type="submit" class="btn btn-success" id="submitBtn">
                <span class="spinner-border spinner-border-sm me-2" style="display: none;" id="loadingSpinner"></span>
                ➕ Add Service
            </button>
            <a href="<?php echo url('dashboard'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- JavaScript -->
<script>
    document.getElementById('addServiceForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const actionUrl = form.getAttribute('data-url');
        const spinner = document.getElementById('loadingSpinner');
        spinner.style.display = 'inline-block';

        const formData = new FormData(form);

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData
            });

            console.log(response);
            console.log
            if (response.ok) {
                const successBox = document.getElementById('successNotification');
                successBox.classList.add('show');
                successBox.style.display = 'block';
                setTimeout(function() {
                    location.reload();
                }, 2000); // 2000 milliseconds = 2 seconds
            } else {
                const errorBox = document.getElementById('errorNotification');
                document.getElementById('errorMessage').textContent = "Failed";
                errorBox.classList.add('show');
                errorBox.style.display = 'block';
            }

        } catch (err) {
            spinner.style.display = 'none';
            const errorBox = document.getElementById('errorNotification');
            document.getElementById('errorMessage').textContent = 'Network error or server issue.';
            errorBox.classList.add('show');
            errorBox.style.display = 'block';
        }
    });
</script>

<?php
$content = ob_get_clean();
$title = 'Add New Service';
include __DIR__ . '/../layouts/main.php';
?>