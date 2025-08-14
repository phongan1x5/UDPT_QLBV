<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container mt-4">
    <h2><i class="fas fa-file-medical"></i> Doctor pharmacy</h2>

    <!-- Tabs -->
    <ul class="nav nav-tabs mt-4" id="prescriptionTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions-panel" type="button" role="tab">📄 Prescriptions</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="medicines-tab" data-bs-toggle="tab" data-bs-target="#medicines-panel" type="button" role="tab">💊 Medicines</button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Prescriptions -->
        <div class="tab-pane fade show active" id="prescriptions-panel" role="tabpanel">
            <?php if (!empty($prescriptions)): ?>
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Medical Record</th>
                            <th>Status</th>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">No prescriptions available.</div>
            <?php endif; ?>
        </div>

        <!-- Medicines -->
        <div class="tab-pane fade" id="medicines-panel" role="tabpanel">
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
$title = 'Doctor pharmacy';
include __DIR__ . '/../layouts/main.php';
?>