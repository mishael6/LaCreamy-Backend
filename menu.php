<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_name = $_POST['item_name'];
    $item_price = floatval($_POST['item_price']);
    $item_image = $_POST['item_image'];
    
    // Check if item already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['name'] === $item_name) {
            $cart_item['quantity']++;
            $found = true;
            break;
        }
    }
    
    // Add new item if not found
    if (!$found) {
        $_SESSION['cart'][] = [
            'name' => $item_name,
            'price' => $item_price,
            'image' => $item_image,
            'quantity' => 1
        ];
    }
    
    header("Location: menu.php");
    exit();
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $remove_index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$remove_index])) {
        unset($_SESSION['cart'][$remove_index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
    header("Location: menu.php");
    exit();
}

// Calculate cart total
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LA CREAMY CAKEIST - Menu</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #fff9f3;
      color: #3a2a1a;
      margin: 0;
      padding: 0;
    }
    header {
      background: #fffaf4;
      padding: 15px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid #d4b47f;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .logo-container {
      display: flex;
      align-items: center;
    }
    .logo {
      height: 60px;
      width: auto;
      border-radius: 8px;
      margin-right: 10px;
    }
    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
    }
    nav ul li {
      display: inline-block;
      margin: 0 10px;
    }
    nav ul li a {
      text-decoration: none;
      color: #3a2a1a;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    nav ul li a:hover {
      color: #b08e58;
    }
    nav ul li a.active {
      color: #c5a46d;
      font-weight: 600;
    }
    .cart-icon {
      position: relative;
      cursor: pointer;
      font-size: 1.5em;
      padding: 8px 15px;
      background: #c5a46d;
      color: white;
      border-radius: 8px;
      transition: background 0.3s;
    }
    .cart-icon:hover {
      background: #b08e58;
    }
    .cart-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #dc3545;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 0.6em;
      font-weight: bold;
    }
    .menu-section {
      text-align: center;
      padding: 60px 20px;
      background: linear-gradient(to bottom, #fffdf8, #fff9f3);
    }
    .menu-section h2 {
      font-size: 2.5em;
      color: #3a2a1a;
      margin-bottom: 40px;
      position: relative;
    }
    .menu-section h2::after {
      content: '';
      display: block;
      width: 80px;
      height: 3px;
      background: #d4b47f;
      margin: 15px auto;
      border-radius: 5px;
    }
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      padding: 0 40px;
    }
    .menu-item {
      background: #fffaf4;
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      padding: 20px;
    }
    .menu-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .menu-item img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
      transition: transform 0.3s ease;
    }
    .menu-item:hover img {
      transform: scale(1.05);
    }
    .menu-item h3 {
      color: #3a2a1a;
      margin-top: 15px;
      font-size: 1.3em;
    }
    .menu-item p {
      color: #6b5440;
      font-size: 0.95em;
      margin: 10px 0;
    }
    .price {
      display: block;
      font-size: 1.2em;
      color: #c5a46d;
      font-weight: 600;
      margin: 10px 0;
    }
    .add-to-cart-btn {
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #c5a46d;
      border: none;
      color: white;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: background 0.3s ease, transform 0.2s ease;
      width: 100%;
    }
    .add-to-cart-btn:hover {
      background-color: #b08e58;
      transform: scale(1.05);
    }
    
    /* Cart Sidebar */
    .cart-sidebar {
      position: fixed;
      right: -400px;
      top: 0;
      width: 400px;
      height: 100%;
      background: white;
      box-shadow: -2px 0 10px rgba(0,0,0,0.2);
      transition: right 0.3s ease;
      z-index: 1000;
      display: flex;
      flex-direction: column;
    }
    .cart-sidebar.open {
      right: 0;
    }
    .cart-header {
      background: #3a2a1a;
      color: white;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .cart-header h3 {
      margin: 0;
    }
    .close-cart {
      background: none;
      border: none;
      color: white;
      font-size: 1.5em;
      cursor: pointer;
    }
    .cart-items {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
    }
    .cart-item {
      display: flex;
      gap: 15px;
      padding: 15px;
      border-bottom: 1px solid #eee;
      align-items: center;
    }
    .cart-item img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }
    .cart-item-details {
      flex: 1;
    }
    .cart-item-details h4 {
      margin: 0 0 5px 0;
      color: #3a2a1a;
      font-size: 0.95em;
    }
    .cart-item-details p {
      margin: 0;
      color: #6b5440;
      font-size: 0.9em;
    }
    .remove-item {
      background: #dc3545;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 0.85em;
    }
    .cart-footer {
      padding: 20px;
      border-top: 2px solid #d4b47f;
      background: #fffaf4;
    }
    .cart-total {
      display: flex;
      justify-content: space-between;
      font-size: 1.3em;
      font-weight: 700;
      color: #3a2a1a;
      margin-bottom: 15px;
    }
    .checkout-btn {
      width: 100%;
      padding: 15px;
      background: #c5a46d;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.1em;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }
    .checkout-btn:hover {
      background: #b08e58;
    }
    .empty-cart {
      text-align: center;
      padding: 40px 20px;
      color: #6b5440;
    }
    .cart-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }
    .cart-overlay.open {
      display: block;
    }
    footer {
      background: #fffaf4;
      padding: 20px;
      text-align: center;
      border-top: 2px solid #d4b47f;
      margin-top: 40px;
      font-size: 0.9em;
    }
  </style>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo-container">
      <img src="photo_2022-11-27_15-40-41.jpg" alt="LA CREAMY CAKEIST Logo" class="logo">
      <h1>LA CREAMY CAKEIST</h1>
    </div>
    <nav>
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="blog.html">Blog</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li>
          <div class="cart-icon" onclick="toggleCart()">
            🛒
            <?php if ($cart_count > 0): ?>
              <span class="cart-badge"><?php echo $cart_count; ?></span>
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </nav>
  </header>

  <section class="menu-section">
    <h2>Our Cakes Menu</h2>
    <div class="menu-grid">
      <?php
      $menu_items = [
        ['name' => 'Cream Cakes', 'price' => 120, 'image' => 'cream cakes.jpg', 'desc' => 'Soft, creamy, sweet and delightful.'],
        ['name' => 'Breakfast basket', 'price' => 100, 'image' => 'breakfast basket.jpg', 'desc' => 'Morning treats all-in-one.'],
        ['name' => 'Tiered Cakes', 'price' => 140, 'image' => 'tired cakes.jpg', 'desc' => 'Multiple layer stacked cake.'],
        ['name' => 'Yummy bento Cakes', 'price' => 130, 'image' => 'yummy bento.jpg', 'desc' => 'Small, cute and delicious cake.'],
        ['name' => 'Bride to be Cakes', 'price' => 150, 'image' => 'bride to be cake.jpg', 'desc' => 'Celebratory, sweet and elegant.'],
        ['name' => 'Money Bouquet', 'price' => 110, 'image' => 'money bouque.jpg', 'desc' => 'Cash arranged beautifully.'],
        ['name' => 'Graduation and congratulations', 'price' => 125, 'image' => 'graduation.jpg', 'desc' => 'Achievement, celebration and sweetness.'],
        ['name' => 'Birthday Cakes', 'price' => 135, 'image' => 'birthday cakes.jpg', 'desc' => 'Festive, joyful and sweet.'],
        ['name' => 'Brownies, cake pops and sickles', 'price' => 115, 'image' => 'brownies.jpg', 'desc' => 'Fudgy, chocolatey and rich.'],
        ['name' => 'Oreo icecream cake', 'price' => 145, 'image' => 'orieo.jpg', 'desc' => 'Crunchy, creamy and indulgent.'],
        ['name' => 'Number cakes and cupcakes', 'price' => 125, 'image' => 'numbers.jpg', 'desc' => 'Creative treats.'],
        ['name' => 'Valentines cakes and packages', 'price' => 120, 'image' => 'valentines.jpg', 'desc' => 'Romantic, sweet and heartfelt.']
      ];

      foreach ($menu_items as $item):
      ?>
      <div class="menu-item">
        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
        <h3><?php echo $item['name']; ?></h3>
        <p><?php echo $item['desc']; ?></p>
        <span class="price">₵<?php echo $item['price']; ?></span>
        <form method="POST" style="margin: 0;">
          <input type="hidden" name="item_name" value="<?php echo $item['name']; ?>">
          <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
          <input type="hidden" name="item_image" value="<?php echo $item['image']; ?>">
          <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
        </form>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Cart Overlay -->
  <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>

  <!-- Cart Sidebar -->
  <div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
      <h3>Your Cart (<?php echo $cart_count; ?>)</h3>
      <button class="close-cart" onclick="toggleCart()">✕</button>
    </div>
    
    <div class="cart-items">
      <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
          <p>🛒</p>
          <p>Your cart is empty</p>
        </div>
      <?php else: ?>
        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
          <div class="cart-item">
            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
            <div class="cart-item-details">
              <h4><?php echo $item['name']; ?></h4>
              <p>₵<?php echo $item['price']; ?> × <?php echo $item['quantity']; ?></p>
              <p><strong>₵<?php echo $item['price'] * $item['quantity']; ?></strong></p>
            </div>
            <a href="?remove=<?php echo $index; ?>" class="remove-item">Remove</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    
    <?php if (!empty($_SESSION['cart'])): ?>
    <div class="cart-footer">
      <div class="cart-total">
        <span>Total:</span>
        <span>₵<?php echo number_format($cart_total, 2); ?></span>
      </div>
      <a href="checkout.php" style="text-decoration: none;">
        <button class="checkout-btn">Proceed to Checkout</button>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <footer>
    <p>© 2025 LA CREAMY CAKEIST | All Rights Reserved</p>
  </footer>

  <script>
    function toggleCart() {
      const sidebar = document.getElementById('cartSidebar');
      const overlay = document.getElementById('cartOverlay');
      sidebar.classList.toggle('open');
      overlay.classList.toggle('open');
    }
  </script>
</body>
</html>
