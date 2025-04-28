<?php
// Database connection (if needed)
$conn = new mysqli("localhost", "root", "", "bupops_db");

// Pagination settings
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for the query

// Fetch data from PayMono API (this is an example; replace with actual API call)
$api_url = "https://api.paymono.com/payments"; // Replace with actual API endpoint
$api_key = "YOUR_API_KEY"; // Replace with your API key

// Assuming we use cURL to fetch data from the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key"
]);
$response = curl_exec($ch);
curl_close($ch);

// Decode the JSON response
$payments = json_decode($response, true); // Replace with proper handling if needed

// Get only the necessary data for the current page
$paginated_data = array_slice($payments, $offset, $limit);

// Return JSON data
echo json_encode([
    'payments' => $paginated_data,
    'total' => count($payments), // Total number of records (for pagination)
    'pages' => ceil(count($payments) / $limit), // Total number of pages
]);
?>
