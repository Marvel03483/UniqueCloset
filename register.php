<?php
require 'includes/db.php'; require 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf();
  $name = trim($_POST['name']); $email = trim($_POST['email']); $pass = $_POST['password'];
  if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
    flash('Check your inputs (name required, valid email, password ≥ 6 chars).','danger');
  } else {
    $exists = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $exists->execute([$email]);
    if ($exists->fetch()) { flash('Email already registered.','danger'); }
    else {
      $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,'customer')")
          ->execute([$name,$email,password_hash($pass, PASSWORD_DEFAULT)]);
      flash('Registered. Please log in.');
      redirect('login.php');
    }
  }
}
require 'includes/header.php';
?>
<h2>Register</h2>
<form method="post" class="col-md-6">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <div class="mb-3"><label>Name</label><input name="name" class="form-control" required></div>
  <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
  <div class="mb-3"><label>Password</label><input name="password" type="password" class="form-control" required minlength="6"></div>
  <button class="btn btn-primary">Register</button>
</form>
<?php require 'includes/footer.php'; ?>