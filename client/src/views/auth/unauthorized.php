<?php
ob_start();
?>

<div class="container-fluid text-center mt-5">
    <h1 class="text-danger">
        <i class="fas fa-ban"></i> Unauthorized
    </h1>
    <p class="lead text-muted">You do not have permission to access this page.</p>
    <a href="<?php echo url('dashboard'); ?>" class="btn btn-outline-primary mt-3">
        <i class="fas fa-home"></i> Return to Dashboard
    </a>
</div>

<?php
$content = ob_get_clean();
$title = 'Unauthorized Access - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>
