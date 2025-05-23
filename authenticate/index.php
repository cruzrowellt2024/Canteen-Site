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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .bg-fade {
      transition: background-image 0.8s ease-in-out;
    }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-b from-green-200 to-gray-300">

  <div class="w-full max-w-6xl h-[36rem] bg-white rounded-xl overflow-hidden shadow-lg flex flex-col md:flex-row">

    <div class="relative md:w-1/2 w-full overflow-hidden">
      <div id="slider-track" class="flex w-full h-full transition-transform duration-1000 ease-in-out">
        <img src="../burger.jpeg" class="w-full object-cover" />
        <img src="../siomairice.jpeg" class="w-full object-cover" />
        <img src="../sisig.jpg" class="w-full object-cover" />
      </div>
      <div class="absolute inset-0 bg-black/30"></div>
    </div>


    <form method="POST" class="md:w-1/2 w-full px-10 flex flex-col justify-center bg-white z-10">
      <h2 class="text-5xl font-bold mb-6 text-center">SwiftBites</h2>
      <h2 class="text-2xl font-bold mb-6">Login</h2>

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

  </div>

  <script>
    const sliderTrack = document.getElementById("slider-track");
    const totalSlides = sliderTrack.children.length;
    let index = 0;

    function slide() {
      index = (index + 1) % totalSlides;
      sliderTrack.style.transform = `translateX(-${index * 100}%)`;
    }

    setInterval(slide, 3000);
  </script>

</body>

</html>