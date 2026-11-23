<?php require '../includes/functions.php'; require_admin(); require '../includes/db.php';
$rows = $pdo->query("SELECT id,name,email,created_at FROM users WHERE role='customer' ORDER BY id")->fetchAll();
require '../includes/header.php';
?>
<h2>Customers</h2>
<table class="table table-bordered">
  <tr><th>ID</th><th>Name</th><th>Email</th><th>Joined</th></tr>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= $r['id'] ?></td><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= $r['created_at'] ?></td></tr>
  <?php endforeach; ?>
</table>
<?php require '../includes/footer.php'; ?>