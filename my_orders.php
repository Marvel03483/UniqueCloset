<?php require 'includes/header.php'; require_login();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<h2>My Orders</h2>
<table class="table">
  <tr><th>#</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th><th></th></tr>
  <?php foreach ($orders as $o): ?>
  <tr>
    <td><?= $o['id'] ?></td>
    <td><?= $o['created_at'] ?></td>
    <td>MWK<?= number_format($o['total_amount'],2) ?></td>
    <td><?= e($o['order_status']) ?></td>
    <td><?= e($o['payment_status']) ?></td>
    <td><a href="track_order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">Track</a></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php require 'includes/footer.php'; ?>