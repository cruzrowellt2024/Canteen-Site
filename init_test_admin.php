<?php
$host = 'localhost';
$dbname = 'canteen_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )");

    $plainPassword = 'password123';
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM admin WHERE username = ?");
    $stmt->execute(['admin']);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $existing['id']]);
        echo "Admin password updated.\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
        echo "Admin user created.\n";
    }

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($plainPassword, $user['password'])) {
        echo "Login test passed! Password verified.\n";
    } else {
        echo "Login test failed! Password verification failed.\n";
    }

} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}