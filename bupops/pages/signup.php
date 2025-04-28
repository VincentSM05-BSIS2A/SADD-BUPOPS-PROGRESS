<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - BUPOPS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
    background: linear-gradient(to left, #af8661, #6274ad);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
}

.signup-container {
    display: flex;
    width: 800px; /* Adjust width as needed */
    height: 500px; /* Adjust height as needed */
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 20);
}

.form-section {
    padding: 30px;
    width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.image-section {
    width: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.image-section img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.form-control {
    border-radius: 20px;
}

.btn-primary {
    border-radius: 60px;
    width: 100%;
}

.social-login {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.social-login button {
    flex: 1;
    border: none;
    padding: 8px;
    border-radius: 20px;
    cursor: pointer;
}

.facebook {
    background: #3b5998;
    color: white;
}

.google {
    background: #dd4b39;
    color: white;
}

    </style>
</head>
<body>
    <div class="signup-container">
        <div class="form-section">
            <h3 class="text-center">CREATE AN ACCOUNT</h3>
            <form action="../auth/process_signup.php" method="POST" onsubmit="return validateForm()">
                <input type="text" name="name" class="form-control mb-2" placeholder="Enter your name" required>
                <input type="text" name="surname" class="form-control mb-2" placeholder="Enter your surname" required>
                <input type="email" name="bu_email" class="form-control mb-2" placeholder="Enter your email" required>
                <select name="role" class="form-control mb-2" required>
                 <option value="user" selected>User</option>
                 <option value="admin">Admin</option>
                </select>
                <input type="password" id="password" name="password" class="form-control mb-2" placeholder="Enter your password" required>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control mb-2" placeholder="Confirm your password" required>
                <div id="passwordError" class="text-danger" style="display: none;">Passwords do not match!</div>
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" required>
                    <label class="form-check-label">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a></label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Sign Up</button>
            </form>
            <div class="social-login">
                <button class="facebook">Log in with Facebook</button>
                <button class="google">Log in with Google</button>
            </div>
        </div>
        <div class="image-section">
    <img src="../assets/bg_signup.jpg" alt="Signup Image" style="width: 100%; height: 100%;">
</div>

        </div>
    </div>
    <script>
        function validateForm() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let errorDiv = document.getElementById("passwordError");
            if (password !== confirmPassword) {
                errorDiv.style.display = "block";
                return false;
            } else {
                errorDiv.style.display = "none";
                return true;
            }
        }
    </script>
</body>
</html>
