<?php
require 'auth.php';
require 'db.php';

$userType = $_SESSION['user_type'];
$userStall = $_SESSION['stall_id'] ?? null;

if ($userType === 'staff') {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE stall_id = ?");
  $stmt->execute([$userStall]);
  $productCount = $stmt->fetchColumn();


  $stmt = $pdo->prepare("SELECT name FROM stalls WHERE id = ?");
  $stmt->execute([$userStall]);
  $stallName = $stmt->fetchColumn();

  $staffList = $pdo->prepare("SELECT * FROM staffs WHERE stall_id = ?");
  $staffList->execute([$userStall]);


  $productList = $pdo->prepare("SELECT * FROM products WHERE stall_id = ?");
  $productList->execute([$userStall]);
} else {

  $stallCount = $pdo->query("SELECT COUNT(*) FROM stalls")->fetchColumn();
  $staffCount = $pdo->query("SELECT COUNT(*) FROM staffs")->fetchColumn();
  $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
  $messageCount = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

  $staffList = $pdo->query("SELECT * FROM staffs");
  $productList = $pdo->query("SELECT * FROM products");
  $stallList = $pdo->query("SELECT * FROM stalls");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

  <nav class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="font-bold text-xl">Dashboard</h1>
    <div>
      <span class="mr-4 text-gray-700">
        Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>
        (<?php echo htmlspecialchars($_SESSION['user_type']); ?>)

      </span>
      <?php if ($_SESSION['user_type'] === 'staff'): ?>
        <span class="mr-4 text-gray-600">Stall: <?php echo htmlspecialchars($stallName); ?></span>
      <?php endif; ?>
      <a href="logout.php"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition">Logout</a>
    </div>
  </nav>

  <main class="flex-grow p-6 space-y-8">

    <?php if ($userType === 'staff'): ?>

      <!-- Staff sees only Products related to their stall -->
      <div class="bg-white p-6 rounded shadow">
        <p class="text-gray-500 text-sm">Total Products in Your Stall</p>
        <h2 class="text-2xl font-bold text-indigo-600"><?php echo $productCount; ?></h2>
      </div>

      <!-- List Products -->
      <section class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($stallName); ?> Products</h2>
          <button id="openProductModal" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Product</button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600">
              <tr>
                <th class="p-3">Product Name</th>
                <th class="p-3">Category</th>
                <th class="p-3">Price</th>
                <th class="p-3">Stock</th>
                <th class="p-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($productList as $product): ?>
                <tr class="border-t">
                  <td class="p-3"><?php echo htmlspecialchars($product['name']); ?></td>
                  <td class="p-3">
                    <select onchange="updateCategory(<?= $product['id']; ?>, this.value)">
                      <?php
                      $categories = ['Snacks', 'Beverages', 'Meals', 'Drinks'];
                      foreach ($categories as $cat):
                      ?>
                        <option value="<?= $cat ?>" <?= ($product['category'] === $cat) ? 'selected' : '' ?>>
                          <?= $cat ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </td>

                  <td class="p-3"><?php echo htmlspecialchars($product['price']); ?></td>
                  <td class="p-3 flex items-center gap-2">
                    <button
                      class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300"
                      onclick="updateStock(<?= $product['id']; ?>, -1)">−</button>

                    <span id="stock-<?= $product['id']; ?>">
                      <?= htmlspecialchars($product['stock']); ?>
                    </span>

                    <button
                      class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300"
                      onclick="updateStock(<?= $product['id']; ?>, 1)">+</button>
                  </td>

                  <td class="p-3 space-x-2">
                    <a href="#"
                      class="text-blue-600 hover:underline editProductBtn"
                      data-id="<?= $product['id']; ?>"
                      data-name="<?= htmlspecialchars($product['name']); ?>"
                      data-price="<?= $product['price']; ?>"
                      data-image="<?= $product['image_url']; ?>"> Edit </a>
                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="text-red-600 hover:underline">Delete</a>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Show staff info of their stall -->
      <section class="bg-white p-6 rounded shadow mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Stall Staff</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600">
              <tr>
                <th class="p-3">Name</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($staffList as $staff): ?>
                <tr class="border-t">
                  <td class="p-3"><?php echo htmlspecialchars($staff['username']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
  </main>

  <!-- Add Product Modal -->
  <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded p-6 w-full max-w-md relative">
      <button type="button" id="closeProductModal" class="absolute top-4 right-5 text-gray-600 hover:text-gray-900 font-bold text-2xl">×</button>
      <h2 class="text-xl font-bold mb-4">Add Product</h2>

      <form id="productForm" method="POST" action="add_product.php" enctype="multipart/form-data">
        <input
          type="text"
          name="name"
          placeholder="Product Name"
          class="w-full border px-3 py-2 rounded mb-4"
          required />

        <select name="category" class="w-full border px-3 py-2 rounded mb-4" required>
          <option value="" disabled selected>Select Category</option>
          <option value="snacks">Snacks</option>
          <option value="beverages">Beverages</option>
          <option value="meals">Meals</option>
          <option value="desserts">Desserts</option>
        </select>

        <input
          type="number"
          name="price"
          placeholder="Price"
          class="w-full border px-3 py-2 rounded mb-4"
          required />

        <input
          type="number"
          name="stock"
          placeholder="Stock"
          class="w-full border px-3 py-2 rounded mb-4"
          required />

        <input
          type="file"
          name="image"
          accept="image/*"
          class="w-full border px-3 py-2 rounded mb-4"
          required />

        <input type="hidden" name="stall_id" value="<?= $_SESSION['stall_id'] ?? '' ?>">

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save Product</button>
      </form>
    </div>
  </div>

  <div id="editProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded p-6 w-full max-w-md relative">
      <button type="button" id="closeEditProductModal" class="absolute top-4 right-5 text-gray-600 hover:text-gray-900 font-bold text-2xl">×</button>
      <h2 class="text-xl font-bold mb-4">Edit Product</h2>
      <form method="POST" action="edit_product.php" enctype="multipart/form-data">
        <input type="hidden" name="id" id="editProductId">
        <img src="../<?= $product['image_url']; ?>" alt="Current image" class="w-full h-40 object-cover mb-2" id="editProductPreview">

        <input
          type="text"
          name="name"
          id="editProductName"
          placeholder="Product Name"
          class="w-full border px-3 py-2 rounded mb-4"
          required />

        <input
          type="number"
          name="price"
          id="editProductPrice"
          placeholder="Price"
          class="w-full border px-3 py-2 rounded mb-4"
          required />

        <input
          type="file"
          name="image"
          id="editProductImage"
          accept="image/*"
          class="w-full border px-3 py-2 rounded mb-4"
          required />

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
      </form>
    </div>
  </div>

<?php else: ?>
  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div onclick="scrollToSection('stallSection')" class="cursor-pointer bg-white p-6 rounded shadow hover:shadow-lg transition">
      <p class="text-gray-500 text-sm">Total Stalls</p>
      <h2 class="text-2xl font-bold text-indigo-600"><?php echo $stallCount; ?></h2>
    </div>
    <div onclick="scrollToSection('staffSection')" class="cursor-pointer bg-white p-6 rounded shadow hover:shadow-lg transition">
      <p class="text-gray-500 text-sm">Total Staff</p>
      <h2 class="text-2xl font-bold text-indigo-600"><?php echo $staffCount; ?></h2>
    </div>
    <div onclick="scrollToSection('messageSection')" class="cursor-pointer bg-white p-6 rounded shadow hover:shadow-lg transition">
      <p class="text-gray-500 text-sm">Messages</p>
      <h2 class="text-2xl font-bold text-indigo-600"><?php echo $messageCount; ?></h2>
    </div>
  </div>

  <!-- Manage Staff Panel -->
  <section id="staffSection" class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-gray-800">Manage Staff</h2>
      <button id="openStaffModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Staff</button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-600">
          <tr>
            <th class="p-3">Name</th>
            <th class="p-3">Stall</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $staffList = $pdo->query("
            SELECT staffs.*, stalls.name AS stall_name 
            FROM staffs 
            LEFT JOIN stalls ON staffs.stall_id = stalls.id
          ");

          foreach ($staffList as $staff):
          ?>
            <tr class="border-t">
              <td class="p-3"><?php echo htmlspecialchars($staff['username']); ?></td>
              <td class="p-3"><?php echo htmlspecialchars($staff['stall_name']); ?></td>
              <td class="p-3 space-x-2">
                <a href="#"
                  class="text-blue-600 hover:underline editBtn"
                  data-id="<?= $staff['id']; ?>"
                  data-username="<?= htmlspecialchars($staff['username']); ?>"
                  data-stall-id="<?= $staff['stall_id']; ?>"> Edit </a>
                <a href="delete_staff.php?id=<?php echo $staff['id']; ?>" class="text-red-600 hover:underline">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>



  <!-- Manage Stalls Panel -->
  <section id="stallSection" class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-gray-800">Manage Stalls</h2>
      <button id="openStallModal" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Stall</button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-600">
          <tr>
            <th class="p-3">Stall Name</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stallList = $pdo->query("SELECT * FROM stalls");
          foreach ($stallList as $stall):
          ?>
            <tr class="border-t">
              <td class="p-3"><?php echo htmlspecialchars($stall['name']); ?></td>
              <td class="p-3 space-x-2">
                <a href="delete_stall.php?id=<?php echo $stall['id']; ?>" class="text-red-600 hover:underline">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section id="messageSection" class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-gray-800">Messages</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-600">
          <tr>
            <th class="p-3">Name</th>
            <th class="p-3">Email</th>
            <th class="p-3">Message</th>
            <th class="p-3">Submitted At</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $messageList = $pdo->query("SELECT * FROM messages");
          foreach ($messageList as $message):
          ?>
            <tr class="border-t">
              <td class="p-3"><?php echo htmlspecialchars($message['name']); ?></td>
              <td class="p-3"><?php echo htmlspecialchars($message['email']); ?></td>
              <td class="p-3"><?php echo htmlspecialchars($message['message']); ?></td>
              <td class="p-3"><?php echo htmlspecialchars($message['submitted_at']); ?></td>
              <td class="p-3">
                <button
                  class="text-blue-600 hover:underline view-btn"
                  data-message="<?php echo htmlspecialchars($message['message'], ENT_QUOTES); ?>">
                  View
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  </main>

  <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded shadow-lg max-w-lg w-full p-6 relative">
      <button id="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</button>
      <h3 class="text-xl font-semibold mb-4">Full Message</h3>
      <p id="modalMessage" class="whitespace-pre-wrap"></p>
    </div>
  </div>

  <div id="editStaffModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded p-6 w-full max-w-md relative">
      <button type="button" id="closeEditStaffModal" class="absolute top-4 right-5 text-gray-600 hover:text-gray-900 font-bold text-2xl">×</button>
      <h2 class="text-xl font-bold mb-4">Edit Staff</h2>
      <form method="POST" action="edit_staff.php">
        <input type="hidden" name="id" id="editStaffId">

        <label class="block mb-2">Username:</label>
        <input type="text" name="username" id="editStaffUsername" class="w-full border px-3 py-2 rounded mb-4" required>

        <label class="block mb-2">Stall:</label>
        <select name="stall_id" id="editStaffStallId" class="w-full border px-3 py-2 rounded mb-4" required>
          <?php
          $stallList = $pdo->query("SELECT id, name FROM stalls");
          foreach ($stallList as $stall) {
            echo '<option value="' . htmlspecialchars($stall['id']) . '">' . htmlspecialchars($stall['name']) . '</option>';
          }
          ?>
        </select>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
      </form>
    </div>
  </div>

  <!-- Add Stall Modal -->
  <div id="stallModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded p-6 w-full max-w-md relative">
      <button type="button" id="closeStallModal" class="absolute top-4 right-5 text-gray-600 hover:text-gray-900 font-bold text-2xl">×</button>
      <h2 class="text-xl font-bold mb-4">Add Stall</h2>
      <form id="stallForm" method="POST" action="add_stall.php" enctype="multipart/form-data">
        <input
          type="text"
          name="name"
          placeholder="Stall Name"
          class="w-full border px-3 py-2 rounded mb-4"
          required />
        <input
          type="file"
          name="image"
          accept="image/*"
          class="w-full border px-3 py-2 rounded mb-4" />
        <input
          type="text"
          name="location"
          placeholder="Location"
          class="w-full border px-3 py-2 rounded mb-2"
          required />
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save Stall</button>
      </form>
    </div>
  </div>

  <!-- Add Staff Modal -->
  <div id="staffModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded p-6 w-full max-w-md relative">
      <button type="button" id="closeStaffModal" class="absolute top-4 right-5 text-gray-600 hover:text-gray-900 font-bold text-2xl">×</button>
      <h2 class="text-xl font-bold mb-4">Add Staff</h2>
      <form id="staffForm" method="POST" action="add_staff.php" enctype="multipart/form-data">
        <input
          type="text"
          name="name"
          placeholder="Name"
          class="w-full border px-3 py-2 rounded mb-2"
          required />
        <input
          type="file"
          name="image"
          accept="image/*"
          class="w-full border px-3 py-2 rounded mb-4" />
        <input
          type="text"
          name="username"
          placeholder="Username"
          class="w-full border px-3 py-2 rounded mb-2"
          required />
        <input
          type="password"
          name="password"
          placeholder="Password"
          class="w-full border px-3 py-2 rounded mb-4"
          required />
        <select name="stall_id" class="w-full border px-3 py-2 rounded mb-4" required><?php $stallList = $pdo->query("SELECT * FROM stalls");
                                                                                      foreach ($stallList as $stall): ?>
            <option value="<?php echo $stall['id']; ?>">
              <?php echo htmlspecialchars($stall['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Staff</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<script>
  function setupModal(openId, modalId, closeId) {
    const modal = document.getElementById(modalId);
    document.getElementById(openId)?.addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById(closeId)?.addEventListener('click', () => modal.classList.add('hidden'));
  }

  setupModal('openStallModal', 'stallModal', 'closeStallModal');
  setupModal('openStaffModal', 'staffModal', 'closeStaffModal');
  setupModal('openProductModal', 'productModal', 'closeProductModal');
</script>

<script>
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.getElementById('editStaffId').value = this.dataset.id;
      document.getElementById('editStaffUsername').value = this.dataset.username;
      document.getElementById('editStaffStallId').value = this.dataset.stallId;
      document.getElementById('editStaffModal').classList.remove('hidden');
    });
  });

  document.getElementById('closeEditStaffModal').addEventListener('click', function() {
    document.getElementById('editStaffModal').classList.add('hidden');
  });
</script>

<script>
  document.querySelectorAll('.editProductBtn').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();

      document.getElementById('editProductId').value = this.dataset.id;
      document.getElementById('editProductName').value = this.dataset.name;
      document.getElementById('editProductPrice').value = this.dataset.price;

      document.getElementById('editProductModal').classList.remove('hidden');
    });
  });

  document.getElementById('closeEditProductModal').addEventListener('click', function() {
    document.getElementById('editProductModal').classList.add('hidden');
  });
</script>


<script>
  function updateStock(productId, change) {
    fetch('update_stock.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${productId}&change=${change}`
      })
      .then(res => res.text())
      .then(text => {
        console.log('Raw response:', text);
        let data = JSON.parse(text);
        if (data.success) {
          document.getElementById(`stock-${productId}`).innerText = data.newStock;
        } else {
          alert('Failed to update stock');
        }
      })
      .catch(err => {
        console.error('Fetch error:', err);
        alert('Error updating stock');
      });
  }

  function updateCategory(productId, newCategory) {
    fetch('update_category.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${productId}&category=${encodeURIComponent(newCategory)}`
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert('Failed to update category');
        }
      })
      .catch(err => {
        console.error('Fetch error:', err);
        alert('Error updating category');
      });
  }
</script>

<script>
  function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
      section.scrollIntoView({
        behavior: 'smooth'
      });
    }
  }
</script>

<script>
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const message = btn.getAttribute('data-message');
      document.getElementById('modalMessage').textContent = message;
      document.getElementById('modal').classList.remove('hidden');
    });
  });

  document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('modal').classList.add('hidden');
  });

  // Optional: close modal when clicking outside modal content
  document.getElementById('modal').addEventListener('click', e => {
    if (e.target === e.currentTarget) {
      document.getElementById('modal').classList.add('hidden');
    }
  });
</script>

</body>

</html>