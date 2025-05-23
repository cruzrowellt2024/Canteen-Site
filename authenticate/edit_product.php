<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        die('Missing staff ID');
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

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


    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$name, $price, $imagePath, $id]);

    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        die('Product not found');
    }
}
