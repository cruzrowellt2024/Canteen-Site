<?php
require 'authenticate/db.php';

$showModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message
            ]);
            $showModal = true;
        } catch (PDOException $e) {
            $error = "Failed to send message.";
        }
    } else {
        $error = "Please fill out all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

  <!-- Contact Form -->
  <form method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-md space-y-4">
    <h2 class="text-2xl font-semibold">Contact Us</h2>

    <?php if (!empty($error)): ?>
      <p class="text-red-600 font-medium"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <input type="text" name="name" required placeholder="Your Name" class="w-full border p-2 rounded" />
    <input type="email" name="email" required placeholder="Your Email" class="w-full border p-2 rounded" />
    <textarea name="message" required placeholder="Your Message" rows="5" class="w-full border p-2 rounded"></textarea>
    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Send Message</button>
  </form>

  <!-- Modal -->
  <?php if ($showModal): ?>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 shadow-lg max-w-sm w-full text-center">
      <h3 class="text-xl font-semibold mb-4">Thank you!</h3>
      <p class="mb-6">Your message has been sent successfully.</p>
      <a href="about.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Go to About</a>
    </div>
  </div>
  <?php endif; ?>

</body>
</html>