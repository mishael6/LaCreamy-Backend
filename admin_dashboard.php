<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// Get statistics
$total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_submissions")->fetch_assoc()['count'];
$new_messages = $conn->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'];

// Get recent messages
$messages_query = "SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 10";
$messages_result = $conn->query($messages_query);

// Get recent orders
$orders_query = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 10";
$orders_result = $conn->query($orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LA CREAMY CAKEIST</title>
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
            margin-left: 20px;
            padding: 8px 16px;
            background: #c5a46d;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .navbar a:hover {
            background: #b08e58;
        }
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            color: #6b5440;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .stat-card .number {
            color: #3a2a1a;
            font-size: 2.5em;
            font-weight: 700;
        }
        .stat-card.highlight {
            background: linear-gradient(135deg, #c5a46d 0%, #b08e58 100%);
            color: white;
        }
        .stat-card.highlight h3,
        .stat-card.highlight .number {
            color: white;
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .section h2 {
            color: #3a2a1a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4b47f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f9f9f9;
            color: #3a2a1a;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #d4b47f;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #fffdf8;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .badge-new {
            background: #fff3cd;
            color: #856404;
        }
        .badge-read {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-responded {
            background: #d4edda;
            color: #155724;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-confirmed {
            background: #cfe2ff;
            color: #084298;
        }
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #c5a46d;
            color: white;
        }
        .btn-primary:hover {
            background: #b08e58;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            background: none;
            color: #6b5440;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab.active {
            color: #c5a46d;
            border-bottom-color: #c5a46d;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🎂 LA CREAMY CAKEIST - Admin Dashboard</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="admin_logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card highlight">
                <h3>New Messages</h3>
                <div class="number"><?php echo $new_messages; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Messages</h3>
                <div class="number"><?php echo $total_messages; ?></div>
            </div>
            <div class="stat-card highlight">
                <h3>Pending Orders</h3>
                <div class="number"><?php echo $pending_orders; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="number"><?php echo $total_orders; ?></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="showTab('messages')">Messages</button>
            <button class="tab" onclick="showTab('orders')">Orders</button>
        </div>

        <!-- Messages Section -->
        <div id="messages" class="tab-content active">
            <div class="section">
                <h2>Recent Contact Messages</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($messages_result->num_rows > 0): ?>
                            <?php while ($message = $messages_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $message['id']; ?></td>
                                    <td><?php echo htmlspecialchars($message['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo htmlspecialchars($message['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($message['message'], 0, 50)) . '...'; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $message['status']; ?>">
                                            <?php echo ucfirst($message['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                                    <td>
                                        <a href="view_message.php?id=<?php echo $message['id']; ?>" class="btn btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: #6b5440;">No messages yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders Section -->
        <div id="orders" class="tab-content">
            <div class="section">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Order Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders_result->num_rows > 0): ?>
                            <?php while ($order = $orders_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_type'] ?? 'N/A'); ?></td>
                                    <td>GH₵ <?php echo number_format($order['total_amount'] ?? 0, 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; color: #6b5440;">No orders yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>