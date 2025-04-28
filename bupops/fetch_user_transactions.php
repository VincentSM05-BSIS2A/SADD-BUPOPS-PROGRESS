<?php
session_start();
include('../database/connection.php');

$user_id = $_SESSION['user_id'];
$transactions_query = "SELECT p.id, p.amount, p.status, p.created_at, r.id AS receipt_id
                       FROM payments p
                       LEFT JOIN receipts r ON p.id = r.payment_id
                       WHERE p.user_id = '$user_id'
                       ORDER BY p.created_at DESC LIMIT 5";

$result = mysqli_query($conn, $transactions_query);
$transactions = [];

while ($row = mysqli_fetch_assoc($result)) {
    $transactions[] = $row;
}

echo json_encode($transactions);
?>
