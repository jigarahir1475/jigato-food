<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "food_order";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
?>
