<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stallName = trim($_POST['name']);
    $stallLocation = trim($_POST['location']);

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
    if ($stallName) {
        $stmt = $pdo->prepare("INSERT INTO stalls (name, image_url, location) VALUES (?, ?, ?)");
        $stmt->execute([$stallName, $imagePath, $stallLocation]);

        header("Location: dashboard.php");
        exit;
    } else {
        echo "Stall name is required.";
    }
} else {
    echo "Invalid request.";
}
