<?php 
require "../includes/db.php"; 
require "../includes/functions.php"; 

// Bulletproof check: Print an error instead of redirecting
if (($_SESSION["role"] ?? "") !== "admin") {
    die("STOP: You are not logged in as an admin. Please <a href=\"login.php\">login here</a>.");
}

require "../includes/header.php"; 
?>
<h2>Admin Dashboard</h2>
<ul>
  <li><a href="categories.php">Manage Categories</a></li>
  <li><a href="products.php">Manage Products</a></li>
  <li><a href="orders.php">Manage Orders</a></li>
  <li><a href="customers.php">View Customers</a></li>
  <li><a href="reports.php">Sales Report</a></li>
</ul>
<?php require "../includes/footer.php"; ?>