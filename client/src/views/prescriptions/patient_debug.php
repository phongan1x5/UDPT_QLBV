<?php
// Debug - you can remove this later
// echo '<pre>';
// print_r($medicalHistory);
// echo '</pre>';
// ob_start();
// 
?>



<?php
$content = ob_get_clean();
$title = 'My Prescriptions - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>