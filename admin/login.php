<?php
require_once '../config.php';
if(is_admin()){header('Location: index.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$email=trim($_POST['email']??'');$pass=$_POST['password']??'';
$s=$pdo->prepare("SELECT * FROM admins WHERE email=?");$s->execute([$email]);$a=$s->fetch();
if($a && password_verify($pass,$a['password_hash'])){session_regenerate_id(true);$_SESSION['admin_id']=$a['id'];$_SESSION['admin_name']=$a['name'];header('Location: index.php');exit;}
$error='Invalid email or password.';}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin login</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><main class="login-card"><a class="brand" href="../index.php">ZY<span>COLLECTION</span></a><h1>Admin login</h1><?php if($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post" class="checkout-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email<input required type="email" name="email"></label><label>Password<input required type="password" name="password"></label><button class="button">Sign in</button></form><p><a href="../index.php">← Store</a></p></main></body></html>
