<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/ds/g/dsEngine.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/transactions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/ds/subs/subHandler.php');

$ds = new dsEngine(true);
if (!$ds->user->check(true, true)){
  $ds->go("checkoutw");
}

$id = $ds->user->user;
$conn = $ds->conn; $moneyconn = $ds->moneyconn;
$custTran = new transaction($moneyconn, $id);

$type = $ds->type;
$basketed = $ds->basketed;
$totalShipping = $ds->shipping();

$totalPrice = $ds->totalPrice($basketed, $totalShipping);
$codeReduction = $ds->basketed->fullDCodeReduction;

if (!$basketed->pureDigit){
  if (count($basketed->deliverableCountries) == 0) {$ds->go("checkout1");}
  else if (!isset($basketed->deliverableCountries[$ds->fetchAddress()["country"]])){$ds->go("checkout1");}
}

if ($basketed->type == "items"){
  $query = "SELECT * FROM address WHERE id = ".$id;
  $result = $conn->query($query);
  $address = null;
  while ($row = $result->fetch_assoc()) {
  if ($row == null AND $basketed->pureDigit == false) {header("Location: checkout1.php");exit();}
      $address = $row["fullname"].";".$row["address"].";".$row["Zip"].";".$row["city"].";".$row["Country"];
      $country = $row["Country"];
  }

  if ($basketed->codesExist AND isset($_COOKIE["ds_codes"])){
      $codeList = $_COOKIE["ds_codes"];
  }
  else {
      $codeList = "";
  }
  require("g/killCodes.php");

  $paidInfo = ["method" => "Many Isles credit", "extraFee" => 0, "codeReduction" => $codeReduction];
  $paidInfo = json_encode($paidInfo);

  $query = sprintf('INSERT INTO dsclearing (buyer, total, purchase, address, country, codes, paidInfo) VALUES (%s, %s, "%s", "%s", "%s", "%s", \'%s\')', $id, $totalPrice, implode(",", $basketed->inbasket), $address, $country, $codeList, $paidInfo);
  if ($conn->query($query)) {
      $clid = $conn->insert_id;
  }
  else {
    $ds->go("checkout2?why=error");
  }

  $oMotive = "Digital Store Order #$clid";
  if (!$custTran->new(0 - $totalPrice, $ds->user->fullName, $oMotive)){
    $ds->go("checkout2?why=error");
  }

  $mycode = $ds->give_actcode();
  require_once("g/handlerEffect.php");
}
else if ($basketed->type == "subs") {
    $oMotive = "Subscription Payment";
    if (!$custTran->new(0 - $totalPrice, $ds->user->fullName, $oMotive)){
      $ds->go("checkout2?why=error");
    }
    $sid = $basketed->itemArray[0]["id"];
    $fullDatas = json_encode(["subId"=>$sid, "paymode"=>"credit"]);
    $query = sprintf('INSERT INTO ds_asubs (buyer, datas, validity, plan) VALUES (%s, \'%s\', 365, '.$sid.')', $id, $fullDatas);
    $moneyconn->query($query);

    $subby = new subHandler($ds->give_actcode());
    $subby->subProfit($sid, $id);
}
else {
  $ds->go("checkout2?why=error");
}
$ds->go("success?type=".$basketed->type);

?>
