<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $stall_id = $_POST['stall_id'];

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

    if ($username && $name && $password && $stall_id) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO staffs (username, name, password, stall_id, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $name, $hashedPassword, $stall_id, $imagePath]);

        header("Location: dashboard.php");
        exit;
    } else {
        echo "All fields are required.";
    }
} else {
    echo "Invalid request.";
}
