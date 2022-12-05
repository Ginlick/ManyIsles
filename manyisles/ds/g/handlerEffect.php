<?php

// requires $clid, $mycode
require_once($_SERVER['DOCUMENT_ROOT'].'/ds/g/dsEngine.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/transactions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/account/prem/partAmount.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/modMailer.php');
$ds = new dsEngine;
$conn = $ds->conn; $moneyconn = $ds->moneyconn;
$mailer = new modMailer;

if (!isset($mycode) OR $mycode != $ds->give_actcode()){echo "invalid certification";exit();}

$query = "SELECT * FROM dsclearing WHERE id = $clid";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $customer = $row["buyer"];
        $purchase = $row["purchase"];
        $address = $row["address"];
        $country = $row["country"];
        $codeList = $row["codes"];
        $dsclearingTotal = $row["total"];
        $paidInfo = json_decode($row["paidInfo"], true);
    }
}
if (!isset($customer)) {echo "invalid clid";exit;}

$custTran = new transaction($moneyconn, $customer);
$custProm = new adventurer($conn, $customer);

$codeCookieReplacement = $codeList;
$purchase = explode(",", $purchase);
$basketed = new loopBasket;
$basketed->loopBasket($conn, $purchase, false, false, true, "items", true);

$partnerMsg = 'Someone just placed an order. You can handle it from your <a href="https://manyisles.ch/ds/p/hub">digital store hub</a>.';

$bigMail = <<<'BIGMAIL'
<p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
    Thank you for shopping with the Many Isles digital store. Payment for your order (id #%%CLID) was received and your order has been placed.<br>
    %%COOLINFOTEXT You can view your <a href="https://manyisles.ch/account/home?display=orders" style="color:#61b3dd">order status online</a>.
</p>
</div>
<!-- payment info -->
<div style="border-top: 3px solid #61b3dd;margin-top:50px">
<div style="width: 85%; margin:auto;">
  <h2 style="font-size: 25px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;">Payment Information</h2>
  <p style="padding:10px 10px 0;margin-bottom:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%PAYMENTADDRESS
  </p>
  <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
    <b>Payment Method:</b><br>
    %%PAYMENTMETHOD
  </p>
</div>
</div>
<!-- order list -->
<div style="border-top: 3px solid #61b3dd;margin-top:50px">
<div style="width: 85%; margin:auto;">
  <h2 style="font-size: 25px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;">Order Details</h2>
  <table style="margin:20px 0;padding:10px;width:100%;border-collapse:collapse;">
    <thead>
      <tr style="background-color:#ddd;">
        <td style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">Details</td>
        <td style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">Price</td>
        <td style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">Shipping</td>
        <td style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">Total</td>
      </tr>
    </thead>
    <tbody style="vertical-align:top;border-left: 1px dotted #ddd;">
      %%FULLLINEHERE
      <tr style="background-color:#ddd">
        <td style="padding:10px;">
          <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
            <b>TOTAL</b>
          </p>
        </td>
        <td></td>
        <td></td>
        <td style="padding:10px;">
          <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
            %%FULLPRICE
          </p>
        </td>
      </tr>
    </tbody>
  </table>
</div>
</div>
<!-- order list -->
<div style="border-top: 3px solid #61b3dd;margin-top:50px">
<div style="width: 85%; margin:auto;">
  <h2 style="font-size: 25px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;">Enjoy your products!</h2>
  <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      Thank you for shopping with us,<br><br>
      Many Isles Publishing Service
  </p>
</div>
BIGMAIL;

$itemLine = <<<STUDD
<tr style="padding-bottom:10px;border-bottom:1px dotted #ddd">
  <td style="border-right: 1px dotted #ddd;padding:10px;display:flex;flex-direction:row">
    <img src="%%COOLIMAGE" alt="Hello There!" style="height:120px;width:120px;object-fit:cover;display:block;border-radius:15px;" />
    <p style="margin:0;padding-left:20px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
        <b>%%COOLTITLE</b><br>
        %%COOLADDINFO
    </p>
  </td>
  <td style="border-right: 1px dotted #ddd;padding:10px;">
    <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%COOLPRICE
    </p>
  </td>
  <td style="border-right: 1px dotted #ddd;padding:10px;">
    <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%COOLSHIPPING
    </p>
  </td>
  <td style="border-right: 1px dotted #ddd;padding:10px;">
    <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%COOLTOTAL
    </p>
  </td>
</tr>
STUDD;
$pseudoItemLine = <<<STUDD
<tr style="padding-bottom:10px;border-bottom:1px dotted #ddd">
  <td style="padding-left:140px;border-right: 1px dotted #ddd;padding:10px;">
    <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%COOLTEXT
    </p>
  </td>
  <td style="border-right: 1px dotted #ddd;padding:10px;">
    <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%COOLPRICE
    </p>
  </td>
  <td style="border-right: 1px dotted #ddd;padding:10px;">
    <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%COOLSHIPPING
    </p>
  </td>
  <td style="border-right: 1px dotted #ddd;padding:10px;">
    <p style="margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
      %%COOLTOTAL
    </p>
  </td>
</tr>
STUDD;

$fullLine = "";
$sellerPayment = array("Royalty" => array("paid"=>0, "shipping"=>0, "amount"=>0, "items"=>"", "digital"=>1));
$ordersArray = array();

$sellerPaymentRoyalty = $sellerPayment["Royalty"];

foreach ($basketed->itemArray as $item) {
  $row = $item["row"];
  $prodname = $row["name"];
  $prodimg = $row["image"];
  $sellerId = $row['sellerId'];
  $prodId = $row["id"];
  $ordiprice = $item["price"] - $item["totalCodeReduc"];

  if (!isset($sellerPayment[$sellerId])){
      $sellerPayment[$sellerId] = array("paid"=>0, "shipping"=>0, "amount"=>0, "items"=>array(), "digital"=>1);
  }
  $sellerPaymentInfo = $sellerPayment[$sellerId];

  $royalty = ceil(($ordiprice*22)/1000);
  $toSeller = $ordiprice - $royalty;
  if ($prodId == 3 OR $prodId == 2 OR $prodId == 1){
    $royalty = 0;
    $toSeller = 0;
  }

  $passtotal = $sellerPaymentInfo["paid"] + $toSeller;
  $sellerPaymentRoyalty["paid"] = $sellerPaymentRoyalty["paid"] + $royalty;
  $sellerPaymentInfo["paid"] = $sellerPaymentInfo["paid"] + $toSeller;

  $sellerPaymentInfo["items"][] = $ds->detailsLine($item["prodSpecs"], $prodname);
  if ($row["digital"] == 0){$sellerPaymentInfo["digital"] = 0;}

  $artShipping = 0;
  $shippingCost = $ds->itemShipping($item, $country);
  $sellerPaymentInfo["shipping"] += $shippingCost;

  $totalCost = $ordiprice + $shippingCost;
  $sellerPaymentInfo["amount"] += $totalCost;
  $sellerPayment[$sellerId] = $sellerPaymentInfo;

  //generate tr for this entry
  $currentLine = str_replace("%%COOLPRICE", $ds->makeHuman($ordiprice), $itemLine);
  $currentLine = str_replace("%%COOLTOTAL", $ds->makeHuman($totalCost), $currentLine);
  $currentLine = str_replace("%%COOLTITLE", $prodname, $currentLine);
  $currentLine = str_replace("%%COOLIMAGE", $ds->clearImgUrl($prodimg), $currentLine);
  $coolAddInfo = "";
  if ($row["digital"] != 0) {
    $coolAddInfo .= "<i>Digital Product</i><br>";
    $currentLine = str_replace("%%COOLSHIPPING", "-", $currentLine);
  }
  else {
    $currentLine = str_replace("%%COOLSHIPPING", $ds->makeHuman($shippingCost), $currentLine);
  }
  $coolAddInfo .= "Seller: ".$row["seller"]." (p#".$row['sellerId'].")"."<br>";
  foreach ($item["prodSpecs"] as $addInfo){
      $coolAddInfo .= ucfirst($addInfo)."<br>";
  }
  $currentLine = str_replace("%%COOLADDINFO", $coolAddInfo, $currentLine);

  $fullLine .= $currentLine;

  //specials
  if ($prodId == 1){
    //tiers
    $newtitle = "Imperial Soldier"; $tieroption = 1;
    if (str_contains($item["assocDico"]["tier"], "1")){$newtitle = "Grand Wizard"; $tieroption = 2;}
    else if (str_contains($item["assocDico"]["tier"], "2")){$newtitle = "Legendar"; $tieroption = 3;}
    $custProm->promote($newtitle);
    payTiers($ordiprice, $tieroption);
  }
  else if ($prodId == 2){
    //credit
      $pTran = new transaction($moneyconn, $customer);
      $pTran->new($ordiprice, $custProm->fullName, "Pay-in");
  }
  else if ($prodId == 3) {
    //support product
    $option = $item["addName"];
    if ($option == "the Pantheon"){$option = "Pantheon";}
    $query = 'SELECT user FROM partners WHERE name = "'.$option.'"';
    if ($result = $conn->query($query)) {
        while ($row = $result->fetch_assoc()){
            $pId = $row["user"];
        }
    }

    if (isset($pId)) {
        $pTran = new transaction($moneyconn, $pId);
        $pTran->new($ordiprice, $custProm->fullName, "Support Payment");
        $custProm->promote("Journeyman");
    }
  }
}

$sellerPayment["Royalty"] = $sellerPaymentRoyalty;

if ($basketed->pureDigit == false) {
  $bigMail = str_replace("%%COOLINFOTEXT", "We will shortly send your items by postal service.", $bigMail);

  $text = <<<MACAE
    <b>Payment Address:</b></p><ul style="list-style-type: none;margin:0;padding:0;">
  MACAE;
  $address = $ds->parseAddressList($address);
  foreach ($address as $line){
    $text .= "<li style=\"margin:0;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;\">".$line."</li>";
  }
  $text .= "</ul>";
  $bigMail = str_replace("%%PAYMENTADDRESS", $text, $bigMail);
}
else {
  $bigMail = str_replace("%%COOLINFOTEXT", "All purchased changes were effectuated.", $bigMail);
  $bigMail = str_replace("%%PAYMENTADDRESS", "", $bigMail);
}

if (isset($paidInfo["extraFee"]) AND $paidInfo["extraFee"] > 0){
  $extraLine = str_replace("%%COOLTEXT", "Transfer Fee (".$paidInfo["method"].")", $pseudoItemLine);
  $extraLine = str_replace("%%COOLPRICE", $ds->makeHuman($paidInfo["extraFee"]), $extraLine);
  $extraLine = str_replace("%%COOLSHIPPING", "-", $extraLine);
  $extraLine = str_replace("%%COOLTOTAL", $ds->makeHuman($paidInfo["extraFee"]), $extraLine);
  $fullLine .= $extraLine;
}
if (isset($paidInfo["codeReduction"]) AND $paidInfo["codeReduction"] > 0){
  $text = "Codes";
  if ($codeList != ""){
      $text .=  " (".$codeList.")";
  }
  $extraLine = str_replace("%%COOLTEXT", $text, $pseudoItemLine);
  $extraLine = str_replace("%%COOLPRICE", $ds->makeHuman($paidInfo["codeReduction"]), $extraLine);
  $extraLine = str_replace("%%COOLSHIPPING", "-", $extraLine);
  $extraLine = str_replace("%%COOLTOTAL", $ds->makeHuman($paidInfo["codeReduction"]), $extraLine);
  $fullLine .= $extraLine;
}

$bigMail = str_replace("%%FULLLINEHERE", $fullLine, $bigMail);
$bigMail = str_replace("%%CLID", $clid, $bigMail);
$bigMail = str_replace("%%FULLPRICE", $ds->makeHuman($dsclearingTotal), $bigMail);

$paymentInfo = "Undefined";
if (isset($paidInfo["method"])){$paymentInfo = $paidInfo["method"];}
$bigMail = str_replace("%%PAYMENTMETHOD", $paymentInfo, $bigMail);

$txtSubject = "Order #$clid Placed";
$subject = "Your Many Isles digital store order #$clid confirmation";
$mailer->send("publishing@manyisles.ch", $txtSubject, $bigMail, "publishing", $txtSubject);
$mailer->send($custProm->email, $subject, $bigMail, "publishing", $txtSubject);

//pay partners
print_r($sellerPayment);

foreach ($sellerPayment as $partner => $partnerArray) {
    $paid = $partnerArray["paid"];
    $shippingPaid = $partnerArray["shipping"];
    if ($partner == "Royalty"){
        $pTran = new transaction($moneyconn, 14);
        $pTran->new($paid, $custProm->fullName, "Royalty on #$clid");
    }
    else {
        //create orders
        unset($partnerAccId);
        if ($partnerArray["digital"]==0){$ordStatus = 0;} else {$ordStatus = 2;}
        $orderItems = implode(", ", $partnerArray["items"]);
        $query = sprintf('INSERT INTO dsorders (orderId, buyer, seller, paid, shipping, items, address, amount, status, codes) VALUES ("%s", %s, %s, %s, %s, "%s", "%s", %s, %s, "%s")', $clid, $customer, $partner, $paid, $shippingPaid, $orderItems, $address, $partnerArray["amount"], $ordStatus, $codeList);
        if ($conn->query($query)){
          $ud = mysqli_insert_id($conn);
          $fullClid = $clid."-".$ud;
          //pay partners
          $query = "SELECT user FROM partners WHERE id = $partner";
          if ($result = $conn->query($query)) {
              while ($row = $result->fetch_assoc()){
                  $partnerAccId = $row["user"];
              }
          }
          $query = 'SELECT email FROM accountsTable WHERE id = "'.$partnerAccId.'"';
          if ($result = $conn->query($query)) {
              while ($row = $result->fetch_assoc()){
                  $partnerAccEmail = $row["email"];
              }
          }
          if (isset($partnerAccId)){
              $pTran = new transaction($moneyconn, $partnerAccId);
              $pTran->new($paid, $custProm->fullName, "Payment of Order #$fullClid");
              $pTran->new($shippingPaid, $custProm->fullName, "Shipping costs for Order #$fullClid");
          }
          //inform partners
          if ($partnerArray["digital"]==0){
            $partnerSubject = "New Order #".$fullClid;
            $mailer->send($partnerAccEmail, $partnerSubject, $partnerMsg, "publishing");
          }
        }
    }
}

if ($basketed->pureDigit == false) {
    $query = "UPDATE dsclearing SET status = 1 WHERE id = $clid";
}
else {
    $query = "UPDATE dsclearing SET status = 2 WHERE id = $clid";
}
$conn->query($query);

?>
