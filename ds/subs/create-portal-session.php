<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/ds/stripe-php-7.75.0/init.php');
require_once($_SERVER['DOCUMENT_ROOT']."/ds/keys/stripe-sk.php");
\Stripe\Stripe::setApiKey($stripe_sk);

header('Content-Type: application/json');

try {
    if (isset($_GET['session_id'])){
        $checkout_session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
        $customer = $checkout_session->customer;
    }
    else if (isset($_GET['customer_id'])){
        $customer = $_GET['customer_id'];
    } else {exit();}
  $return_url = $YOUR_DOMAIN . "/ds/subs/hub";

  // Authenticate your user.
  $session = \Stripe\BillingPortal\Session::create([
    'customer' => $customer,
    'return_url' => $return_url,
  ]);
  header("HTTP/1.1 303 See Other");
  header("Location: " . $session->url);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

session_start();
session_destroy();
