<?php
require_once '../config.php';require_admin();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$action=$_POST['action']??'';
 if($action==='delete'){$s=$pdo->prepare("DELETE FROM products WHERE id=?");$s->execute([(int)$_POST['id']]);header('Location: products.php');exit;}
 if($action==='save'){
  $id=(int)($_POST['id']??0);$name=trim($_POST['name']??'');$category=$_POST['category']??'Women';$desc=trim($_POST['description']??'');$price=(float)($_POST['price']??0);$stock=(int)($_POST['stock']??0);$featured=isset($_POST['featured'])?1:0;$image=$_POST['old_image']??'placeholder.svg';
  if(isset($_FILES['image']) && $_FILES['image']['error']===UPLOAD_ERR_OK){
   $ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));$allowed=['jpg','jpeg','png','webp'];
   if(!in_array($ext,$allowed,true) || $_FILES['image']['size']>4*1024*1024)$error='Image must be JPG, PNG or WEBP and under 4MB.';
   else {$image=bin2hex(random_bytes(12)).'.'.$ext;move_uploaded_file($_FILES['image']['tmp_name'],__DIR__.'/../uploads/'.$image);}
  }
  if(!$error && $name && in_array($category,['Men','Women','Shoes','Accessories'],true) && $price>0 && $stock>=0){
   if($id){$s=$pdo->prepare("UPDATE products SET name=?,category=?,description=?,price=?,stock=?,image=?,featured=? WHERE id=?");$s->execute([$name,$category,$desc,$price,$stock,$image,$featured,$id]);}
   else {$s=$pdo->prepare("INSERT INTO products(name,category,description,price,stock,image,featured) VALUES(?,?,?,?,?,?,?)");$s->execute([$name,$category,$desc,$price,$stock,$image,$featured]);}
   header('Location: products.php');exit;
  } elseif(!$error)$error='Check the product details.';
 }
}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM products WHERE id=?");$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$products=$pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Products</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><header class="site-header"><a class="brand" href="index.php">ZY<span>ADMIN</span></a><nav><a href="index.php">Dashboard</a><a href="products.php">Products</a><a href="orders.php">Orders</a></nav><a href="logout.php">Logout</a></header><main class="section"><h1>Products</h1><?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?>
<div class="admin-form"><h2><?= $edit?'Edit product':'Add product' ?></h2><form method="post" enctype="multipart/form-data" class="checkout-form"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>"><input type="hidden" name="old_image" value="<?=e($edit['image']??'placeholder.svg')?>"><label>Product name<input required name="name" value="<?=e($edit['name']??'')?>"></label><label>Category<select name="category"><?php foreach(['Men','Women','Shoes','Accessories'] as $c):?><option <?=($edit['category']??'Women')===$c?'selected':''?>><?=$c?></option><?php endforeach;?></select></label><label>Description<textarea name="description"><?=e($edit['description']??'')?></textarea></label><label>Price (USD)<input required type="number" min="0.01" step="0.01" name="price" value="<?=e((string)($edit['price']??''))?>"></label><label>Stock<input required type="number" min="0" name="stock" value="<?=e((string)($edit['stock']??0))?>"></label><label>Product image<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></label><label class="check-label"><input type="checkbox" name="featured" <?=!empty($edit['featured'])?'checked':''?>> Featured product</label><button class="button">Save product</button><?php if($edit):?><a href="products.php">Cancel edit</a><?php endif;?></form></div>
<h2>All products</h2><div class="table-wrap"><table><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr><?php foreach($products as $p):?><tr><td><?=e($p['name'])?></td><td><?=e($p['category'])?></td><td><?=money((float)$p['price'])?></td><td><?=(int)$p['stock']?></td><td><a href="?edit=<?=(int)$p['id']?>">Edit</a> <form class="inline-form" method="post" onsubmit="return confirm('Delete this product?')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$p['id']?>"><button class="text-button">Delete</button></form></td></tr><?php endforeach;?></table></div></main></body></html>
