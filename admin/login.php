<?php
require '../includes/db.php'; require '../includes/functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf();
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND role='admin'");
  $stmt->execute([trim($_POST['email'])]);
  $u = $stmt->fetch();
  if ($u && password_verify($_POST['password'], $u['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['role'] = 'admin';
    redirect('dashboard.php');
  } else { flash('Invalid admin credentials.','danger'); }
}
require '../includes/header.php';
?>
<h2>Admin Login</h2>
<form method="post" class="col-md-6">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <div class="mb-3"><label>Email</label><input name="email" class="form-control" required></div>
  <div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control" required></div>
  <button class="btn btn-dark">Login as Admin</button>
</form>
<?php require '../includes/footer.php'; ?>