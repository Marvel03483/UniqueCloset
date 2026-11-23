<?php require 'includes/header.php';
$where = "is_active=1"; $params = [];
if (!empty($_GET['cat'])) { $where .= " AND category_id=?"; $params[] = (int)$_GET['cat']; }
if (!empty($_GET['q']))   { $where .= " AND name LIKE ?";   $params[] = '%'.$_GET['q'].'%'; }
if (!empty($_GET['min'])) { $where .= " AND price >= ?";    $params[] = (float)$_GET['min']; }
if (!empty($_GET['max'])) { $where .= " AND price <= ?";    $params[] = (float)$_GET['max']; }

$stmt = $pdo->prepare("SELECT * FROM products WHERE $where ORDER BY id DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<h2>Catalogue</h2>
<form class="row g-2 mb-4" method="get">
  <div class="col-md-3"><input name="q" class="form-control" placeholder="Search…" value="<?= e($_GET['q'] ?? '') ?>"></div>
  <div class="col-md-2"><select name="cat" class="form-select">
    <option value="">All categories</option>
    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= ($_GET['cat'] ?? '')==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
  </select></div>
  <div class="col-md-2"><input name="min" type="number" class="form-control" placeholder="Min price" value="<?= e($_GET['min'] ?? '') ?>"></div>
  <div class="col-md-2"><input name="max" type="number" class="form-control" placeholder="Max price" value="<?= e($_GET['max'] ?? '') ?>"></div>
  <div class="col-md-1"><button class="btn btn-primary w-100">Filter</button></div>
</form>

<div class="row">
<?php foreach ($products as $p): ?>
  <div class="col-md-3 mb-3">
    <div class="card h-100">
      <?php if ($p['image_path']): ?><img src="<?= e($p['image_path']) ?>" class="card-img-top" style="height:180px;object-fit:cover" loading="lazy"><?php endif; ?>
      <div class="card-body">
        <h6 class="card-title"><?= e($p['name']) ?></h6>
        <p class="mb-1">MWK<?= number_format($p['price'],2) ?> · Size <?= e($p['size']) ?></p>
        <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$products): ?><p class="text-muted">No products match.</p><?php endif; ?>
</div>
<?php require 'includes/footer.php'; ?>