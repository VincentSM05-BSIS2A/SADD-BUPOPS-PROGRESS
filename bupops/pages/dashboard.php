<?php
session_start();
include('../database/connection.php'); // Connect to database

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student balance (sum of pending payments)
$balance_query = "SELECT SUM(amount) AS balance FROM payments WHERE user_id = '$user_id' AND status = 'pending'";
$balance_result = mysqli_query($conn, $balance_query);
$balance = mysqli_fetch_assoc($balance_result)['balance'] ?? 0;

// Fetch recent transactions with receipts
$transactions_query = "SELECT p.id, p.amount, p.status, p.created_at, r.id AS receipt_id 
                       FROM payments p
                       LEFT JOIN receipts r ON p.id = r.payment_id
                       WHERE p.user_id = '$user_id'
                       ORDER BY p.created_at DESC
                       LIMIT 5";
$transactions_result = mysqli_query($conn, $transactions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - BUPOPS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .logo {
            height: 50px;
            margin-right: 10px;
        }
        .dashboard-text {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../assets/bupc_dashboard-logo-removebg-preview.png" class="logo">
            <span class="dashboard-text">BUPOPS Dashboard</span>
        </a>

        <div class="d-flex align-items-center">
            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown">
                    <li><a class="dropdown-item" href="#">📌 Payment of ₱500 processed</a></li>
                    <li><a class="dropdown-item" href="#">📌 Your receipt is available</a></li>
                    <li><a class="dropdown-item" href="#">📌 Payment due in 3 days</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-primary" href="../notifications.php">View All Notifications</a></li>
                </ul>
            </div>

            <!-- Messages -->
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative" type="button" id="mailDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-envelope"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">2</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mailDropdown">
                    <li><a class="dropdown-item" href="#">📩 Payment confirmation email sent</a></li>
                    <li><a class="dropdown-item" href="#">📩 Reminder: Pending payment due soon</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-primary" href="../messages.php">View All Messages</a></li>
                </ul>
            </div>

            <!-- Profile -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> Profile
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="../profile/user_account.php"><i class="fas fa-user"></i> My Account</a></li>
                    <li><a class="dropdown-item" href="../profile/qr_code.php"><i class="fas fa-qrcode"></i> My QR Code</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h3>Welcome, Student</h3>

    <!-- Outstanding Balance -->
    <div class="card bg-warning text-white mb-4">
        <div class="card-body">
            <h5>Outstanding Balance</h5>
            <h2>₱<?php echo number_format($balance, 2); ?></h2>
        </div>
    </div>

    <!-- Recent Transactions -->
    <h4>Recent Transactions</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($transactions_result)): ?>
                <tr>
                    <td><?php echo ($row['receipt_id'] ? "R-" . $row['receipt_id'] : "P-" . $row['id']); ?></td>
                    <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="../payments/transaction_history.php" class="btn btn-secondary mb-4">View All Transactions</a>

    <!-- Required Payment Dues -->
    <!-- Required Payment Dues -->
<h4>Required Payment Dues</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch ALL payment dues
        $dues_query = "SELECT id, category_name, amount FROM payment_categories";
        $dues_result = mysqli_query($conn, $dues_query);
        while ($due = mysqli_fetch_assoc($dues_result)):
        ?>
            <tr>
                <td><?php echo $due['id']; ?></td>
                <td><?php echo $due['category_name']; ?></td>
                <td>₱<?php echo number_format($due['amount'], 2); ?></td>
                <td>
                    <form action="../controller.php" method="GET">
                        <input type="hidden" name="due_id" value="<?php echo $due['id']; ?>">
                        <button type="submit" class="btn btn-primary">Pay Now</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
setInterval(function() {
    // Fetch the updated transactions
    fetch('fetch_user_transactions.php')
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector('tbody');
            tbody.innerHTML = '';
            data.forEach(transaction => {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td>${transaction.receipt_id ? "R-" + transaction.receipt_id : "P-" + transaction.id}</td>
                    <td>₱${transaction.amount}</td>
                    <td>${transaction.status}</td>
                    <td>${transaction.created_at}</td>
                `;
                tbody.appendChild(row);
            });
        });
}, 5000);  // Refresh every 5 seconds



</script>
</body>
</html>
