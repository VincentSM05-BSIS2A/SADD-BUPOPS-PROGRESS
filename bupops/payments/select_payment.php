<?php
session_start();
include('../database/connection.php');

if (!isset($_SESSION['user_id']) || !isset($_GET['due_id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$due_id = $_GET['due_id'];

// Fix: Include sub_category_id in the query
$due_query = "SELECT id, category_name, amount, sub_category_id FROM payment_categories WHERE id = '$due_id'";
$due_result = mysqli_query($conn, $due_query);
$due = mysqli_fetch_assoc($due_result);

if (!$due) {
    echo "Invalid payment due.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];
    $account_number = $_POST['account_number'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Fix: Handle NULL sub_category_id properly
    $sub_category_id = isset($due['sub_category_id']) && !is_null($due['sub_category_id']) ? $due['sub_category_id'] : 'NULL';

    // Fix: Add sub_category_id to the INSERT query
    $insert_query = "INSERT INTO payments (user_id, amount, status, method, account_number, name, email, sub_category_id)
                     VALUES ('$user_id', '{$due['amount']}', 'pending', '$payment_method', '$account_number', '$name', '$email', $sub_category_id)";

    if (mysqli_query($conn, $insert_query)) {
        header("Location: verify_payment.php?payment_id=" . mysqli_insert_id($conn));
        exit();
    } else {
        echo "Error processing payment: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Payment Method</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Select Payment Method</h3>
        <form method="POST">
            <p><strong>Payment For:</strong> <?php echo $due['category_name']; ?></p>
            <p><strong>Amount:</strong> ₱<?php echo number_format($due['amount'], 2); ?></p>

            <label>Payment Method:</label>
            <select name="payment_method" class="form-control" required>
                <option value="gcash">GCash</option>
                <option value="paymaya">PayMaya</option>
            </select>

            <label>Account Number:</label>
            <input type="text" name="account_number" class="form-control" required>

            <label>Name:</label>
            <input type="text" name="name" class="form-control" required>

            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>

            <button type="submit" class="btn btn-success mt-3">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>
