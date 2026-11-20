<?php
require_once '../config.php';require_admin();
$stats=$pdo->query("SELECT COUNT(*) orders_count, COALESCE(SUM(total),0) revenue FROM orders WHERE status<>'Cancelled'")->fetch();
$productsCount=(int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$pending=(int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
$orders=$pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 8")->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin dashboard</title><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<header class="site-header"><a class="brand" href="../index.php">ZY<span>ADMIN</span></a><nav><a href="index.php">Dashboard</a><a href="products.php">Products</a><a href="orders.php">Orders</a></nav><a href="logout.php">Logout</a></header>
<main class="section"><p class="eyebrow">STORE MANAGEMENT</p><h1>Welcome, <?= e($_SESSION['admin_name']??'Admin') ?></h1><div class="stats-grid"><div class="stat"><span>Total orders</span><strong><?= (int)$stats['orders_count'] ?></strong></div><div class="stat"><span>Revenue (non-cancelled)</span><strong><?= money((float)$stats['revenue']) ?></strong></div><div class="stat"><span>Products</span><strong><?= $productsCount ?></strong></div><div class="stat"><span>Pending orders</span><strong><?= $pending ?></strong></div></div>
<h2>Recent orders</h2><div class="table-wrap"><table><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr><?php foreach($orders as $o): ?><tr><td>#<?= (int)$o['id'] ?></td><td><?= e($o['customer_name']) ?></td><td><?= money((float)$o['total']) ?></td><td><?= e($o['payment_method']) ?> / <?= e($o['payment_status']) ?></td><td><?= e($o['status']) ?></td><td><a href="orders.php">Manage</a></td></tr><?php endforeach; ?></table></div></main></body></html>
