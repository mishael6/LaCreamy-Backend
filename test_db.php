<?php
// Try 127.0.0.1 instead of localhost
$servername = "127.0.0.1";
$username = "root";
$password = "akosua666";
$dbname = "lacreamy";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection error: " . $e->getMessage());
}
?>