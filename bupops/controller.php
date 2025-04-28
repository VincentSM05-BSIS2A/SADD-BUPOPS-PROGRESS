
<?php
session_start();
require_once('vendor/autoload.php');
use GuzzleHttp\Client;

// Database credentials
$host = 'localhost';   // or your host
$dbname = 'bupops_db'; // your database name
$username = 'root';     // your database username (default is root)
$password = '';         // your database password (default is empty for XAMPP)

$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form submission
    $amount = isset($_POST['amount']) ? (int) $_POST['amount'] : 0;
    $description = isset($_POST['description']) ? $_POST['description'] : 'No description';
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';

    // Convert amount to cents (if using PayMongo)
    $amountInCents = $amount * 100;

    // Initialize the HTTP client
    $client = new Client();

    try {
        // Send a request to the PayMongo API to create a payment link
        $response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
            'body' => json_encode([
                'data' => [
                    'attributes' => [
                        'amount' => $amountInCents,
                        'description' => $description,
                        'remarks' => $remarks
                    ]
                ]
            ]),
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic c2tfdGVzdF9kUUtTRXhMU2NxejJ0VWVkdHA1cENodng6',
                'content-type' => 'application/json',
            ],
        ]);

        // Decode the response from PayMongo
        $body = json_decode($response->getBody(), true);

        // Get the checkout URL from the response
        $checkoutUrl = $body['data']['attributes']['checkout_url'];

        // Store the payment record in the database (pending status)
        // Assuming $_SESSION['user_id'] is available for the logged-in user
       // Start the session to access user info
        $user_id = $_SESSION['user_id']; // Assuming user is logged in
        $payment_query = "INSERT INTO payments (user_id, amount, status, description, remarks) 
                          VALUES ('$user_id', '$amount', 'pending', '$description', '$remarks')";

        if (mysqli_query($conn, $payment_query)) {
            $payment_id = mysqli_insert_id($conn); // Get the ID of the inserted payment record
        } else {
            echo "Error storing payment record: " . mysqli_error($conn);
            exit();
        }

        // Redirect to the PayMongo checkout URL
        header("Location: " . $checkoutUrl);
        exit();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    // Handle payment status update (admin)
    $paymentId = (int) $_POST['id']; 
    $status = $_POST['status']; // 'pending', 'completed', 'failed'

    if ($paymentId > 0 && in_array($status, ['pending', 'completed', 'failed'])) {
        // Update payment status
        $update_query = "UPDATE payments SET status = '$status' WHERE id = '$paymentId'";

        if (mysqli_query($conn, $update_query)) {
            // Insert into transaction history
            $history_query = "INSERT INTO transaction_history (payment_id, status, changed_at) 
                              VALUES ('$paymentId', '$status', NOW())";

            if (mysqli_query($conn, $history_query)) {
                echo json_encode(['success' => true, 'message' => 'Payment status updated and history logged.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to log transaction history.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update payment status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid payment ID or status.']);
    }
} else {
    // Form display for creating payment link
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Create Payment Link</title>
        <style>
            body {
                background: #f5f7fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: 'Arial', sans-serif;
                margin: 0;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                width: 320px;
                text-align: center;
            }
            h2 {
                margin-bottom: 20px;
                color: #333;
            }
            label {
                display: block;
                margin: 10px 0 5px;
                text-align: left;
                color: #555;
                font-size: 14px;
            }
            input[type="number"],
            input[type="text"] {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 14px;
                transition: border 0.3s;
            }
            input[type="number"]:focus,
            input[type="text"]:focus {
                border-color: #007BFF;
                outline: none;
            }
            button {
                background-color: #007BFF;
                color: white;
                padding: 10px 15px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
                width: 100%;
                transition: background 0.3s;
            }
            button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Create Payment Link</h2>
            <form method="POST" action="">
                <label for="amount">Amount (PHP):</label>
                <input type="number" name="amount" id="amount" required>

                <label for="description">Description:</label>
                <input type="text" name="description" id="description" required>

                <label for="remarks">Remarks:</label>
                <input type="text" name="remarks" id="remarks">

                <button type="submit">Create Link</button>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>
