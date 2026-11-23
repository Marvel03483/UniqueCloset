<?php require 'includes/header.php'; require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$o = $stmt->fetch();
if (!$o) { echo "<p>Order not found.</p>"; require 'includes/footer.php'; exit; }
?>
<div class="alert alert-success">
  <h3>Order #<?= $o['id'] ?> placed!</h3>
  <p>Payment: <strong>Cash on Delivery</strong> · Status: <strong><?= e($o['order_status']) ?></strong></p>
  <p>Total: MMK<?= number_format($o['total_amount'],2) ?></p>
</div>
<a href="my_orders.php" class="btn btn-primary">View My Orders</a>
<a href="index.php" class="btn btn-outline-secondary">Keep shopping</a>
<?php require 'includes/footer.php'; ?>