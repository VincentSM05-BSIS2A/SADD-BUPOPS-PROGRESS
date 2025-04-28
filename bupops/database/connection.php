<?php
// Connect to MySQL on port 3308 (changed from default 3306)
$conn = new mysqli("localhost", "root", "", "bupops_db", 3306);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
