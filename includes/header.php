<?php require_once __DIR__.'/db.php'; require_once __DIR__.'/functions.php'; ?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Unique Closet</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
 <div class="container">
  <a class="navbar-brand" href="index.php">Unique Closet</a>
  <div class="ms-auto">
   <a class="btn btn-sm btn-outline-light" href="cart.php">Cart</a>
   <?php if (!empty($_SESSION['user_id'])): ?>
     <a class="btn btn-sm btn-outline-light" href="my_orders.php">My Orders</a>
     <a class="btn btn-sm btn-outline-warning" href="logout.php">Logout</a>
   <?php else: ?>
     <a class="btn btn-sm btn-outline-light" href="login.php">Login</a>
     <a class="btn btn-sm btn-outline-light" href="register.php">Register</a>
   <?php endif; ?>
  </div>
 </div>
</nav>
<div class="container"><?php show_flash(); ?>