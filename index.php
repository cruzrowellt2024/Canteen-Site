<?php
require 'authenticate/db.php'; // your PDO connection

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SwiftBites - Products</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">SwiftBites</h1>
      <nav>
        <ul class="flex space-x-4">
          <li><a href="index.php" class="text-blue-500 hover:underline">Home</a></li>
          <li><a href="#" class="text-blue-500 hover:underline">About</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="p-6 max-w-7xl mx-auto">
    <h2 class="text-3xl font-semibold mb-6">Menu</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if (!$products): ?>
        <p class="text-gray-700 col-span-full">No products found.</p>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
          <div class="bg-white rounded shadow p-4 flex flex-col">
            <img 
              src="<?= htmlspecialchars($product['image_url'] ?: 'placeholder.jpg') ?>" 
              alt="<?= htmlspecialchars($product['name']) ?>" 
              class="w-full h-48 object-cover rounded mb-4"
              onerror="this.onerror=null;this.src='placeholder.jpg';"
            />
            <h3 class="font-semibold text-lg mb-2"><?= htmlspecialchars($product['name']) ?></h3>
            <p class="text-indigo-600 font-bold text-xl mb-4">$<?= number_format($product['price'], 2) ?></p>
            <a href="product_detail.php?id=<?= $product['id'] ?>" 
               class="mt-auto bg-blue-600 text-white text-center py-2 rounded hover:bg-blue-700 transition">
              View Details
            </a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <footer class="bg-white border-t mt-10">
    <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
      © <?= date('Y') ?> SwiftBites. All rights reserved.
    </div>
  </footer>
</body>

</html>
