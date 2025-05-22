<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'error' => 'Invalid request method']);
  exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$category = isset($_POST['category']) ? trim($_POST['category']) : '';

$validCategories = ['Snacks', 'Beverages', 'Meals', 'Drinks'];

if ($id <= 0 || !in_array($category, $validCategories)) {
  echo json_encode(['success' => false, 'error' => 'Invalid data']);
  exit;
}

try {
  $stmt = $pdo->prepare("UPDATE products SET category = :category WHERE id = :id");
  $stmt->execute([
    'category' => $category,
    'id' => $id
  ]);

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'error' => 'Server error']);
}
