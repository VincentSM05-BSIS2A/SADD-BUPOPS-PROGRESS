<?php
$host = "localhost";  // Change if using a remote server
$username = "root";   // Default XAMPP username
$password = "";       // Default XAMPP password (leave blank)
$database = "bupops_db";  // Name of your database
$port = 3306;          // Changed MySQL port in XAMPP

// Connect to MySQL using the updated port
$conn = new mysqli($host, $username, $password, $database, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
