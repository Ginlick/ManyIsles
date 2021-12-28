<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

session_start();
$inbasket = explode(",", $_SESSION["basket"]);
require_once("g/loopBasket.php");

if ($totalPrice < 150) {
    $difference = 150 - $totalPrice;
    $topush = "2-".$difference;
    array_push($inbasket, $topush);
}

$_SESSION["basket"] = implode(",", $inbasket);

header("Location:checkout2.php");exit;

?>

