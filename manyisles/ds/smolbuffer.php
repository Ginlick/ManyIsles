﻿<?php

require_once("g/dsEngine.php");
$ds = new dsEngine;

$inbasket = $ds->basketed->inbasket;
if ($ds->basketed->totalPrice < 150) {
    $difference = 150 - $ds->basketed->totalPrice;
    $topush = "2-".$difference;
    array_push($inbasket, $topush);
}

$_SESSION["basket"] = implode(",", $inbasket);

header("Location:checkout2");exit;

?>
