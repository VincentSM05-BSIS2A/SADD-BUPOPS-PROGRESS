<?php
session_start();
include "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];

    $query = "INSERT INTO payments (user_id, category_id, amount, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iid", $user_id, $category_id, $amount);

    if ($stmt->execute()) {
        echo "Payment request submitted!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<form method="POST">
    <input type="number" name="category_id" placeholder="Category ID" required>
    <input type="number" name="amount" placeholder="Amount" required>
    <button type="submit">Pay Now</button>
</form>