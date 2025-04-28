<?php
$conn = new mysqli("localhost", "root", "", "bupops_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$gcash_payment_id = 5; // Change to the ID you are checking

$checkQuery = "SELECT id FROM gcash_payments WHERE id = $gcash_payment_id";
$result = $conn->query($checkQuery);

if ($result->num_rows > 0) {
    echo "✅ The gcash_payment_id ($gcash_payment_id) exists!";
} else {
    echo "❌ Error: The gcash_payment_id ($gcash_payment_id) does NOT exist!";
}

$conn->close();
?>
