<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['product_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'add' && $id > 0) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id=?");
        $stmt->execute([$id]); $product = $stmt->fetch();
        if ($product && (int)$product['stock'] > 0) {
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            $_SESSION['cart'][$id] = min((int)$product['stock'], (int)($_SESSION['cart'][$id] ?? 0) + $qty);
        }
    } elseif ($action === 'remove') { unset($_SESSION['cart'][$id]); }
    elseif ($action === 'update') {
        foreach (($_POST['quantities'] ?? []) as $pid => $qty) {
            $pid = (int)$pid; $qty = (int)$qty;
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id=?"); $stmt->execute([$pid]); $p = $stmt->fetch();
            if (!$p || $qty <= 0) unset($_SESSION['cart'][$pid]);
            else $_SESSION['cart'][$pid] = min($qty, (int)$p['stock']);
        }
    }
    header('Location: cart.php'); exit;
}
$items=[]; $total=0;
if ($_SESSION['cart']) {
    $ids=array_keys($_SESSION['cart']); $marks=implode(',', array_fill(0,count($ids),'?'));
    $stmt=$pdo->prepare("SELECT * FROM products WHERE id IN ($marks)"); $stmt->execute($ids);
    foreach($stmt->fetchAll() as $p) { $qty=(int)$_SESSION['cart'][$p['id']]; $line=$qty*(float)$p['price']; $total+=$line; $items[]=['p'=>$p,'qty'=>$qty,'line'=>$line]; }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Your Bag — Zy Collection</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="site-header"><a class="brand" href="index.php">ZY<span>COLLECTION</span></a><nav><a href="index.php">Continue shopping</a></nav><a class="cart-link" href="cart.php">Bag (<?= cart_count() ?>)</a></header>
<main class="section narrow"><p class="eyebrow">YOUR SELECTION</p><h1>Your shopping bag</h1>
<?php if (!$items): ?><p>Your bag is empty.</p><a class="button" href="index.php">Browse collection</a><?php else: ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update">
<?php foreach($items as $it): $p=$it['p']; ?><div class="cart-row"><img src="uploads/<?= e($p['image']) ?>" onerror="this.src='assets/placeholder.svg'" alt=""><div class="cart-desc"><h3><?= e($p['name']) ?></h3><p><?= money((float)$p['price']) ?></p></div><input type="number" min="0" max="<?= (int)$p['stock'] ?>" name="quantities[<?= (int)$p['id'] ?>]" value="<?= $it['qty'] ?>"><strong><?= money($it['line']) ?></strong></div><?php endforeach; ?>
<button class="button button-outline" type="submit">Update bag</button></form>
<div class="total-row"><span>Subtotal</span><strong><?= money($total) ?></strong></div><a class="button" href="checkout.php">Continue to checkout</a><?php endif; ?></main></body></html>
