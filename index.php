<?php
require 'authenticate/db.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search) {
  $query .= " AND name LIKE :search";
  $params['search'] = "%$search%";
}

if ($category) {
  $query .= " AND category = :category";
  $params['category'] = $category;
}

switch ($sort) {
  case 'price_asc':
    $query .= " ORDER BY price ASC";
    break;
  case 'price_desc':
    $query .= " ORDER BY price DESC";
    break;
  case 'name_asc':
    $query .= " ORDER BY name ASC";
    break;
  case 'name_desc':
    $query .= " ORDER BY name DESC";
    break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
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

<body class="bg-gradient-to-b from-green-200 to-gray-300 text-gray-900">
  <header class="bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <ul class="flex items-center space-x-4">
        <h1 class="text-white text-xl font-bold mr-10">SwiftBites</h1>
        <li><a href="index.php" class="text-white text-l underline">Home</a></li>
        <li><a href="#" class="text-white text-l underline">About</a></li>
      </ul>
      <nav class="flex items-center space-x-4">
        <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="ml-4 flex space-x-2 items-center">
          <input
            type="text"
            name="search"
            placeholder="Search..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            class="border border-gray-300 rounded px-2 py-1 text-sm" />

          <select name="category" class="border border-gray-300 rounded px-2 py-1 text-sm">
            <option value="">All Categories</option>
            <option value="drinks" <?= ($_GET['category'] ?? '') === 'drinks' ? 'selected' : '' ?>>Drinks</option>
            <option value="meals" <?= ($_GET['category'] ?? '') === 'meals' ? 'selected' : '' ?>>Meals</option>
            <option value="snacks" <?= ($_GET['category'] ?? '') === 'snacks' ? 'selected' : '' ?>>Snacks</option>
          </select>

          <select name="sort" class="border border-gray-300 rounded px-2 py-1 text-sm">
            <option value="">Sort By</option>
            <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name: A-Z</option>
            <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Name: Z-A</option>
          </select>

          <button type="submit" class="bg-green-600 text-white px-4 py-1.5 rounded text-sm hover:bg-green-700">
            Apply
          </button>
        </form>
      </nav>
    </div>
  </header>

  <main class="p-6 max-w-7xl mx-auto">
    <div class="bg-gray-900 text-white rounded p-1 mb-6 text-center shadow">
      <div class="relative w-full h-[300px] overflow-hidden">
        <div id="slider-track" class="flex w-full h-full transition-transform duration-1000 ease-in-out" style="transform: translateX(0%)">
          <img src="burger.jpeg" class="min-w-full h-full object-cover object-center" />
          <img src="siomairice.jpeg" class="min-w-full h-full object-cover object-center" />
          <img src="sisig.jpg" class="min-w-full h-full object-cover object-center" />
        </div>
        <div class="absolute inset-0 bg-black/50 pointer-events-none"></div>

        <div class="absolute inset-0 flex items-center justify-start ml-10 pointer-events-none">
          <h2 class="text-white text-2xl sm:text-3xl md:text-4xl font-bold drop-shadow-md">For Study-Time Cravings</h2>
        </div>
      </div>
    </div>

    <h2 class="text-3xl font-semibold mb-6">Menu</h2>
    <?php if ($search): ?>
      <p class="mb-4 text-sm text-gray-600">Showing results for: <strong><?= htmlspecialchars($search) ?></strong></p>
    <?php endif; ?>


    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if (!$products): ?>
        <p class="text-gray-700 col-span-full">No products found.</p>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
          <?php if ($product['stock'] <= 0) continue; ?>
          <a href="product_detail.php?id=<?= $product['id'] ?>">
            <div class="bg-blue-100 rounded shadow flex flex-col transition-transform duration-300 transform hover:scale-105">
              <div class="relative w-full h-48 rounded overflow-hidden">
                <img
                  src="<?= htmlspecialchars($product['image_url'] ?: 'placeholder.jpg') ?>"
                  alt="<?= htmlspecialchars($product['name']) ?>"
                  class="w-full h-full object-cover"
                  onerror="this.onerror=null;this.src='placeholder.jpg';" />
                <div class="absolute bottom-0 left-0 w-full bg-gray-500 bg-opacity-60 text-white text-start font-bold text-xl py-1 px-5 backdrop-blur-md">
                  <?= htmlspecialchars($product['name']) ?>
                  <p class="text-green-400 font-bold text-xl text-start">₱<?= number_format($product['price'], 2) ?></p>
                </div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <footer class="bg-gray-800 border-t mt-10">
    <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-gray-400">
      © <?= date('Y') ?> SwiftBites. All rights reserved.
    </div>
  </footer>
</body>
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

</html>