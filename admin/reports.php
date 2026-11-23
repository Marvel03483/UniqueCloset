<?php require '../includes/functions.php'; require_admin(); require '../includes/db.php';
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');
$stmt = $pdo->prepare("SELECT DATE(delivered_at) d, COUNT(*) n, SUM(total_amount) rev
                       FROM orders WHERE order_status='Delivered'
                         AND DATE(delivered_at) BETWEEN ? AND ? GROUP BY DATE(delivered_at) ORDER BY d");
$stmt->execute([$from, $to]);
$rows = $stmt->fetchAll();
$pending = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status<>'Delivered' AND order_status<>'Cancelled'")->fetchColumn();
require '../includes/header.php';
?>
<h2>Sales Report</h2>
<form class="row g-2 mb-3">
  <div class="col-md-3"><input type="date" name="from" value="<?= e($from) ?>" class="form-control"></div>
  <div class="col-md-3"><input type="date" name="to" value="<?= e($to) ?>" class="form-control"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>
<p><strong>Pending deliveries:</strong> <?= $pending ?></p>
<table class="table table-bordered">
  <tr><th>Date</th><th>Delivered Orders</th><th>Revenue</th></tr>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= $r['d'] ?></td><td><?= $r['n'] ?></td><td>MWK<?= number_format($r['rev'],2) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php require '../includes/footer.php'; ?>