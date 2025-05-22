<?php
header('Content-Type: application/json');
require_once 'db.php'; // uses your $pdo connection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'error' => 'Invalid request method']);
  exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$change = isset($_POST['change']) ? intval($_POST['change']) : 0;

if ($id <= 0) {
  echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
  exit;
}

try {
  $pdo->beginTransaction();

  $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
  $stmt->execute(['id' => $id]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$product) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Product not found']);
    exit;
  }

  $currentStock = (int)$product['stock'];
  $newStock = max(0, $currentStock + $change); // clamp to zero

  $update = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
  $update->execute([
    'stock' => $newStock,
    'id' => $id
  ]);

  $pdo->commit();

  echo json_encode(['success' => true, 'newStock' => $newStock]);
} catch (Exception $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  echo json_encode(['success' => false, 'error' => 'Server error']);
}