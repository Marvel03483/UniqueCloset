<?php
require "../includes/db.php"; require "../includes/functions.php";
if (($_SESSION["role"] ?? "") !== "admin") die("Please <a href='login.php'>login</a> as admin.");
if ($_SERVER["REQUEST_METHOD"]==="POST") {
  check_csrf();
  if (($_POST["action"] ?? "") === "add") {
    $pdo->prepare("INSERT INTO products (category_id,name,description,price,size,stock_quantity,image_path,is_active) VALUES (?,?,?,?,?,?,?,1)")
        ->execute([(int)$_POST["category_id"], trim($_POST["name"]), trim($_POST["description"]), (float)$_POST["price"], trim($_POST["size"]), (int)$_POST["stock"], trim($_POST["image"]) ?: null]);
  } elseif (($_POST["action"] ?? "") === "toggle") {
    $pdo->prepare("UPDATE products SET is_active = 1 - is_active WHERE id=?")->execute([(int)$_POST["id"]]);
  }
  redirect("products.php");
}
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name AS cat FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC")->fetchAll();
require "../includes/header.php"; ?>
<h2>Products</h2>
<h4>Add Product</h4>
<form method="post" class="row g-2 mb-4">
  <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="add">
  <div class="col-md-3"><select name="category_id" class="form-select" required><option value="">Category...</option><?php foreach ($cats as $c): ?><option value="<?= $c["id"] ?>"><?= e($c["name"]) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-3"><input name="name" class="form-control" placeholder="Name" required></div>
  <div class="col-md-2"><input name="price" type="number" step="0.01" min="0" class="form-control" placeholder="Price" required></div>
  <div class="col-md-1"><input name="size" class="form-control" placeholder="Size" required></div>
  <div class="col-md-1"><input name="stock" type="number" min="0" class="form-control" placeholder="Stock" required></div>
  <div class="col-md-2"><input name="image" class="form-control" placeholder="assets/x.jpg"></div>
  <div class="col-md-10"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
</form>
<h4>All Products</h4>
<table class="table table-bordered"><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Size</th><th>Stock</th><th>Active</th><th></th></tr>
  <?php foreach ($products as $p): ?><tr><td><?= $p["id"] ?></td><td><?= e($p["name"]) ?></td><td><?= e($p["cat"]) ?></td><td><?= number_format($p["price"],2) ?></td><td><?= e($p["size"]) ?></td><td><?= $p["stock_quantity"] ?></td><td><?= $p["is_active"] ? "Yes" : "No" ?></td>
    <td><form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $p["id"] ?>"><button class="btn btn-sm btn-outline-secondary">Toggle</button></form></td></tr><?php endforeach; ?>
</table><?php require "../includes/footer.php"; ?>