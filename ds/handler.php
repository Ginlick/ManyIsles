<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

require_once($_SERVER['DOCUMENT_ROOT'].'/ds/stripe-php-7.75.0/init.php');
require_once(dirname($_SERVER['DOCUMENT_ROOT'])."/media/keys/stripe-sk.php");
require_once(dirname($_SERVER['DOCUMENT_ROOT'])."/media/keys/ds-actcode.php");

require_once($_SERVER['DOCUMENT_ROOT'].'/ds/subs/subHandler.php');

function print_log($val) {
  return file_put_contents('php://stderr', print_r($val, TRUE));
}

\Stripe\Stripe::setApiKey($stripe_sk);
$endpoint_secret = $stripe_whsec;
$mycode = $ds_actcode;

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  http_response_code(400);
  exit();
}

switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;
        $clid = $session->metadata["clid"];

        if ($session->metadata["type"]=="items"){
            require_once($_SERVER['DOCUMENT_ROOT'].'/ds/g/handlerEffect.php');

            session_start();
            session_destroy();
        }
        else if ($session->metadata["type"]=="subs"){
            $plan = new subHandler($mycode, "stripe", $session);
            $plan->newSub($clid);
        }

        break;
    case 'customer.subscription.created':

        break;
    case 'customer.subscription.deleted':
        $session = $event->data->object;

        $plan = new subHandler($mycode, "stripe", $session);
        $plan->delSub($session->id);
        break;
    case 'customer.subscription.updated':
        $session = $event->data->object;

        $plan = new subHandler($mycode, "stripe", $session);
        $plan->upSub($session->id);
        break;
}


http_response_code(200);


?>
