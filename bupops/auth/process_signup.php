<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include(__DIR__ . '/../database/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? null;
    $surname = $_POST['surname'] ?? null;
    $bu_email = $_POST['bu_email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Check if any required field is missing
    if (!$name || !$surname || !$bu_email || !$password || !$confirm_password) {
        header('Location: ../pages/signup.php?error=missing_fields');
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        header('Location: ../pages/signup.php?error=password_mismatch');
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE bu_email = ?");
    $stmt->bind_param("s", $bu_email);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Redirect back to signup with an error
        header('Location: ../pages/signup.php?error=email_exists');
        exit();
    }

    // Insert new user into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'user'; // Default role is 'user'
$stmt = $conn->prepare("INSERT INTO users (name, surname, bu_email, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $surname, $bu_email, $hashed_password, $role);

    if ($stmt->execute()) {
        // Redirect to login page after successful signup
        header('Location: ../index.php?signup=success');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
