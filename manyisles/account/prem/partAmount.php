<?php


function giveProdAmounts() {
  require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
  require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dl.php");
  $partnersArr = [];
  $query = "SELECT * FROM partners WHERE type > 0";
  if ($max = $conn->query($query)) {
    while ($row = $max->fetch_assoc()){
      $partnerId = $row["id"];
      $partnerArr = [0=>0,1=>0,2=>0,3=>0,"account"=>$row["user"]];
      $query = "SELECT tier FROM products WHERE partner = $partnerId";
      if ($max2 = $dlconn->query($query)) {
        while ($row2 = $max2->fetch_assoc()){
          $partnerArr[$row2["tier"]] = $partnerArr[$row2["tier"]] + 1;
          $partnersArr[$partnerId] = $partnerArr;
        }
      }
    }
  }
  return $partnersArr;
}

function giveTierValue($partId, $tier, $partnersArr = null) {
  $fullNums = 0;
  if ($partnersArr == null){$partnersArr = giveProdAmounts();}
  if ($partnersArr[$partId][$tier]==0){return 0;}
  foreach ($partnersArr as $partnerArr) {
    $fullNums += $partnerArr[$tier];
  }
  if (isset($partnersArr[$partId])){
    return round($partnersArr[$partId][$tier] / $fullNums, 4);
  }
  return false;
}

function payTiers($amount, $tier){
  require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/transactions.php');
  if (!isset($moneyconn)){
    require($_SERVER['DOCUMENT_ROOT'].'/Server-Side/db_money.php');
  }

  $partnersArr = giveProdAmounts();
  $transferred = 0;
  foreach ($partnersArr as $partner => $partnerArr){
    if ($transferred > $amount){return true;}
    $tierValue = giveTierValue($partner, $tier, $partnersArr);
    $awarded =  floor($amount * $tierValue);
    $pTran = new transaction($moneyconn, $partnerArr["account"]);
    $pTran->new($awarded, "Partnership Program", "Tier $tier Purchase");
    $transferred += $awarded;
  }
  return true;
}
?>
