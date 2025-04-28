<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Replace with your actual PayMongo Test Secret Key
$secret_key = "sk_test_dQKSExLScqz2tUedtp5pChvx";

// Function to make API requests to PayMongo
function makePayMongoRequest($url, $method = 'POST', $data = null) {
    global $secret_key;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($secret_key . ':'),
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        curl_close($ch);
        exit();
    }
    curl_close($ch);
    return json_decode($response, true);
}

// If user clicked "Pay Now"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    // Step 1: Create Payment Intent
    $payment_intent_data = [
        'data' => [
            'attributes' => [
                'amount' => (int)$amount,
                'payment_method_allowed' => ['gcash', 'paymaya'],
                'payment_method_types' => ['method'],
                'currency' => 'PHP',
                'description' => $description,
            ]
        ]
    ];
    $payment_intent_response = makePayMongoRequest('https://api.paymongo.com/v1/payment_intents', 'POST', $payment_intent_data);

    if (isset($payment_intent_response['data']['attributes']['client_key'])) {
        $client_key = $payment_intent_response['data']['attributes']['client_key'];
        $payment_intent_id = $payment_intent_response['data']['id'];
        // Show the payment method selection form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Select Payment Method</title>
        </head>
        <body>
            <h1>Select Payment Method</h1>
            <form method="POST">
                <input type="hidden" name="payment_intent_id" value="<?php echo $payment_intent_id; ?>">
                <input type="hidden" name="client_key" value="<?php echo $client_key; ?>">
                <button type="submit" name="method" value="gcash">Pay with GCash</button>
                <button type="submit" name="method" value="paymaya">Pay with PayMaya</button>
            </form>
        </body>
        </html>
        <?php
        exit();
    } else {
        echo "Error: Failed to create Payment Intent. Response: " . print_r($payment_intent_response, true);
        exit();
    }
}

// If user selected a payment method
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['method'])) {
    $payment_intent_id = $_POST['payment_intent_id'];
    $client_key = $_POST['client_key'];
    $method = $_POST['method'];

    // Step 2: Create Payment Method with required return_url
    $payment_method_data = [
        'data' => [
            'attributes' => [
                'type' => $method,
                'details' => [
                    // Replace with your actual return URL endpoint.
                    'return_url' => 'http://localhost/bupops/sucess.html'
                ]
            ]
        ]
    ];
    $payment_method_response = makePayMongoRequest('https://api.paymongo.com/v1/payment_methods', 'POST', $payment_method_data);

    if (isset($payment_method_response['data']['id'])) {
        $payment_method_id = $payment_method_response['data']['id'];

        // Step 3: Attach Payment Method, now including return_url
        $attach_data = [
            'data' => [
                'attributes' => [
                    'payment_method' => $payment_method_id,
                    'client_key' => $client_key,
                    'return_url' => 'http://localhost/bupops/sucess.html'
                ]
            ]
        ];
        $attach_url = "https://api.paymongo.com/v1/payment_intents/$payment_intent_id/attach";
        $attach_response = makePayMongoRequest($attach_url, 'POST', $attach_data);

        if (isset($attach_response['data']['attributes']['next_action']['redirect']['url'])) {
            $redirect_url = $attach_response['data']['attributes']['next_action']['redirect']['url'];
            header("Location: $redirect_url");
            exit();
        } else {
            echo "Error: Failed to attach payment method. Response: " . print_r($attach_response, true);
            exit();
        }
    } else {
        echo "Error: Failed to create Payment Method. Response: " . print_r($payment_method_response, true);
        exit();
    }
}
?>

<!-- Default page (no form submitted yet) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Payment</title>
</head>
<body>
    <h1>Test Payment Page</h1>
    <form method="POST">
        <input type="hidden" name="amount" value="20000"> <!-- ₱200.00 -->
        <input type="hidden" name="description" value="Test Payment">
        <button type="submit" name="pay_now">Pay Now</button>
    </form>
</body>
</html>
