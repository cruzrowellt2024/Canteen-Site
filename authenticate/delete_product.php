<?php
require 'auth.php';
require 'db.php';

if (!isset($_GET['id'])) {
  die('Missing product ID');
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: dashboard.php");
exit;