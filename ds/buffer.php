<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

session_start();
require_once("g/sideBasket.php");

if ($basketed->totalPrice < 500) {
    $difference = 500 - $basketed->totalPrice;
    $topush = "2-".$difference;
    array_push($inbasket, $topush);
}

$_SESSION["basket"] = implode(",", $inbasket);

header("Location:checkout2.php");exit;

?>

