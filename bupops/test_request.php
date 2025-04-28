<?php
require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$response = $client->request('POST', 'https://api.paymongo.com/v1/payment_methods', [
  'body' => '{"data":{"attributes":{"billing":{"name":"test","email":"test@test.com","phone":"099999999"},"type":"gcash"}}}',
  'headers' => [
    'Content-Type' => 'application/json',
    'accept' => 'application/json',
    'authorization' => 'Basic c2tfdGVzdF9kUUtTRXhMU2NxejJ0VWVkdHA1cENodng6',
  ],
]);

echo $response->getBody();