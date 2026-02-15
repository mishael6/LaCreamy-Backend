<?php
session_start();

if (empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';
    
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $delivery_address = trim($_POST['delivery_address']);
    $notes = trim($_POST['notes']);
    
    // Calculate total
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Prepare order details
    $order_details = [];
    foreach ($_SESSION['cart'] as $item) {
        $order_details[] = [
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'subtotal' => $item['price'] * $item['quantity']
        ];
    }
    $order_details_json = json_encode($order_details);
    
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, order_details, order_type, total_amount, delivery_address, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $order_type = "Cake Order";
    $stmt->bind_param("sssssdss", $customer_name, $customer_email, $customer_phone, $order_details_json, $order_type, $total_amount, $delivery_address, $notes);
    
    if ($stmt->execute()) {
        $success = true;
        $_SESSION['cart'] = []; // Clear cart
    } else {
        $error = "Failed to place order. Please try again.";
    }
    
    $stmt->close();
    $conn->close();
}

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - LA CREAMY CAKEIST</title>
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
      border-bottom: 2px solid #d4b47f;
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }
    .section {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #3a2a1a;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #d4b47f;
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      color: #3a2a1a;
      font-weight: 500;
      margin-bottom: 8px;
    }
    input, textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #d4b47f;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-size: 1em;
    }
    textarea {
      resize: vertical;
      min-height: 100px;
    }
    .order-item {
      display: flex;
      gap: 15px;
      padding: 15px 0;
      border-bottom: 1px solid #eee;
    }
    .order-item img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }
    .order-item-details {
      flex: 1;
    }
    .order-item-details h4 {
      margin: 0 0 5px 0;
      color: #3a2a1a;
    }
    .order-item-details p {
      margin: 0;
      color: #6b5440;
      font-size: 0.9em;
    }
    .order-total {
      display: flex;
      justify-content: space-between;
      font-size: 1.3em;
      font-weight: 700;
      color: #3a2a1a;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 2px solid #d4b47f;
    }
    .btn {
      padding: 15px 30px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 1em;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    .btn-primary {
      background: #c5a46d;
      color: white;
      width: 100%;
    }
    .btn-primary:hover {
      background: #b08e58;
    }
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    .success-message {
      background: #d4edda;
      color: #155724;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 20px;
    }
    .success-message h3 {
      margin: 0 0 10px 0;
    }
    .error-message {
      background: #f8d7da;
      color: #721c24;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    @media (max-width: 768px) {
      .container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>🎂 LA CREAMY CAKEIST - Checkout</h1>
  </header>

  <div class="container">
    <div class="section">
      <h2>Order Summary</h2>
      <?php foreach ($_SESSION['cart'] as $item): ?>
        <div class="order-item">
          <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
          <div class="order-item-details">
            <h4><?php echo $item['name']; ?></h4>
            <p>₵<?php echo $item['price']; ?> × <?php echo $item['quantity']; ?></p>
            <p><strong>₵<?php echo $item['price'] * $item['quantity']; ?></strong></p>
          </div>
        </div>
      <?php endforeach; ?>
      
      <div class="order-total">
        <span>Total:</span>
        <span>₵<?php echo number_format($cart_total, 2); ?></span>
      </div>
    </div>

    <div class="section">
      <?php if ($success): ?>
        <div class="success-message">
          <h3>✓ Order Placed Successfully!</h3>
          <p>Thank you for your order. We'll contact you shortly to confirm.</p>
          <p>Order Total: ₵<?php echo number_format($cart_total, 2); ?></p>
        </div>
        <a href="menu.php" class="btn btn-primary">Continue Shopping</a>
      <?php else: ?>
        <h2>Customer Details</h2>
        
        <?php if ($error): ?>
          <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
          <div class="form-group">
            <label for="customer_name">Full Name *</label>
            <input type="text" id="customer_name" name="customer_name" required>
          </div>
          
          <div class="form-group">
            <label for="customer_email">Email *</label>
            <input type="email" id="customer_email" name="customer_email" required>
          </div>
          
          <div class="form-group">
            <label for="customer_phone">Phone Number *</label>
            <input type="tel" id="customer_phone" name="customer_phone" required>
          </div>
          
          <div class="form-group">
            <label for="delivery_address">Delivery Address *</label>
            <textarea id="delivery_address" name="delivery_address" required></textarea>
          </div>
          
          <div class="form-group">
            <label for="notes">Special Instructions (Optional)</label>
            <textarea id="notes" name="notes"></textarea>
          </div>
          
          <button type="submit" class="btn btn-primary">Place Order - ₵<?php echo number_format($cart_total, 2); ?></button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>