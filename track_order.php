<?php require 'includes/header.php'; require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$o = $stmt->fetch();
if (!$o) { echo "<p>Order not found.</p>"; require 'includes/footer.php'; exit; }

if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '') === 'cancel') {
  check_csrf();
  if (in_array($o['order_status'], ['Pending','Confirmed'])) {
    $pdo->beginTransaction();
    $pdo->prepare("UPDATE orders SET order_status='Cancelled', cancellation_reason=? WHERE id=?")
        ->execute([trim($_POST['reason']), $id]);
    $pdo->prepare("INSERT INTO order_status_history (order_id,status,changed_by,note) VALUES (?,?,?,?)")
        ->execute([$id, 'Cancelled', $_SESSION['user_id'], trim($_POST['reason'])]);
    $pdo->commit();
    flash('Order cancelled.');
    redirect("track_order.php?id=$id");
  } else { flash('Cannot cancel at this stage.','danger'); }
}

$hist = $pdo->prepare("SELECT * FROM order_status_history WHERE order_id=? ORDER BY id");
$hist->execute([$id]);
$hist = $hist->fetchAll();
?>
<h2>Order #<?= $o['id'] ?></h2>
<p><strong>Status:</strong> <?= e($o['order_status']) ?> · <strong>Payment:</strong> <?= e($o['payment_status']) ?></p>
<p><strong>Total:</strong> MWK<?= number_format($o['total_amount'],2) ?> · <strong>Address:</strong> <?= e($o['delivery_address']) ?></p>

<h4>Status Timeline</h4>
<ul class="list-group mb-3">
  <?php foreach ($hist as $h): ?>
    <li class="list-group-item d-flex justify-content-between">
      <span><?= e($h['status']) ?><?= $h['note'] ? ' — '.e($h['note']) : '' ?></span>
      <small class="text-muted"><?= $h['changed_at'] ?></small>
    </li>
  <?php endforeach; ?>
</ul>

<?php if (in_array($o['order_status'], ['Pending','Confirmed'])): ?>
<h5>Cancel Order</h5>
<form method="post" class="col-md-6">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <input type="hidden" name="action" value="cancel">
  <div class="mb-2"><input name="reason" class="form-control" placeholder="Reason for cancelling" required></div>
  <button class="btn btn-danger">Cancel Order</button>
</form>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>