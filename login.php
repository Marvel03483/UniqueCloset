<?php
require 'includes/db.php'; require 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf();
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND role='customer'");
  $stmt->execute([trim($_POST['email'])]);
  $u = $stmt->fetch();
  if ($u && password_verify($_POST['password'], $u['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['name'] = $u['name'];
    $_SESSION['role'] = 'customer';
    redirect('index.php');
  } else { flash('Invalid credentials.','danger'); }
}
require 'includes/header.php';
?>
<h2>Login</h2>
<form method="post" class="col-md-6">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
  <div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control" required></div>
  <button class="btn btn-primary">Login</button>
</form>
<?php require 'includes/footer.php'; ?>