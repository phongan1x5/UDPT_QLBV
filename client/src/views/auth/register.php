<?php
ob_start();
?>

<div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h3>Create Account</h3>
                        <p class="text-muted">Join Hospital Management System</p>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="id" class="form-label">User ID</label>
                                <input type="text" class="form-control" id="id" name="id" required value="<?= htmlspecialchars($_POST['id'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="patient" <?= ($_POST['role'] ?? 'patient') == 'patient' ? 'selected' : '' ?>>Patient</option>
                                    <option value="doctor" <?= ($_POST['role'] ?? '') == 'doctor' ? 'selected' : '' ?>>Doctor</option>
                                    <option value="staff" <?= ($_POST['role'] ?? '') == 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="admin" <?= ($_POST['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Create Account</button>
                        </form>

                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login">Sign in here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Register - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>