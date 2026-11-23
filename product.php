<?php
require "includes/db.php";
require "includes/functions.php";

$id = (int)($_GET["id"] ?? 0);

// Handle Review Submission (MUST be before HTML output)
if ($_SERVER["REQUEST_METHOD"]==="POST" && ($_POST["action"] ?? "")==="review" && !empty($_SESSION["user_id"])) {
  check_csrf();
  $v = $pdo->prepare("SELECT 1 FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE oi.product_id=? AND o.user_id=? AND o.order_status=\"Delivered\" LIMIT 1");
  $v->execute([$id, $_SESSION["user_id"]]);
  
  if (!$v->fetch()) { 
    flash("You can only review products you have received (Delivered).","danger"); 
  } else {
    try {
      $pdo->prepare("INSERT INTO reviews (product_id,user_id,rating,comment) VALUES (?,?,?,?)")
          ->execute([$id, $_SESSION["user_id"], (int)$_POST["rating"], trim($_POST["comment"])]);
      flash("Review posted.");
    } catch (Exception $ex) { 
      flash("You already reviewed this product.","danger"); 
    }
  }
  redirect("product.php?id=$id");
}

require "includes/header.php";

$stmt = $pdo->prepare("SELECT p.*, c.name AS cat FROM products p JOIN categories c ON p.category_id=c.id WHERE p.id=? AND p.is_active=1");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { echo "<p>Product not found.</p>"; require "includes/footer.php"; exit; }
?>
<div class="row">
  <div class="col-md-5">
    <?php if ($p["image_path"]): ?><img src="<?= e($p["image_path"]) ?>" class="img-fluid rounded"><?php endif; ?>
  </div>
  <div class="col-md-7">
    <h2><?= e($p["name"]) ?></h2>
    <p class="text-muted"><?= e($p["cat"]) ?> · Size <?= e($p["size"]) ?></p>
    <h4>MWK<?= number_format($p["price"],2) ?></h4>
    <p><?= nl2br(e($p["description"])) ?></p>
    <p><strong>Stock:</strong> <?= $p["stock_quantity"] ?></p>
    <?php if (!empty($_SESSION["user_id"])): ?>
      <form method="post" action="cart.php" class="row g-2">
        <input type="hidden" name="csrf" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= $p["id"] ?>">
        <div class="col-3"><input type="number" name="qty" value="1" min="1" class="form-control"></div>
        <div class="col-5"><button class="btn btn-primary">Add to Cart</button></div>
      </form>
    <?php else: ?>
      <a href="login.php" class="btn btn-primary">Log in to buy</a>
    <?php endif; ?>
  </div>
</div>

<hr class="mt-5">
<h4>Customer Reviews</h4>
<?php
$rev = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.product_id=? ORDER BY r.id DESC");
$rev->execute([$p["id"]]);
$reviews = $rev->fetchAll();
?>
<?php foreach ($reviews as $r): ?>
  <div class="border rounded p-3 mb-2 bg-light">
    <strong><?= e($r["name"]) ?></strong> — <span class="text-warning"><?= str_repeat("★",$r["rating"]).str_repeat("☆",5-$r["rating"]) ?></span>
    <small class="text-muted d-block" style="font-size: 0.8em;"><?= $r["created_at"] ?></small>
    <p class="mb-0 mt-2"><?= nl2br(e($r["comment"])) ?></p>
  </div>
<?php endforeach; ?>
<?php if (!$reviews): ?><p class="text-muted">No reviews yet. Be the first!</p><?php endif; ?>

<?php if (!empty($_SESSION["user_id"])): ?>
<form method="post" class="col-md-6 mt-4 mb-5 card card-body shadow-sm">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <input type="hidden" name="action" value="review">
  <h5>Leave a Review</h5>
  <div class="mb-2">
    <label class="form-label">Rating</label>
    <select name="rating" class="form-select" required>
      <option value="5">5 ★ Excellent</option>
      <option value="4">4 ★ Good</option>
      <option value="3">3 ★ Average</option>
      <option value="2">2 ★ Poor</option>
      <option value="1">1 ★ Terrible</option>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Comment</label>
    <textarea name="comment" class="form-control" rows="3" placeholder="What did you think of this product?" required></textarea>
  </div>
  <button class="btn btn-primary">Submit Review</button>
</form>
<?php endif; ?>

<?php require "includes/footer.php"; ?>