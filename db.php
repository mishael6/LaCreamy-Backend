<?php
$servername = "localhost";
$username = "root";
$password = "akosua666";  // Leave empty or put your password
$dbname = "Lacreamy";

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    echo "Connected successfully!";
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>