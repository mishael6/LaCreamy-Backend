<?php
require_once 'db.php';

// Generate a new password hash
$username = "admin";
$password = "admin123";
$email = "admin@lacreamy.com";

// Create password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Creating Admin User</h2>";
echo "Username: $username<br>";
echo "Password: $password<br>";
echo "Hashed: $hashed_password<br><br>";

// Delete existing admin user
$conn->query("DELETE FROM admin_users WHERE username = 'admin'");

// Insert new admin user
$stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed_password, $email);

if ($stmt->execute()) {
    echo "<p style='color: green; font-weight: bold;'>✓ Admin user created successfully!</p>";
    echo "<p>Now try logging in at: <a href='admin_login.php'>admin_login.php</a></p>";
    echo "<p>Username: admin<br>Password: admin123</p>";
} else {
    echo "<p style='color: red;'>✗ Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();

// Also test password verification
echo "<hr><h3>Testing Password Verification</h3>";

// Fetch the user we just created
$conn2 = new mysqli("127.0.0.1", "root", "", "lacreamy", 3306);
$result = $conn2->query("SELECT password FROM admin_users WHERE username = 'admin'");
$user = $result->fetch_assoc();

if ($user) {
    echo "Password from DB: " . $user['password'] . "<br>";
    
    if (password_verify("admin123", $user['password'])) {
        echo "<p style='color: green;'>✓ Password verification works!</p>";
    } else {
        echo "<p style='color: red;'>✗ Password verification failed!</p>";
    }
} else {
    echo "<p style='color: red;'>✗ User not found in database!</p>";
}

$conn2->close();
?>