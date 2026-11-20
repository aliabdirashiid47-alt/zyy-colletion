<?php
// Run once from localhost, then DELETE this file immediately.
require_once 'config.php';
$name='Store Admin'; $email='admin@zycollection.local'; $password='ChangeMe123!';
$hash=password_hash($password,PASSWORD_DEFAULT);
$s=$pdo->prepare("INSERT INTO admins(name,email,password_hash) VALUES(?,?,?)");
try {$s->execute([$name,$email,$hash]); echo "Admin created. Email: ".e($email)." Password: ".e($password).". Delete create_admin.php now and change this password."; }
catch(PDOException $e){echo "Admin may already exist. Delete this file after checking.";}
