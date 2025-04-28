<?php
session_start();
include('../database/connection.php');

if (!isset($_SESSION['user_id']) || !isset($_GET['payment_id'])) {
    header("Location: dashboard.php");
    exit();
}

$payment_id = $_GET['payment_id'];

// Debugging: Check if payment_id is received
echo "Received payment_id: " . $payment_id . "<br>";

$query = "SELECT * FROM payments WHERE id = '$payment_id' AND status = 'pending'";
$result = mysqli_query($conn, $query);
$payment = mysqli_fetch_assoc($result);

if (!$payment) {
    echo "Invalid payment. Debugging: No record found for payment_id: " . $payment_id . "<br>";

    // Debugging: Check existing payments
    $debug_query = "SELECT id, status FROM payments";
    $debug_result = mysqli_query($conn, $debug_query);

    echo "Existing Payments:<br>";
    while ($row = mysqli_fetch_assoc($debug_result)) {
        echo "ID: " . $row['id'] . " | Status: " . $row['status'] . "<br>";
    }
    exit();
}

// Simulating payment verification
$funds_sufficient = rand(0, 1);

// Debugging output
echo "Fund status: " . $funds_sufficient . "<br>";

if ($funds_sufficient) {
    // Update payment status
    $update_query = "UPDATE payments SET status = 'completed' WHERE id = '$payment_id'";
    if (!mysqli_query($conn, $update_query)) {
        echo "Payment update failed: " . mysqli_error($conn);
        exit();
    }

    // Insert receipt (Fix for user_id issue)
    if (isset($payment['user_id'])) {
        // If user_id exists in payments, include it in the receipts table
        $receipt_query = "INSERT INTO receipts (payment_id, user_id, amount) VALUES ('$payment_id', '{$payment['user_id']}', '{$payment['amount']}')";
    } else {
        // If user_id does NOT exist, remove it from the insert statement
        $receipt_query = "INSERT INTO receipts (payment_id, amount) VALUES ('$payment_id', '{$payment['amount']}')";
    }

    if (!mysqli_query($conn, $receipt_query)) {
        echo "Receipt insertion failed: " . mysqli_error($conn);
        exit();
    }

    // Send email (optional)
    mail($payment['email'], "Payment Confirmation", "Your payment of ₱{$payment['amount']} has been received.");

    echo "Payment successful! A receipt has been sent to your email.<br>";

    // Redirect after 3 seconds
    header("refresh:3; url=dashboard.php");
} else {
    echo "Payment failed due to insufficient funds.<br>";
    
    // Redirect after 3 seconds
    header("refresh:3; url=dashboard.php");
}

exit();
?>