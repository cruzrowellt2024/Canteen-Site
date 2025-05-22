<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'staff') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stallId = floatval($_POST['stall_id'] ?? 0);
    $category = $_POST['category'] ?? '';
    $stock = $_POST['stock'] ?? '';

    if ($name === '' || $price <= 0) {
        echo "Invalid input.";
        exit;
    }

    // Image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            echo "Invalid image type. Only PNG, JPG, and JPEG are allowed.";
            exit;
        }

        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/' . $filename;
        } else {
            echo "Image upload failed.";
            exit;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, image_url, price, stall_id, description, category, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $imagePath, $price, $stallId, $description, $category, $stock])) {
            header("Location: dashboard.php");
        } else {
            echo "Database insert failed.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
