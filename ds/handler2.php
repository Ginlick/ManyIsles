<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

require_once($_SERVER['DOCUMENT_ROOT'].'/ds/stripe-php-7.75.0/init.php');
require_once(dirname($_SERVER['DOCUMENT_ROOT'])."/media/keys/stripe-sk.php");
require_once(dirname($_SERVER['DOCUMENT_ROOT'])."/media/keys/ds-actcode.php");

require_once($_SERVER['DOCUMENT_ROOT'].'/ds/subs/newSub.php');
$query = "INSERT INTO tests (stuff) VALUES ('hail')"; $conn->query($query);

$mycode = $ds_actcode;

$plan = new subHandler($mycode, "stripe");
$plan->newSub("sub_1K0n7W2wqvxfBLhL1JpJC5cQ");




?>
