<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    list($type, $id) = explode(':', $_COOKIE['rememberme'], 2);

    if ($type === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = 'admin';
        }
    } elseif ($type === 'staff') {
        $stmt = $pdo->prepare("SELECT * FROM staffs WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = 'staff';
            $_SESSION['stall_id'] = $user['stall_id'];
        }
    }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}