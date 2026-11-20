<?php
require_once 'config.php';
if (!$_SESSION['cart']) { header('Location: cart.php'); exit; }
$ids=array_keys($_SESSION['cart']); $marks=implode(',',array_fill(0,count($ids),'?'));
$stmt=$pdo->prepare("SELECT * FROM products WHERE id IN ($marks)"); $stmt->execute($ids); $products=$stmt->fetchAll();
$total=0; foreach($products as $p){$total+=(float)$p['price']*(int)$_SESSION['cart'][$p['id']];}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $name=trim($_POST['customer_name']??''); $phone=trim($_POST['phone']??''); $email=trim($_POST['email']??'');
    $address=trim($_POST['address']??''); $method=$_POST['payment_method']??''; $reference=trim($_POST['payment_reference']??'');
    if(!$name||!$phone||!$address||!$reference||!in_array($method,['Zaad','eDahab'],true)) $error='Please complete all required fields.';
    else {
        try {
            $pdo->beginTransaction();
            // Re-check stock and prices inside transaction before creating order.
            $fresh=$pdo->prepare("SELECT * FROM products WHERE id=? FOR UPDATE");
            $verified=[]; $grand=0;
            foreach($_SESSION['cart'] as $pid=>$qty){
                $fresh->execute([(int)$pid]); $p=$fresh->fetch();
                if(!$p || (int)$p['stock'] < (int)$qty) throw new RuntimeException('A product is out of stock or quantity is no longer available.');
                $grand+=(float)$p['price']*(int)$qty; $verified[]=['p'=>$p,'qty'=>(int)$qty];
            }
            $ins=$pdo->prepare("INSERT INTO orders(customer_name,phone,email,address,payment_method,payment_reference,total) VALUES(?,?,?,?,?,?,?)");
            $ins->execute([$name,$phone,$email?:null,$address,$method,$reference,$grand]); $orderId=(int)$pdo->lastInsertId();
            $itemIns=$pdo->prepare("INSERT INTO order_items(order_id,product_id,product_name,price,quantity) VALUES(?,?,?,?,?)");
            $stockUpd=$pdo->prepare("UPDATE products SET stock=stock-? WHERE id=?");
            foreach($verified as $v){$p=$v['p'];$qty=$v['qty'];$itemIns->execute([$orderId,$p['id'],$p['name'],$p['price'],$qty]);$stockUpd->execute([$qty,$p['id']]);}
            $pdo->commit(); $_SESSION['cart']=[];
            header('Location: order-success.php?id='.$orderId); exit;
        } catch(Throwable $ex) {
            if($pdo->inTransaction()) $pdo->rollBack();
            $error=$ex instanceof RuntimeException ? $ex->getMessage() : 'Could not place your order. Please try again.';
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Checkout — Zy Collection</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="site-header"><a class="brand" href="index.php">ZY<span>COLLECTION</span></a><a href="cart.php">Back to bag</a></header>
<main class="section narrow"><p class="eyebrow">ALMOST YOURS</p><h1>Checkout</h1><?php if($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<div class="payment-note"><strong>Payment instructions</strong><p>Send the total of <?= money($total) ?> using Zaad or eDahab to the store number supplied by the store owner. Enter the transaction/reference ID below. Your order remains pending until the administrator verifies the payment.</p></div>
<form method="post" class="checkout-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Full name *<input required name="customer_name" value="<?= e($_POST['customer_name']??'') ?>"></label>
<label>Phone number *<input required name="phone" value="<?= e($_POST['phone']??'') ?>"></label>
<label>Email (optional)<input type="email" name="email" value="<?= e($_POST['email']??'') ?>"></label>
<label>Delivery address / area *<textarea required name="address"><?= e($_POST['address']??'') ?></textarea></label>
<label>Payment method *<select name="payment_method" required><option value="">Choose method</option><option>Zaad</option><option>eDahab</option></select></label>
<label>Transaction/reference ID *<input required name="payment_reference" placeholder="Enter your payment reference"></label>
<div class="total-row"><span>Total</span><strong><?= money($total) ?></strong></div><button class="button" type="submit">Place order</button></form></main></body></html>
