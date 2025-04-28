<?php
session_start();
include('../database/connection.php'); // Connect to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate user credentials from the database
    $query = "SELECT * FROM users WHERE email='$email' AND password=MD5('$password')";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];  // Save user ID in session
        $_SESSION['email'] = $user['bu_email']; // Save email in session
        header('Location: ../pages/home.php'); // Redirect to the homepage
        exit();
    } else {
        // Redirect back to login with an error
        header('Location: ../index.php?error=1');
        exit();
    }
}
?>
<?php
session_start();
include('../database/connection.php'); // Connect to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate user credentials from the database
    $query = "SELECT * FROM users WHERE email='$email' AND password=MD5('$password')";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];  // Save user ID in session
        $_SESSION['email'] = $user['email']; // FIXED: Save correct email in session
        header('Location: ../pages/dashboard.php'); // Redirect to dashboard
        exit();
    } else {
        // Redirect back to login with an error
        header('Location: ../index.php?error=1');
        exit();
    }
}
?>
