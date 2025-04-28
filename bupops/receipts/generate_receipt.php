<?php
session_start();
include "../config/db.php";

if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];

    $query = "INSERT INTO receipts (payment_id, receipt_number) VALUES (?, ?)";
    $receipt_number = "REC-" . rand(10000, 99999);
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $payment_id, $receipt_number);
    $stmt->execute();

    echo "Receipt Generated: " . $receipt_number;
}
?>
