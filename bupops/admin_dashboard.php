<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}
$email = $_SESSION['email'];
$conn = new mysqli("localhost", "root", "", "bupops_db");

// PAGINATION LOGIC
$limit = 10; // 10 payments per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page from URL, default is 1
$offset = ($page - 1) * $limit;

// Fetch only payments for the current page
$result = $conn->query("SELECT * FROM payments ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

// Get total number of records for pagination
$total_result = $conn->query("SELECT COUNT(*) as total FROM payments");
$total_row = $total_result->fetch_assoc();
$total_payments = $total_row['total'];
$total_pages = ceil($total_payments / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BUPOPS - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {--p:#4361ee;--s:#3f37c9;--sc:#4cc9f0;--d:#f72585;--w:#f8961e;--l:#f8f9fa;--dk:#212529;--g:#6c757d;}
        * {margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
        body {background:#f5f7fb;color:var(--dk)}
        .dashboard {display:flex;min-height:100vh}
        .sidebar {width:250px;background:#fff;box-shadow:0 0 20px rgba(0,0,0,0.05);padding:20px}
        .sidebar-header h2 {color:var(--p);display:flex;align-items:center}
        .sidebar-header i {margin-right:10px;color:var(--sc)}
        .sidebar-menu {list-style:none}
        .sidebar-menu a {display:flex;align-items:center;padding:12px 20px;color:var(--g);text-decoration:none;transition:.3s;border-left:3px solid transparent}
        .sidebar-menu a:hover,.sidebar-menu a.active {background:rgba(67,97,238,0.1);color:var(--p);border-left:3px solid var(--p)}
        .main-content {flex:1;padding:20px}
        .header {display:flex;justify-content:space-between;align-items:center;background:#fff;padding:15px 25px;border-radius:10px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05)}
        .user-info {display:flex;align-items:center}
        .user-info img {width:40px;height:40px;border-radius:50%;margin-right:10px;object-fit:cover}
        .cards {display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:30px}
        .card {background:#fff;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05);transition:.3s}
        .card:hover {transform:translateY(-5px)}
        .recent-transactions {background:#fff;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05)}
        table {width:100%;border-collapse:collapse}
        th,td {padding:12px;text-align:left;border-bottom:1px solid #e9ecef}
        select {padding:5px;border-radius:5px}
        @media(max-width:768px){.dashboard{flex-direction:column}.sidebar{width:100%;height:auto}.cards{grid-template-columns:1fr}}
        .pagination {
    margin-top: 20px;
    text-align: center;
}
.pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 2px;
    background: var(--l);
    color: var(--dk);
    border: 1px solid #ddd;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s;
}
.pagination a:hover {
    background: var(--sc);
    color: #fff;
}
.pagination a.active {
    background: var(--p);
    color: #fff;
    font-weight: bold;
}

    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header"><h2><i class="fas fa-cubes"></i>BUPOPS</h2></div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-credit-card"></i> Payments</a></li>
            <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Fees</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="/bupops/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            <button onclick="refreshPayments()" style="background: var(--p); color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-right: 20px;">🔄 Refresh Payments</button>
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($email) ?>&background=random" alt="User">
                <div><h4><?= $email ?></h4><small>Administrator</small></div>
            </div>
        </div>

        <div class="cards">
            <?php
            $cards = [
                ["Total Payments", "₱24,580", "+12% from last month", "fas fa-wallet", "rgba(67, 97, 238, 0.1)", "var(--p)"],
                ["Pending Fees", "42", "5 new today", "fas fa-clock", "rgba(248,150,30,0.1)", "var(--w)"],
                ["Active Users", "156", "8 new this week", "fas fa-users", "rgba(76,201,240,0.1)", "var(--sc)"],
                ["Recent Notifications", "23", "3 unread", "fas fa-bell", "rgba(247,37,133,0.1)", "var(--d)"]
            ];
            foreach ($cards as [$title, $value, $desc, $icon, $bg, $color]) {
                echo "<div class='card'>
                        <div class='card-header'><h3>$title</h3><i class='$icon' style='background:$bg;color:$color'></i></div>
                        <div class='card-body'><h2>$value</h2><p>$desc</p></div>
                      </div>";
            }
            ?>
        </div>

        <div class="recent-transactions">
            <div class="section-header">
                <h2>Recent Transactions</h2>
                <button class="btn btn-primary">View All</button>
            </div>
            <h2>Payments</h2>
            <a href="controller.php">+ Create New Payment</a>
            <table>
                <thead>
                    <tr><th>ID</th><th>User</th><th>Amount</th><th>Description</th><th>Status</th><th>Checkout</th><th>Date</th></tr>
                </thead>
                <tbody id="payments-table">
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?> (<?= $row['email'] ?>)</td>
            <td>₱<?= number_format($row['amount'], 2) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
                <select onchange="updateStatus(<?= $row['id'] ?>, this.value)">
                    <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
                    <option value="completed" <?= $row['status']=='completed'?'selected':'' ?>>Completed</option>
                    <option value="failed" <?= $row['status']=='failed'?'selected':'' ?>>Failed</option>
                </select>
            </td>
            <td>
                <?= $row['checkout_url'] ? "<a href='{$row['checkout_url']}' target='_blank'>Open</a>" : "N/A" ?>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <button onclick="deletePayment(<?= $row['id'] ?>)" style="background: var(--d); color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">Delete</button>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

            </table>
            <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

        </div>
    </div>
</div>

<script>
function fetchPayments() {
    fetch('fetch_payments.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#payments-table");
            tbody.innerHTML = data.map(row => `
                <tr>
                    <td>${row.id}</td>
                    <td>${row.name} (${row.email})</td>
                    <td>₱${parseFloat(row.amount).toFixed(2)}</td>
                    <td>${row.description}</td>
                    <td>
                        <select onchange="updateStatus(${row.id}, this.value)">
                            <option value="pending" ${row.status=='pending'?'selected':''}>Pending</option>
                            <option value="completed" ${row.status=='completed'?'selected':''}>Completed</option>
                            <option value="failed" ${row.status=='failed'?'selected':''}>Failed</option>
                        </select>
                    </td>
                    <td>${row.checkout_url ? `<a href="${row.checkout_url}" target="_blank">Open</a>` : "N/A"}</td>
                    <td>${row.created_at}</td>
                    <td>
                        <button onclick="deletePayment(${row.id})" style="background: var(--d); color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">Delete</button>
                    </td>
                </tr>
            `).join('');
        });
}

function updateStatus(id, status) {
    fetch('update_status.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`id=${id}&status=${status}`
    }).then(res => res.text()).then(alert).then(fetchPayments);
}

function deletePayment(id) {
    if (confirm("Delete this payment?")) {
        fetch('delete_payment.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}`
        })
        .then(res => res.text())
        .then(response => {
            alert(response); // Alert success or error message
            fetchPayments(); // Refresh payments table
        });
    }
}

setInterval(fetchPayments, 3000);
window.onload = fetchPayments;
function refreshPayments() {
    fetch('refresh_payments.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Payments refreshed successfully!');
            fetchPayments(); // Reload the updated table
        } else {
            alert('Failed to refresh payments.');
        }
    })
    .catch(err => {
        alert('Error refreshing payments.');
        console.error(err);
    });
}
setInterval(function() {
    fetchPayments();  // Function to refresh payments
}, 5000);  // Refresh every 5 seconds


</script>
</body>
</html>
