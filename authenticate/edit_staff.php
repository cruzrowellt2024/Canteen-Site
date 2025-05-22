<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        die('Missing staff ID');
    }

    $id = $_POST['id'];
    $username = $_POST['username'];
    $stall_id = $_POST['stall_id'];

    $stmt = $pdo->prepare("UPDATE staffs SET username = ?, stall_id = ? WHERE id = ?");
    $stmt->execute([$username, $stall_id, $id]);

    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM staffs WHERE id = ?");
    $stmt->execute([$id]);
    $staff = $stmt->fetch();

    if (!$staff) {
        die('Staff not found');
    }
}