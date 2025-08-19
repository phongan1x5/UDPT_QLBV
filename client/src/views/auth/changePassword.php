<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white text-center">
                    <h5 class="mb-0">
                        <i class="fas fa-key"></i> Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('changePassword'); ?>" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?php echo $_SESSION['user']['user_id'] ?? ''; ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Current Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">Please enter your current password.</div>
                        </div>

                        <div class="mb-3">
                            <label for="newPassword" class="form-label">
                                <i class="fas fa-unlock-alt"></i> New Password
                            </label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required minlength="6">
                            <div class="invalid-feedback">New password must be at least 6 characters.</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-check-double"></i> Confirm New Password
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">Please confirm your new password.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-eraser"></i> Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bootstrap validation + password match check
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                var newPassword = document.getElementById('newPassword').value;
                var confirmPassword = document.getElementById('confirm_password').value;

                if (newPassword !== confirmPassword) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('New password and confirmation do not match.');
                    return;
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        }, false);
    })();
</script>

<?php
$content = ob_get_clean();
$title = 'Change Password - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>