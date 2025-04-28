<?php
include '../config/db_connect.php'; // Adjust to your actual database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | BUPOPS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form action="register.php" method="POST">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

<?php
include 'db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $fullname, $email, $password);

    if ($stmt->execute()) {
        echo "Signup successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<form method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
</form>

