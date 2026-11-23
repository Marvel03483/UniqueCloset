<?php require 'includes/header.php'; require_login();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf();
  $items = $pdo->prepare("SELECT ci.*, p.price, p.name, p.stock_quantity FROM cart_items ci
                          JOIN products p ON ci.product_id=p.id WHERE ci.user_id=?");
  $items->execute([$_SESSION['user_id']]);
  $items = $items->fetchAll();
  if (!$items) { flash('Cart is empty.','danger'); redirect('cart.php'); }

  $subtotal = 0;
  foreach ($items as $i) {
    if ($i['quantity'] > $i['stock_quantity']) { flash('Not enough stock for '.$i['name'],'danger'); redirect('cart.php'); }
    $subtotal += $i['price'] * $i['quantity'];
  }
  $fee = 5.00; $total = $subtotal + $fee;

  $pdo->beginTransaction();
  try {
    $pdo->prepare("INSERT INTO orders (user_id,total_amount,delivery_fee,delivery_address,delivery_phone)
                   VALUES (?,?,?,?,?)")
        ->execute([$_SESSION['user_id'], $total, $fee, trim($_POST['address']), trim($_POST['phone'])]);
    $order_id = $pdo->lastInsertId();

    foreach ($items as $i) {
      $pdo->prepare("INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity,subtotal)
                     VALUES (?,?,?,?,?,?)")
          ->execute([$order_id, $i['product_id'], $i['name'], $i['price'], $i['quantity'], $i['price']*$i['quantity']]);
      $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id=?")
          ->execute([$i['quantity'], $i['product_id']]);
    }
    $pdo->prepare("INSERT INTO order_status_history (order_id,status,changed_by,note) VALUES (?,?,?,?)")
        ->execute([$order_id, 'Pending', $_SESSION['user_id'], 'Order placed by customer']);
    $pdo->prepare("DELETE FROM cart_items WHERE user_id=?")->execute([$_SESSION['user_id']]);
    $pdo->commit();
    redirect("order_confirm.php?id=$order_id");
  } catch (Exception $ex) {
    $pdo->rollBack();
    flash('Checkout error: '.$ex->getMessage(),'danger');
  }
}
?>
<h2>Checkout</h2>
<form method="post" class="col-md-6">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <div class="mb-3"><label>Delivery address</label><textarea name="address" class="form-control" required></textarea></div>
  <div class="mb-3"><label>Phone</label><input name="phone" class="form-control" required></div>
  <div class="alert alert-info">Payment method: <strong>Cash on Delivery</strong>. Delivery fee: MWK5000.00.</div>
  <button class="btn btn-success">Place Order</button>
</form>
<?php require 'includes/footer.php'; ?>