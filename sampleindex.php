<?php
session_start();

$error = '';

if (isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check both tables: admin and staff
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = 'admin';

        if (isset($_POST['remember'])) {
            setcookie('rememberme', "admin:" . $user['id'], time() + (86400 * 30), "/");
        }

        header('Location: dashboard.php');
        exit();
    }

    // Try staff next
    $stmt = $pdo->prepare("SELECT * FROM staffs WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = 'staff';
        $_SESSION['stall_id'] = $user['stall_id'];

        if (isset($_POST['remember'])) {
            setcookie('rememberme', "staff:" . $user['id'], time() + (86400 * 30), "/");
        }

        header('Location: dashboard.php');
        exit();
    }

    $error = "Invalid username or password.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <form method="POST" class="bg-white p-8 rounded shadow-md w-96">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <?php if ($error): ?>
      <div class="mb-4 text-red-600 text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <label class="block mb-2 font-semibold" for="username">Username</label>
    <input type="text" name="username" id="username" required
      class="w-full p-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" />

    <label class="block mb-2 font-semibold" for="password">Password</label>
    <input type="password" name="password" id="password" required
      class="w-full p-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" />

    <label class="inline-flex items-center mb-6">
      <input type="checkbox" name="remember" class="form-checkbox h-5 w-5 text-blue-600" />
      <span class="ml-2 text-gray-700">Remember Me</span>
    </label>

    <button type="submit"
      class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
  </form>
</body>
</html>