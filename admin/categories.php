<?php
require "../includes/db.php"; require "../includes/functions.php";
if (($_SESSION["role"] ?? "") !== "admin") die("Please <a href='login.php'>login</a> as admin.");
if ($_SERVER["REQUEST_METHOD"]==="POST") {
  check_csrf();
  if (($_POST["action"] ?? "") === "add") {
    $pdo->prepare("INSERT INTO categories (name,description) VALUES (?,?)")->execute([trim($_POST["name"]), trim($_POST["description"] ?? "")]);
  } elseif (($_POST["action"] ?? "") === "delete") {
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST["id"]]);
  }
  redirect("categories.php");
}
$cats = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id) AS n FROM categories c ORDER BY id")->fetchAll();
require "../includes/header.php"; ?>
<h2>Categories</h2>
<form method="post" class="row g-2 mb-4">
  <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="add">
  <div class="col-md-4"><input name="name" class="form-control" placeholder="Name" required></div>
  <div class="col-md-6"><input name="description" class="form-control" placeholder="Description"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
</form>
<table class="table table-bordered"><tr><th>ID</th><th>Name</th><th>Description</th><th>Products</th><th></th></tr>
  <?php foreach ($cats as $c): ?><tr><td><?= $c["id"] ?></td><td><?= e($c["name"]) ?></td><td><?= e($c["description"]) ?></td><td><?= $c["n"] ?></td>
    <td><?php if ($c["n"] == 0): ?><form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c["id"] ?>"><button class="btn btn-sm btn-danger">Delete</button></form><?php else: ?>Has products<?php endif; ?></td></tr><?php endforeach; ?>
</table><?php require "../includes/footer.php"; ?>