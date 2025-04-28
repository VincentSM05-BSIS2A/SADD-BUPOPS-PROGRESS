<?php
require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$response = $client->request('POST', 'https://api.paymongo.com/v1/payment_intents', [
  'body' => '{"data":{"attributes":{"amount":20000,"payment_method_allowed":["paymaya","billease","gcash"],"payment_method_options":{"card":{"request_three_d_secure":"any"}},"currency":"PHP","capture_type":"automatic","description":"test"}}}',
  'headers' => [
    'accept' => 'application/json',
    'authorization' => 'Basic c2tfdGVzdF9kUUtTRXhMU2NxejJ0VWVkdHA1cENodng6',
    'content-type' => 'application/json',
  ],
]);

echo $response->getBody();