<?php
require 'auth.php';
require 'db.php';

if (!isset($_GET['id'])) {
  die('Missing stall ID');
}

$id = $_GET['id'];

if ($_SESSION['user_type'] !== 'admin') {
  die('Unauthorized');
}

$stmt = $pdo->prepare("DELETE FROM stalls WHERE id = ?");
$stmt->execute([$id]);

header("Location: dashboard.php");
exit;