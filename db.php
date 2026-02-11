<?php
$servername = "localhost";
$username = "root";
$password = "akosua666";
$dbname = "lacreamy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>