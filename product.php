<?php
require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); exit('Product not found.'); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($p['name']) ?> — <?= e(SITE_NAME) ?></title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="site-header"><a class="brand" href="index.php">ZY<span>COLLECTION</span></a><nav><a href="index.php">Continue shopping</a></nav><a class="cart-link" href="cart.php">Bag (<?= cart_count() ?>)</a></header>
<main class="detail-layout"><div class="detail-image"><img src="uploads/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" onerror="this.src='assets/placeholder.svg'"></div><div class="detail-copy"><p class="eyebrow"><?= e($p['category']) ?></p><h1><?= e($p['name']) ?></h1><h2><?= money((float)$p['price']) ?></h2><p><?= nl2br(e($p['description'] ?? '')) ?></p><p class="stock"><?= (int)$p['stock'] ?> available</p>
<form method="post" action="cart.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="action" value="add"><label>Quantity <input type="number" name="quantity" min="1" max="<?= (int)$p['stock'] ?>" value="1"></label><button class="button" type="submit">Add to bag</button></form></div></main>
</body></html>
