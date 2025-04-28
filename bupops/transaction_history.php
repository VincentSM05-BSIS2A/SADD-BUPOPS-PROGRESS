<?php
session_start();
include('../database/connection.php'); // Connect to database

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}    
echo "Hello, World!";
?>
