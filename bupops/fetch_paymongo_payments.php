<?php
// fetch_paymongo_payments.php

// PayMongo Secret Key
$secret_key = 'sk_test_your_secret_key_here'; // ← Replace with your actual PayMongo Secret Key!

// Initialize CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/payment_intents");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($secret_key . ":"),
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
}
curl_close($ch);

// Decode JSON response
$data = json_decode($response, true);

// Connect to database
$conn = new mysqli("localhost", "root", "", "bupops_db");

// Save the latest payments into your `payments` table
foreach ($data['data'] as $payment) {
    $id = $conn->real_escape_string($payment['id']);
    $amount = $payment['attributes']['amount'] / 100; // PayMongo returns amount in centavos
    $status = $conn->real_escape_string($payment['attributes']['status']);
    $description = $conn->real_escape_string($payment['attributes']['description'] ?? 'No description');
    $email = $conn->real_escape_string($payment['attributes']['billing']['email'] ?? 'noemail@example.com');
    $name = $conn->real_escape_string($payment['attributes']['billing']['name'] ?? 'Anonymous');
    $created_at = date('Y-m-d H:i:s', strtotime($payment['attributes']['created_at']));

    // Insert or Update payments
    $conn->query("
        INSERT INTO payments (id, amount, status, description, email, name, created_at)
        VALUES ('$id', '$amount', '$status', '$description', '$email', '$name', '$created_at')
        ON DUPLICATE KEY UPDATE
            amount='$amount',
            status='$status',
            description='$description',
            email='$email',
            name='$name',
            created_at='$created_at'
    ");
}

echo json_encode(["success" => true]);
?>
