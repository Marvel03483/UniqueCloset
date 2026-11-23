<?php
require '../includes/functions.php'; require_admin();
require '../includes/db.php';

$next = ['Pending'=>'Confirmed','Confirmed'=>'Shipped','Shipped'=>'Out for Delivery','Out for Delivery'=>'Delivered'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf();
  $id = (int)$_POST['id'];
  $a = $_POST['action'] ?? '';
  $stmt = $pdo->prepare("SELECT order_status FROM orders WHERE id=?");
  $stmt->execute([$id]);
  $current = $stmt->fetchColumn();

  if ($a === 'advance' && isset($next[$current])) {
    $new = $next[$current];
    $pdo->beginTransaction();
    if ($new === 'Delivered') {
      $pdo->prepare("UPDATE orders SET order_status=?, payment_status='Paid', delivered_at=NOW() WHERE id=?")->execute([$new, $id]);
    } else {
      $pdo->prepare("UPDATE orders SET order_status=? WHERE id=?")->execute([$new, $id]);
    }
    $pdo->prepare("INSERT INTO order_status_history (order_id,status,changed_by) VALUES (?,?,?)")
        ->execute([$id, $new, $_SESSION['user_id']]);
    $pdo->commit();
  }
  redirect('orders.php');
}

$orders = $pdo->query("SELECT o.*, u.name AS customer FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.id DESC")->fetchAll();
require '../includes/header.php';
?>
<h2>Orders</h2>
<table class="table table-bordered">
  <tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Action</th></tr>
  <?php foreach ($orders as $o): ?>
  <tr>
    <td><?= $o['id'] ?></td>
    <td><?= e($o['customer']) ?></td>
    <td>MWK<?= number_format($o['total_amount'],2) ?></td>
    <td><?= e($o['order_status']) ?></td>
    <td><?= e($o['payment_status']) ?></td>
    <td>
      <?php if (isset($next[$o['order_status']])): ?>
        <form method="post" style="display:inline">
          <input type="hidden" name="csrf" value="<?= csrf() ?>">
          <input type="hidden" name="action" value="advance">
          <input type="hidden" name="id" value="<?= $o['id'] ?>">
          <button class="btn btn-sm btn-primary">Move to <?= $next[$o['order_status']] ?></button>
        </form>
      <?php else: ?><span class="text-muted">—</span><?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php require '../includes/footer.php'; ?>