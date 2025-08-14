<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<?php if (isset($usedService)): ?>
  <div class="container mt-4">
    <h3>Used Service Detail</h3>
    <table class="table table-bordered mt-3">
      <tr>
        <th>Service ID</th>
        <td><?= $usedService['MaDVSD'] ?></td>
      </tr>
      <tr>
        <th>Service Name</th>
        <td><?= htmlspecialchars($usedService['TenDichVu'] ?? '-') ?></td>
      </tr>
      <tr>
        <th>Medical Record ID</th>
        <td><?= $usedService['MaGiayKhamBenh'] ?></td>
      </tr>
      <tr>
        <th>Time</th>
        <td><?= date('Y-m-d H:i', strtotime($usedService['ThoiGian'])) ?></td>
      </tr>
      <tr>
        <th>Result</th>
        <td>
          <?php if (!empty($usedService['KetQua'])): ?>
            <span class="badge bg-success">Available</span>
            <div class="mt-2"><?= nl2br(htmlspecialchars($usedService['KetQua'])) ?></div>
          <?php else: ?>
            <span class="badge bg-warning text-dark">Pending</span>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th>File Result</th>
        <td>

          <?php if (!empty($usedService['FileKetQua'])): ?>
            <?php $filePath = rawurlencode($usedService['FileKetQua']); ?>
            <a href="<?php echo url('lab/used-services/results/' . $filePath); ?>" target="_blank">
              📄 View PDF
            </a>
          <?php else: ?>
            <span class="no-file">No file available</span>
          <?php endif; ?>
        </td>
      </tr>
    </table>

    <a href="javascript:history.back()" class="btn btn-secondary mt-3">← Back</a>
  </div>
<?php else: ?>
  <div class="alert alert-danger mt-3">Service detail not found.</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
$title = 'Add New Service';
include __DIR__ . '/../layouts/main.php';
?>