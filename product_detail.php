<?php
require 'authenticate/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT p.*, s.name AS stall_name, s.location AS stall_location,  s.image_url AS stall_image_url
    FROM products p
    LEFT JOIN stalls s ON p.stall_id = s.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

$staff_stmt = $pdo->prepare("SELECT name, image_url FROM staffs WHERE stall_id = ?");
$staff_stmt->execute([$product['stall_id']]);
$staff_list = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SwiftBites - <?= htmlspecialchars($product['name']) ?></title>
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
        </div>
    </header>

    <main class="max-w-6xl max-h-10xl mx-auto p-6">
        <div class="bg-white rounded shadow p-6 flex flex-col md:flex-row gap-6">

            <div class="md:w-1/2 flex flex-col">
                <img
                    src="<?= htmlspecialchars($product['image_url'] ?: 'placeholder.jpg') ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    class="w-full h-64 object-cover rounded"
                    onerror="this.onerror=null;this.src='placeholder.jpg';" />

                <div class="mt-4 text-center">
                    <h2 class="text-3xl font-semibold mb-4"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="text-green-600 text-2xl font-bold mb-4">$<?= number_format($product['price'], 2) ?></p>
                    <p class="text-gray-700 mb-6"><?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?></p>
                    <a href="index.php" class="inline-block bg-gray-700 text-white px-10 py-2 rounded hover:bg-gray-400 transition">Back to Menu</a>
                </div>
            </div>

            <div class="md:w-1/2 mt-6 md:mt-0 border-l-4 border-gray-300 rounded md:pl-6">
                <div class="flex space-x-4 mb-4">
                    <img src="<?= htmlspecialchars($product['stall_image_url'] ?? 'placeholder.jpg') ?>"
                        class="w-40 h-40 object-cover rounded"
                        onerror="this.onerror=null;this.src='placeholder.jpg';"
                        alt="Stall Image">
                    <div>
                        <p class="text-2xl font-bold"><?= htmlspecialchars($product['stall_name']) ?></p>
                        <p class="text-sm text-gray-600">Location: <?= htmlspecialchars($product['stall_location']) ?></p>
                    </div>
                </div>

                <?php if ($staff_list): ?>
                    <p class="font-bold">Staff:</p>
                    <div class="flex flex-wrap gap-4 mt-2">
                        <?php foreach ($staff_list as $staff): ?>
                            <div class="flex items-center space-x-2">
                                <img src="<?= htmlspecialchars($staff['image_url'] ?? 'placeholder.jpg') ?>"
                                    class="w-10 h-10 object-cover rounded-full"
                                    onerror="this.onerror=null;this.src='placeholder.jpg';"
                                    alt="Staff Image">
                                <span><?= htmlspecialchars($staff['name']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No assigned staff.</p>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <footer class="bg-gray-800 border-t mt-10">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-gray-400">
            © <?= date('Y') ?> SwiftBites. All rights reserved.
        </div>
    </footer>
</body>

</html>