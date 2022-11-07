<?php
session_start();

if (isset($_GET["which"])) {if (preg_match("/[^0-9]/", $_GET["which"])==1){header("Location: home.php");exit();} else {$which = $_GET["which"];} } else  {$which = 0;}
if (isset($_GET["allItem"])) {if (preg_match("/[^0-9]/", $_GET["allItem"])==1){header("Location: home.php");exit();} else {$allItem = $_GET["allItem"];} } else  {$allItem = 0;}
if (isset($_GET["return"])) {if (preg_match("/[^a-zA-Z]/", $_GET["return"])==1){header("Location: home.php");exit();} else {$return = $_GET["return"];} } else  {$return = "";}


$inbasket = explode(",", $_SESSION["basket"]);
//echo $_SESSION["basket"]."<br>";
//echo $inbasket[2];


if ($allItem == 0){
    unset($inbasket[$which]);
}
else {
    require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/loopBasket.php");
    $killing = new loopBasket($conn, $inbasket);
    foreach ($killing->itemArray as $item){
        if ($item["row"]["id"] == $allItem){
            unset($inbasket[$item["basketPos"]]);
        }
    }
}

$_SESSION["basket"] = implode(",", $inbasket);

if ($_SESSION["basket"]=="") {unset($_SESSION["basket"]);}


if (isset($_GET["sender"])){
    header("Location:".$_GET["sender"]);exit;
}
else if ($return != ""){
    header("Location:basket.php?show=$return");exit;
}
else {
    header("Location:basket.php");exit;
}

?>

