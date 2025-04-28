<?php
session_start();
include(__DIR__ . '/../database/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE bu_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Successful login, store session
        $_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['bu_email'];
$_SESSION['role'] = $user['role'];

        
        // Redirect to the dashboard or homepage
        if ($user['role'] === 'admin') {
            header("Location:  ../admin_dashboard.php");
        } else {
            header("Location: ../pages/dashboard.php");
        }
        
        exit();
        
        exit();
    } else {
        // Redirect back to login page with error
        header("Location: ../index.php?error=invalid_credentials");
        exit();
    }
}
echo "Login process file is working!";


?>
