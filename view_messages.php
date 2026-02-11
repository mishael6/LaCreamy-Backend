<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($message_id === 0) {
    header("Location: admin_dashboard.php");
    exit();
}

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    $response = trim($_POST['response']);
    $admin_id = $_SESSION['admin_id'];
    
    $stmt = $conn->prepare("UPDATE contact_submissions SET status = 'responded', admin_response = ?, responded_at = NOW(), responded_by = ? WHERE id = ?");
    $stmt->bind_param("sii", $response, $admin_id, $message_id);
    
    if ($stmt->execute()) {
        $success_message = "Response sent successfully!";
        
        // Here you could add email functionality to send the response to the customer
        // mail($customer_email, "Response from LA CREAMY CAKEIST", $response);
    }
    $stmt->close();
}

// Mark as read if not already
$conn->query("UPDATE contact_submissions SET status = 'read' WHERE id = $message_id AND status = 'new'");

// Get message details
$stmt = $conn->prepare("SELECT * FROM contact_submissions WHERE id = ?");
$stmt->bind_param("i", $message_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_dashboard.php");
    exit();
}

$message = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message - Admin Dashboard</title>
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
            max-width: 900px;
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
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d4b47f;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1em;
            resize: vertical;
            min-height: 150px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
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
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9em;
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
    </style>
</head>
<body>
    <div class="navbar">
        <h1>View Message</h1>
        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Message Details 
                <span class="badge badge-<?php echo $message['status']; ?>">
                    <?php echo ucfirst($message['status']); ?>
                </span>
            </h2>
            
            <div class="info-group">
                <label>From:</label>
                <p><?php echo htmlspecialchars($message['full_name']); ?></p>
            </div>
            
            <div class="info-group">
                <label>Email:</label>
                <p><a href="mailto:<?php echo htmlspecialchars($message['email']); ?>"><?php echo htmlspecialchars($message['email']); ?></a></p>
            </div>
            
            <div class="info-group">
                <label>Phone:</label>
                <p><a href="tel:<?php echo htmlspecialchars($message['phone_number']); ?>"><?php echo htmlspecialchars($message['phone_number']); ?></a></p>
            </div>
            
            <div class="info-group">
                <label>Message:</label>
                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
            </div>
            
            <div class="info-group">
                <label>Received:</label>
                <p><?php echo date('F j, Y g:i A', strtotime($message['created_at'])); ?></p>
            </div>
        </div>

        <?php if ($message['admin_response']): ?>
            <div class="card">
                <h2>Your Response</h2>
                <div class="info-group">
                    <p><?php echo nl2br(htmlspecialchars($message['admin_response'])); ?></p>
                </div>
                <div class="info-group">
                    <label>Responded at:</label>
                    <p><?php echo date('F j, Y g:i A', strtotime($message['responded_at'])); ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>Send Response</h2>
                <form method="POST">
                    <div class="info-group">
                        <label>Your Response:</label>
                        <textarea name="response" required placeholder="Type your response here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Response</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>