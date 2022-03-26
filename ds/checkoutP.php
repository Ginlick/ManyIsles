<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");
require_once(dirname($_SERVER['DOCUMENT_ROOT'])."/media/keys/ds-actcode.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/transactions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/ds/subs/subHandler.php');

$user = new adventurer();
if (!$user->check(true, true)){
  if ($user->signedIn){
    header("Location: checkoutw");exit();
  }
  header("Location: checkout");exit();
}
$id = $user->user;
$conn = $user->conn;
$custTran = new transaction($moneyconn, $id);

session_start();
if (!isset($_SESSION["subbasket"]) or $_SESSION["subbasket"] == ""){
    if (!isset($_SESSION["basket"]) or $_SESSION["basket"] == ""){
        header("Location: basket.php");exit();
    }
    else {
        $inbasket = explode(",", $_SESSION["basket"]);
        $type = "items";
    }
}
else {
    $inbasket = $_SESSION["subbasket"];
    $type = "subs";
}
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/loopBasket.php");
$basketed = new loopBasket($conn, $inbasket, true, false, true, $type);

require_once("g/shipping.php");
if($totalShipping === null){exit();}
$totalPrice = $basketed->totalPrice - $basketed->fullDCodeReduction;
$totalPrice += $totalShipping;
$permTotalPrice = $totalPrice;

if (!$basketed->pureDigit){
    if (count($basketed->deliverableCountries) == 0) {header("Location: checkout1.php");exit();}
    else {
        $query = "SELECT Country FROM address WHERE id = ".$id;
        $result = $conn->query($query);
        if (mysqli_num_rows($result) == 0) { header("Location: checkout1.php");exit(); }
        while ($row = $result->fetch_assoc()) {
            if (!isset($basketed->deliverableCountries[$row["Country"]])) { header("Location: checkout1.php");exit();}
        }
    }
}

$query = "SELECT * FROM address WHERE id = ".$id;
$result = $conn->query($query);
$address = null;
while ($row = $result->fetch_assoc()) {
if ($row == null AND $basketed->pureDigit == false) {header("Location: checkout1.php");exit();}
    $address = $row["fullname"].";".$row["address"].";".$row["Zip"].";".$row["city"].";".$row["Country"];
    $country = $row["Country"];
}

if (isset($_COOKIE["ds_codes"])){
    $codeList = $_COOKIE["ds_codes"];
}
else {
    $codeList = "";
}
require("g/killCodes.php");

if ($basketed->type == "items"){
    $query = sprintf('INSERT INTO dsclearing (buyer, total, purchase, address, country, codes) VALUES (%s, %s, "%s", "%s", "%s", "%s")', $id, $totalPrice, implode(",", $basketed->inbasket), $address, $country, $codeList);
    if ($conn->query($query)) {
        $clid = $conn->insert_id;
    }
    else {
        echo "<script>window.location.replace('checkout2')</script>";exit();
    }

    $oMotive = "Digital Store Order #$clid";
    if (!$custTran->new(0 - $permTotalPrice, $user->fullName, $oMotive)){
            echo "<script>window.location.replace('checkout2')</script>";exit();
    }

    $mycode = $ds_actcode;
    require_once("g/handlerEffect.php");
    $totalPrice = $permTotalPrice;
}
else if ($basketed->type == "subs") {
    $oMotive = "Subscription Payment";
    if (!$custTran->new(0 - $permTotalPrice, $user->fullName, $oMotive)){
            echo "<script>window.location.replace('checkout2')</script>";exit();
    }

    $sid = $basketed->itemArray[0]["id"];
    $fullDatas = json_encode(["subId"=>$sid, "paymode"=>"credit"]);
    $query = sprintf('INSERT INTO ds_asubs (buyer, datas, validity, plan) VALUES (%s, \'%s\', 365, '.$sid.')', $id, $fullDatas);
    $moneyconn->query($query);

    $subby = new subHandler($ds_actcode);
    $subby->subProfit($sid, $id);
}
else {exit();}

echo "<script>window.location.replace('success.php?type=$basketed->type');</script>";


$moneyconn->close();
$conn->close();

?>
