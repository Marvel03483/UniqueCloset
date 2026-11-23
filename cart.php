<?php require 'includes/header.php'; require_login();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf();
  $a = $_POST['action'] ?? '';
  if ($a === 'add') {
    $pid = (int)$_POST['product_id']; $qty = max(1,(int)$_POST['qty']);
    $pdo->prepare("INSERT INTO cart_items (user_id,product_id,quantity) VALUES (?,?,?)
                   ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)")
        ->execute([$_SESSION['user_id'], $pid, $qty]);
  } elseif ($a === 'update') {
    $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=? AND user_id=?")
        ->execute([max(1,(int)$_POST['qty']), (int)$_POST['id'], $_SESSION['user_id']]);
  } elseif ($a === 'remove') {
    $pdo->prepare("DELETE FROM cart_items WHERE id=? AND user_id=?")
        ->execute([(int)$_POST['id'], $_SESSION['user_id']]);
  }
  redirect('cart.php');
}

$items = $pdo->prepare("SELECT ci.*, p.name, p.price, p.stock_quantity FROM cart_items ci
                        JOIN products p ON ci.product_id=p.id WHERE ci.user_id=?");
$items->execute([$_SESSION['user_id']]);
$items = $items->fetchAll();
$total = 0;
?>
<h2>Your Cart</h2>
<table class="table">
  <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
  <?php foreach ($items as $i): $sub = $i['price']*$i['quantity']; $total += $sub; ?>
  <tr>
    <td><?= e($i['name']) ?></td>
    <td>MWK<?= number_format($i['price'],2) ?></td>
    <td>
      <form method="post" class="d-flex gap-1">
        <input type="hidden" name="csrf" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $i['id'] ?>">
        <input type="number" name="qty" value="<?= $i['quantity'] ?>" min="1" class="form-control form-control-sm" style="width:70px">
        <button class="btn btn-sm btn-outline-secondary">Update</button>
      </form>
    </td>
    <td>MWK<?= number_format($sub,2) ?></td>
    <td>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="remove">
        <input type="hidden" name="id" value="<?= $i['id'] ?>">
        <button class="btn btn-sm btn-danger">Remove</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<h4>Total: MWK<?= number_format($total,2) ?></h4>
<?php if ($items): ?>
  <a href="checkout.php" class="btn btn-success">Checkout (Cash on Delivery)</a>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>