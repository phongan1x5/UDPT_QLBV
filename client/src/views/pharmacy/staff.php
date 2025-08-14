<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container mt-4">
    <h2><i class="fas fa-file-medical"></i> Staff Pharmacy</h2>

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
                <?php
                $totalPrescriptions = count($prescriptions);
                $dispensedCount = 0;
                $activeCount = 0;
                $paidCount = 0;
                foreach ($prescriptions as $p) {
                    $status = $p['TrangThaiToaThuoc'] ?? 'Active';
                    if ($status === 'Completed') {
                        $dispensedCount++;
                    } elseif ($status === 'Active') {
                        $activeCount++;
                    } elseif ($status === 'Paid') {
                        $paidCount++;
                    }
                }
                $pendingCount = $totalPrescriptions - $dispensedCount;
                ?>

                <div class="alert alert-primary d-flex justify-content-between align-items-center">
                    <div>
                        Total: <strong><?= $totalPrescriptions ?></strong> |
                        Active: <strong class="text-warning"><?= $activeCount ?></strong> |
                        Paid: <strong class="text-info"><?= $paidCount ?></strong> |
                        Completed: <strong class="text-success"><?= $dispensedCount ?></strong>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">🔍 Filter by Status:</span>
                            <select class="form-select" id="statusFilter" onchange="filterPrescriptions()">
                                <option value="all">All Prescriptions</option>
                                <option value="Active">Active (<?= $activeCount ?>)</option>
                                <option value="Paid">Paid (<?= $paidCount ?>)</option>
                                <option value="Completed">Completed (<?= $dispensedCount ?>)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">📋 Search ID:</span>
                            <input type="text" class="form-control" id="prescriptionSearch"
                                placeholder="Search by Prescription ID or Medical Record..."
                                onkeyup="filterPrescriptions()">
                            <button class="btn btn-outline-secondary" onclick="clearPrescriptionSearch()">Clear</button>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Prescription ID</th>
                            <th>Medical Record</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="prescriptionTableBody">
                        <?php foreach ($prescriptions as $p): ?>
                            <?php
                            $presId = (int)$p['MaToaThuoc'];
                            $status = $p['TrangThaiToaThuoc'] ?? 'Active';
                            $badgeClass = match ($status) {
                                'Active' => 'bg-warning text-dark',
                                'Paid' => 'bg-info text-dark',
                                'Completed' => 'bg-success',
                                default => 'bg-secondary'
                            };
                            ?>
                            <tr class="prescription-row" data-status="<?= $status ?>" data-id="<?= $presId ?>" data-medical-record="<?= htmlspecialchars($p['MaGiayKhamBenh']) ?>">
                                <td><?= $presId ?></td>
                                <td><?= htmlspecialchars($p['MaGiayKhamBenh']) ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Details Button -->
                                        <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailsModal<?= $presId ?>">
                                            🔎 Details
                                        </button>

                                        <!-- Print Button -->
                                        <button class="btn btn-sm btn-outline-secondary"
                                            onclick="printPrescription(<?= $presId ?>)">
                                            🖨️ Print
                                        </button>

                                        <!-- Action Buttons -->
                                        <?php if ($status === 'Active'): ?>
                                            <button class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#paidModal<?= $presId ?>">
                                                💳 Mark Paid
                                            </button>
                                        <?php elseif ($status === 'Paid'): ?>
                                            <button class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#distributeModal<?= $presId ?>">
                                                🚚 Distribute
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- No Results Message -->
                <div id="noResultsMessage" class="alert alert-info" style="display: none;">
                    <i class="fas fa-search"></i> No prescriptions found matching your filter criteria.
                </div>

                <!-- All Modals Outside the Table -->
                <?php foreach ($prescriptions as $p): ?>
                    <?php
                    $presId = (int)$p['MaToaThuoc'];
                    $status = $p['TrangThaiToaThuoc'] ?? 'Active';
                    $badgeClass = match ($status) {
                        'Active' => 'bg-warning text-dark',
                        'Paid' => 'bg-info text-dark',
                        'Completed' => 'bg-success',
                        default => 'bg-secondary'
                    };
                    ?>

                    <!-- Details Modal -->
                    <div class="modal fade" id="detailsModal<?= $presId ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Prescription #<?= $presId ?> Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6"><strong>Prescription ID:</strong> <?= $presId ?></div>
                                        <div class="col-md-6"><strong>Medical Record:</strong> <?= htmlspecialchars($p['MaGiayKhamBenh']) ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Status:</strong> <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                    </div>

                                    <h6>Medicines:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Medicine</th>
                                                    <th>Unit</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Instructions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (($p['medicines'] ?? []) as $m): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($m['TenThuoc']) ?></td>
                                                        <td><?= htmlspecialchars($m['DonViTinh']) ?></td>
                                                        <td><?= (int)$m['SoLuong'] ?></td>
                                                        <td><?= number_format((float)$m['GiaTien'], 2) ?></td>
                                                        <td><strong><?= number_format((float)$m['ThanhTien'], 2) ?></strong></td>
                                                        <td><?= htmlspecialchars($m['GhiChu'] ?? '') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-primary">
                                                    <th colspan="4" class="text-end">Total Amount:</th>
                                                    <th><?= number_format((float)($p['total_cost'] ?? 0), 2) ?> VND</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mark as Paid Confirmation Modal -->
                    <?php if ($status === 'Active'): ?>
                        <div class="modal fade" id="paidModal<?= $presId ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title">💳 Confirm Payment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <strong>⚠️ Confirmation Required</strong><br>
                                            Are you sure the patient has paid for Prescription #<?= $presId ?>?
                                        </div>
                                        <div class="text-center">
                                            <p><strong>Total Amount: <?= number_format((float)($p['total_cost'] ?? 0), 2) ?> VND</strong></p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="<?php echo url('pharmacy/paidPrescription/'); ?>">
                                            <input type="hidden" name="MaToaThuoc" value="<?= $presId ?>">
                                            <input type="hidden" name="TrangThaiToaThuoc" value="Paid">
                                            <button type="submit" class="btn btn-warning">✅ Yes, Mark as Paid</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Distribute Medicine Confirmation Modal -->
                    <?php if ($status === 'Paid'): ?>
                        <div class="modal fade" id="distributeModal<?= $presId ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">🚚 Confirm Medicine Distribution</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-success">
                                            <strong>✅ Confirmation Required</strong><br>
                                            Are you sure you want to distribute medicines for Prescription #<?= $presId ?>?
                                        </div>
                                        <div class="alert alert-info">
                                            <strong>📋 This will:</strong><br>
                                            • Mark prescription as "Completed"<br>
                                            • Subtract medicine quantities from stock<br>
                                            • Cannot be undone
                                        </div>
                                        <div class="text-center">
                                            <p><strong>Medicines to distribute:</strong></p>
                                            <ul class="list-unstyled">
                                                <?php foreach (($p['medicines'] ?? []) as $m): ?>
                                                    <li>• <?= htmlspecialchars($m['TenThuoc']) ?> - <?= (int)$m['SoLuong'] ?> <?= htmlspecialchars($m['DonViTinh']) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="<?php echo url('pharmacy/handleMedicine'); ?>">
                                            <input type="hidden" name="MaToaThuoc" value="<?= $presId ?>">
                                            <button type="submit" class="btn btn-success">✅ Yes, Distribute Medicines</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>

            <?php else: ?>
                <div class="alert alert-info">No prescriptions available.</div>
            <?php endif; ?>
        </div>

        <!-- Medicines Tab -->
        <div class="tab-pane fade" id="medicines-panel" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">🔍 Search Medicine:</span>
                        <input type="text" class="form-control" id="medicineSearch"
                            placeholder="Search by medicine name..."
                            onkeyup="filterMedicines()">
                        <button class="btn btn-outline-secondary" onclick="clearMedicineSearch()">Clear</button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="<?= url('pharmacy/addmedicine'); ?>" class="btn btn-success">➕ Add Medicine</a>
                </div>
            </div>

            <?php if (!empty($medicines)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Medicine ID</th>
                            <th>Medicine Name</th>
                            <th>Unit</th>
                            <th>Indication</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="medicineTableBody">
                        <?php foreach ($medicines as $med): ?>
                            <tr class="medicine-row" data-name="<?= strtolower(htmlspecialchars($med['TenThuoc'])) ?>">
                                <td><?= $med['MaThuoc'] ?></td>
                                <td><?= htmlspecialchars($med['TenThuoc']) ?></td>
                                <td><?= htmlspecialchars($med['DonViTinh']) ?></td>
                                <td><?= htmlspecialchars($med['ChiDinh'] ?? '') ?></td>
                                <td>
                                    <?php if ($med['SoLuongTonKho'] <= 10): ?>
                                        <span class="badge bg-danger"><?= $med['SoLuongTonKho'] ?> (Low Stock)</span>
                                    <?php elseif ($med['SoLuongTonKho'] <= 50): ?>
                                        <span class="badge bg-warning text-dark"><?= $med['SoLuongTonKho'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $med['SoLuongTonKho'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= number_format($med['GiaTien'], 2) ?> VND</td>
                                <td>
                                    <a href="<?= url('pharmacy/updatemedicine'); ?>?MaThuoc=<?= urlencode($med['MaThuoc']) ?>" class="btn btn-sm btn-outline-secondary">
                                        ✏️ Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- No Results Message for Medicines -->
                <div id="noMedicineResultsMessage" class="alert alert-info" style="display: none;">
                    <i class="fas fa-search"></i> No medicines found matching your search criteria.
                </div>

            <?php else: ?>
                <div class="alert alert-info">No medicines found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Staff Pharmacy';
include __DIR__ . '/../layouts/main.php';
?>

<script>
    // Prescription filtering function
    function filterPrescriptions() {
        const statusFilter = document.getElementById('statusFilter').value;
        const searchFilter = document.getElementById('prescriptionSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.prescription-row');
        const noResultsMsg = document.getElementById('noResultsMessage');
        let visibleRows = 0;

        rows.forEach(row => {
            const status = row.dataset.status;
            const id = row.dataset.id.toLowerCase();
            const medicalRecord = row.dataset.medicalRecord.toLowerCase();

            const statusMatch = (statusFilter === 'all' || status === statusFilter);
            const searchMatch = (searchFilter === '' ||
                id.includes(searchFilter) ||
                medicalRecord.includes(searchFilter));

            if (statusMatch && searchMatch) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleRows === 0 && (statusFilter !== 'all' || searchFilter !== '')) {
            noResultsMsg.style.display = 'block';
        } else {
            noResultsMsg.style.display = 'none';
        }
    }

    // Clear prescription search
    function clearPrescriptionSearch() {
        document.getElementById('prescriptionSearch').value = '';
        document.getElementById('statusFilter').value = 'all';
        filterPrescriptions();
    }

    // Medicine filtering function
    function filterMedicines() {
        const searchFilter = document.getElementById('medicineSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.medicine-row');
        const noResultsMsg = document.getElementById('noMedicineResultsMessage');
        let visibleRows = 0;

        rows.forEach(row => {
            const medicineName = row.dataset.name;

            if (searchFilter === '' || medicineName.includes(searchFilter)) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleRows === 0 && searchFilter !== '') {
            noResultsMsg.style.display = 'block';
        } else {
            noResultsMsg.style.display = 'none';
        }
    }

    // Clear medicine search
    function clearMedicineSearch() {
        document.getElementById('medicineSearch').value = '';
        filterMedicines();
    }

    // Print prescription function
    function printPrescription(prescriptionId) {
        const prescriptions = <?= json_encode($prescriptions ?? []) ?>;
        const prescription = prescriptions.find(p => p.MaToaThuoc == prescriptionId);

        if (!prescription) {
            alert('Prescription not found!');
            return;
        }

        const printContent = `
        <div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px;">
                <h1 style="color: #2c3e50;">🏥 Hospital Pharmacy</h1>
                <h2 style="color: #34495e;">Medical Prescription</h2>
            </div>
            
            <div style="margin-bottom: 30px;">
                <div style="margin-bottom: 10px;">
                    <strong>Prescription ID: ${prescription.MaToaThuoc}</strong>
                    <span style="float: right;"><strong>Medical Record: ${prescription.MaGiayKhamBenh}</strong></span>
                </div>
                <div>
                    <strong>Status: ${prescription.TrangThaiToaThuoc}</strong>
                    <span style="float: right;"><strong>Date: ${new Date().toLocaleDateString()}</strong></span>
                </div>
            </div>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background-color: #ecf0f1;">
                        <th style="border: 1px solid #bdc3c7; padding: 12px; text-align: left;">Medicine</th>
                        <th style="border: 1px solid #bdc3c7; padding: 12px; text-align: center;">Quantity</th>
                        <th style="border: 1px solid #bdc3c7; padding: 12px; text-align: center;">Unit</th>
                        <th style="border: 1px solid #bdc3c7; padding: 12px; text-align: right;">Price</th>
                        <th style="border: 1px solid #bdc3c7; padding: 12px; text-align: right;">Amount</th>
                        <th style="border: 1px solid #bdc3c7; padding: 12px; text-align: left;">Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    ${prescription.medicines.map(med => `
                        <tr>
                            <td style="border: 1px solid #bdc3c7; padding: 10px;">${med.TenThuoc}</td>
                            <td style="border: 1px solid #bdc3c7; padding: 10px; text-align: center;">${med.SoLuong}</td>
                            <td style="border: 1px solid #bdc3c7; padding: 10px; text-align: center;">${med.DonViTinh}</td>
                            <td style="border: 1px solid #bdc3c7; padding: 10px; text-align: right;">${parseFloat(med.GiaTien).toFixed(2)}</td>
                            <td style="border: 1px solid #bdc3c7; padding: 10px; text-align: right;"><strong>${parseFloat(med.ThanhTien).toFixed(2)}</strong></td>
                            <td style="border: 1px solid #bdc3c7; padding: 10px;">${med.GhiChu || '-'}</td>
                        </tr>
                    `).join('')}
                </tbody>
                <tfoot>
                    <tr style="background-color: #d5dbdb;">
                        <th colspan="4" style="border: 1px solid #bdc3c7; padding: 12px; text-align: right;">Total Amount:</th>
                        <th style="border: 1px solid #bdc3c7; padding: 12px; text-align: right; font-size: 18px;">${parseFloat(prescription.total_cost).toFixed(2)} VND</th>
                        <th style="border: 1px solid #bdc3c7; padding: 12px;"></th>
                    </tr>
                </tfoot>
            </table>
            
            <div style="margin-top: 50px;">
                <div style="display: inline-block; width: 45%; text-align: center;">
                    <div style="border-top: 1px solid #000; margin-top: 50px; padding-top: 10px;">
                        <strong>Patient Signature</strong>
                    </div>
                </div>
                <div style="display: inline-block; width: 45%; float: right; text-align: center;">
                    <div style="border-top: 1px solid #000; margin-top: 50px; padding-top: 10px;">
                        <strong>Pharmacist Signature</strong>
                    </div>
                </div>
            </div>
            
            <div style="clear: both; text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #bdc3c7; color: #7f8c8d;">
                <small>Generated on ${new Date().toLocaleString()} | Hospital Management System</small>
            </div>
        </div>
    `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Prescription #${prescriptionId}</title>
            <style>
                @media print {
                    body { margin: 0; }
                    @page { margin: 1cm; }
                }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);

        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }
</script>

<style>
    .btn-group .btn {
        margin-right: 2px;
    }

    .modal-header.bg-warning {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .modal-header.bg-success {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .input-group-text {
        font-weight: 500;
    }

    .prescription-row,
    .medicine-row {
        transition: all 0.3s ease;
    }

    .prescription-row:hover,
    .medicine-row:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }
</style>