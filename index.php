<?php
require_once 'config.php';
$category = $_GET['category'] ?? '';
$search = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM products WHERE stock > 0";
$params = [];
if (in_array($category, ['Men','Women','Shoes','Accessories'], true)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($search !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY featured DESC, created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(SITE_NAME) ?> — Modern essentials</title>
<link rel="stylesheet" href="assets/css/style.css">
</head><body>
<header class="site-header"><a class="brand" href="index.php">ZY<span>COLLECTION</span></a>
<nav><a href="index.php">Home</a><a href="index.php?category=Men">Men</a><a href="index.php?category=Women">Women</a><a href="index.php?category=Shoes">Shoes</a><a href="index.php?category=Accessories">Accessories</a></nav>
<a class="cart-link" href="cart.php">Bag (<?= cart_count() ?>)</a></header>
<main>
<section class="hero"><div class="hero-copy"><p class="eyebrow">THE NEW SEASON / 2026</p><h1>Style that speaks<br><em>without saying.</em></h1><p>Curated essentials for every day. Discover modern pieces made to move with you.</p><a class="button button-light" href="#shop">Explore collection</a></div><div class="hero-art"><div class="hero-orbit"></div><div class="hero-label">ZY<br>EDIT<br>NO. 01</div></div></section>
<section class="section" id="shop"><div class="section-heading"><div><p class="eyebrow">CURATED FOR YOU</p><h2>Shop the collection</h2></div>
<form class="search-form" method="get"><input name="q" placeholder="Search pieces..." value="<?= e($search) ?>"><button aria-label="Search">Search</button></form></div>
<div class="filters"><a class="<?= $category===''?'active':'' ?>" href="index.php">All</a><a class="<?= $category==='Men'?'active':'' ?>" href="?category=Men">Men</a><a class="<?= $category==='Women'?'active':'' ?>" href="?category=Women">Women</a><a class="<?= $category==='Shoes'?'active':'' ?>" href="?category=Shoes">Shoes</a><a class="<?= $category==='Accessories'?'active':'' ?>" href="?category=Accessories">Accessories</a></div>
<div class="product-grid">
<?php foreach ($products as $p): ?><article class="product-card">
<a class="product-image" href="product.php?id=<?= (int)$p['id'] ?>"><img src="uploads/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" onerror="this.src='assets/placeholder.svg'"><span><?= e($p['category']) ?></span></a>
<div class="product-info"><div><h3><?= e($p['name']) ?></h3><p><?= money((float)$p['price']) ?></p></div><form method="post" action="cart.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="action" value="add"><button class="add-button" aria-label="Add to bag">+</button></form></div>
</article><?php endforeach; ?>
<?php if (!$products): ?><p>No products found. Try another search.</p><?php endif; ?>
</div></section>
<section class="story"><p class="eyebrow">OUR POINT OF VIEW</p><h2>Less noise. More<br><em>personal style.</em></h2><p>Zy Collection brings together wearable pieces, thoughtful details and a modern approach to everyday fashion.</p></section>
</main><footer><a class="brand" href="index.php">ZY<span>COLLECTION</span></a><p>© <?= date('Y') ?> Zy Collection. All rights reserved.</p><a href="admin/login.php">Admin</a></footer>
<script src="assets/js/app.js"></script></body></html>
