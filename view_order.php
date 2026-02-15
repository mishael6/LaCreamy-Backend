<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id === 0) {
    header("Location: admin_dashboard.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    $success_message = "Order status updated successfully!";
}

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_dashboard.php");
    exit();
}

$order = $result->fetch_assoc();
$order_items = json_decode($order['order_details'], true);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
        }
        .navbar {
            background: #3a2a1a;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            font-size: 1.5em;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: #c5a46d;
            border-radius: 6px;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card h2 {
            color: #3a2a1a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4b47f;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-group {
            margin-bottom: 15px;
        }
        .info-group label {
            display: block;
            color: #6b5440;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-group p {
            color: #3a2a1a;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f9f9f9;
            color: #3a2a1a;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-confirmed {
            background: #cfe2ff;
            color: #084298;
        }
        .badge-in_progress {
            background: #cff4fc;
            color: #055160;
        }
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        .badge-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        select {
            padding: 10px;
            border: 1px solid #d4b47f;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1em;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #c5a46d;
            color: white;
        }
        .btn-primary:hover {
            background: #b08e58;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .total-row {
            font-weight: 700;
            font-size: 1.2em;
            background: #fffaf4 !important;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>View Order #<?php echo $order['id']; ?></h1>
        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Order Information 
                <span class="badge badge-<?php echo $order['status']; ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                </span>
            </h2>
            
            <div class="info-grid">
                <div class="info-group">
                    <label>Order ID:</label>
                    <p>#<?php echo $order['id']; ?></p>
                </div>
                
                <div class="info-group">
                    <label>Order Date:</label>
                    <p><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                </div>
                
                <div class="info-group">
                    <label>Customer Name:</label>
                    <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
                </div>
                
                <div class="info-group">
                    <label>Email:</label>
                    <p><a href="mailto:<?php echo htmlspecialchars($order['customer_email']); ?>"><?php echo htmlspecialchars($order['customer_email']); ?></a></p>
                </div>
                
                <div class="info-group">
                    <label>Phone:</label>
                    <p><a href="tel:<?php echo htmlspecialchars($order['customer_phone']); ?>"><?php echo htmlspecialchars($order['customer_phone']); ?></a></p>
                </div>
                
                <div class="info-group">
                    <label>Total Amount:</label>
                    <p><strong>GH₵ <?php echo number_format($order['total_amount'], 2); ?></strong></p>
                </div>
            </div>
            
            <div class="info-group">
                <label>Delivery Address:</label>
                <p><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
            </div>
            
            <?php if ($order['notes']): ?>
            <div class="info-group">
                <label>Special Instructions:</label>
                <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>GH₵ <?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>GH₵ <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Total:</td>
                        <td>GH₵ <?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Update Order Status</h2>
            <form method="POST">
                <div class="info-group">
                    <label>Status:</label>
                    <select name="status" required>
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="in_progress" <?php echo $order['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>