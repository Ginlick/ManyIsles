<?php

// requires $conn, $moneyconn, $clid, $customer_email, $mycode

require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/transactions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/promote.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/ds/keys/ds-actcode.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/account/prem/partAmount.php');
if (!isset($mycode) OR $mycode != $ds_actcode){echo "invalid certification";exit();}

require_once("g/countries.php");
if (!class_exists("loopBasket")){require_once("g/loopBasket.php");}

$query = "SELECT * FROM dsclearing WHERE id = $clid";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $customer = $row["buyer"];
        $purchase = $row["purchase"];
        $address = $row["address"];
        $country = $row["country"];
        $codeList = $row["codes"];
        $dsclearingTotal = $row["total"];
    }
}

$query = "SELECT * FROM accountsTable WHERE id = $customer";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $customer_tier = $row["tier"];
    $customer_name = $row["uname"];
    $customer_title = $row["title"];
}

$custTran = new transaction($moneyconn, $customer);
$custProm = new adventurer($conn, $customer);

$codeCookieReplacement = $codeList;
$purchase = explode(",", $purchase);
$inbasket = $purchase;
$basketed = new loopBasket($conn, $purchase, false, false, true);


$partnerMail = <<<MASSMAIL
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://manyisles.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">New Order #22222</h1>
    <p style="text-align: center;font-size: calc(8px + 0.9vw);color: black;padding:10px;">
        Someone just placed an order. You can handle it from your <a href="https://manyisles.ch/ds/p/hub.php">digital store hub</a>.
    </p>
</body>
</html>

MASSMAIL;
$bigMail = <<<'BIGMAIL'
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title></title>
</head>
<body>

<div style="width: 100%;background-color: #d1a720;margin:0;min-height:100vh;padding:2vw;box-sizing: border-box;">
    <img src="https://manyisles.ch/Imgs/FaviconDS.png" style="width:10%;padding:1vw 1vw 0 1vw" />
    <h2 style="color:white;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;display:inline-block;font-size:8vw; margin:0;transform: translate(0, -22%);">Many Isles</h2>
    <div style="background-color:white;padding:1vw;border-radius:22px;min-height:1000px;">
        <h1 style="text-align:center;font-size:calc(12px + 3vw);color:black;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;margin-bottom:0;">Order Cleared</h1>
        <p style="text-align:center;font-size:calc(8px + 0.8vw);color:black;margin-top:20px;margin-bottom:5px;margin-right:5%;margin-left:5%;">
            Order id: #COOLCLID<br />
        </p>
        <p style="text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;margin-right:5%;margin-left:5%;">
            COOLINFOTEXT<br />
        </p>

        PRODLINESTUDD

        <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
        <p style="text-align:center;font-size:calc(8px + 1.5vw);color:black;margin-top:15px;margin-bottom:15px;">
            Total: COOLAMOUNT
        </p>
        <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
        <p style="text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
            INFOABOUTCODESIf you have any questions or problems, please contact us.<br /><br />
        </p>


    </div>
</div>

</body>
</html>

BIGMAIL;

$itemLine = <<<STUDD

        <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
        <div style="width:15%;margin:2.5%;float:left;display:block;position:relative">
            <img src="https://manyisles.ch/ds/images/thumbnails/COOLIMAGE" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
        </div>
        <h2 style="width:80%;float:left;position:relative;text-align:left;font-size:calc(8px + 2.5vw);color:black;margin-bottom:0px;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">COOLTITLE</h2>
        <p style="width:80%;float:left;position:relative;text-align:left;font-size:calc(8px + 1.5vw);color:black;margin-top:5px;margin-bottom:5px;">
            COOLPRICE
        </p>
        <p style="width:80%;float:left;position:relative;text-align:left;font-size:calc(8px + 1.3vw);color:grey;margin-bottom:5px;">
            COOLADDINFO
        </p>

STUDD;

$fullLine = "";
$sellerPayment = array(
"Royalty" => array("paid"=>0, "shipping"=>0, "amount"=>0, "items"=>"", "digital"=>1)
);
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
if ($prodId == 3){$royalty = 0;}
else {
    $royalty = ceil(($ordiprice*22)/1000);
}

$sellerPaymentRoyalty["paid"] = $sellerPaymentRoyalty["paid"] + $royalty;
$toSeller = $ordiprice - $royalty;
$passtotal = $sellerPaymentInfo["paid"] + $toSeller;
$sellerPaymentInfo["paid"] = $sellerPaymentInfo["paid"] + $toSeller;

$sellerPaymentInfo["items"][] = detailsLine($prodname, $item["prodSpecs"]);
if ($row["digital"] == 0){$sellerPaymentInfo["digital"] = 0;}

$artShipping = $row["shipping"];
$shippingCost = $item["specShipping"];
if ($artShipping != ""){
    $chunks = array_chunk(preg_split('/(:|,)/', $artShipping), 2);
    $assocDico = array_combine(array_column($chunks, 0), array_column($chunks, 1));
    foreach ($assocDico as $key => $value) {
        if (strlen($key) == 3){
        //see if it's in a country array
            $currentArray =  $countries[$key];
            if (isset($currentArray[$country])){
                $shippingCost = $value;
                break;
            }
        }
        else if (strlen($key) == 2){
        //single country
            if ($key==$country){
                $shippingCost = $value;
                break;
            }
        }
    }
}
$sellerPaymentInfo["shipping"] += $shippingCost;

$sellerPaymentInfo["amount"] += $ordiprice + $shippingCost;
$sellerPayment[$sellerId] = $sellerPaymentInfo;

//generate email line
$currentLine = str_replace("COOLPRICE", makeHuman($ordiprice), $itemLine);
$currentLine = str_replace("COOLTITLE", $prodname, $currentLine);
$currentLine = str_replace("COOLIMAGE", $prodimg, $currentLine);
$coolAddInfo = "";
$coolAddInfo = $coolAddInfo."Seller: ".$row["seller"]." (p#".$row['sellerId'].")"."<br>";
if ($row["digital"] == 0) {$coolAddInfo = $coolAddInfo."Shipping: ".makeHuman($shippingCost)."<br>";}
foreach ($item["prodSpecs"] as $addInfo){
    $coolAddInfo .= ucfirst($addInfo)."<br>";
}
$currentLine = str_replace("COOLADDINFO", $coolAddInfo, $currentLine);

$fullLine = $fullLine.$currentLine;

//update stock info
if ($row["digital"] == 0){
    $query = "UPDATE dsprods SET stock = stock - ".$item["quant"]." WHERE id = $prodId";
    $conn->query($query);
}
}

$sellerPayment["Royalty"] = $sellerPaymentRoyalty;

if ($basketed->pureDigit == false) {
    $bigMail = str_replace("COOLINFOTEXT", "Your payment has been received and we will shortly send your items by postal service. This mail is your receipt.", $bigMail);
}
else {
    $bigMail = str_replace("COOLINFOTEXT", "Your payment has been received and the changes effectuated. This mail is your receipt.", $bigMail);
}
$bigMail = str_replace("PRODLINESTUDD", $fullLine, $bigMail);
$bigMail = str_replace("COOLCLID", $clid, $bigMail);
$bigMail = str_replace("COOLAMOUNT", makeHuman($dsclearingTotal), $bigMail);
if ($codeList != ""){
    $bigMail = str_replace("INFOABOUTCODES", "Codes used: ".$codeList."<br>", $bigMail);
}
else {
    $bigMail = str_replace("INFOABOUTCODES", "", $bigMail);
}
//echo $bigMail;

$headers = "From: pantheon@manyisles.ch" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

mail ("pantheon@manyisles.ch", "Order #$clid Cleared", $bigMail, $headers);
mail ($customer_email, "Order #$clid Cleared", $bigMail, $headers);


// calc stuff

foreach ($purchase as $x => $value) {
    if ($value == "") {continue;}
    if (stripos($value, "[")) {
        $shortitem = substr($value, 0, strpos($value, "["));
    }
    else if (stripos($value, "-")) {
        $shortitem = substr($value, 0, strpos($value, "-"));
    }
    else if (stripos($value, "(")) {
        $shortitem = substr($value, 0, strpos($value, "("));
    }
    else {$shortitem = $value;}

    if ($shortitem == 1) {
      //tiers
        if (str_contains($value, "tier:2")){$tieroption = 3; $newtitle = "Legendar";}
        else if (str_contains($value, "tier:1")){$tieroption = 2; $newtitle = "Grand Wizard";}
        else {$newtitle = "Imperial Soldier"; $tieroption = 1;}
        $custProm->promote($newtitle);
        $forPartner = floor(0.5*$ordiprice);
        payTiers($forPartner, $tieroption);
    }
    else if ($shortitem == 2){
      //credit
        $price = substr($value, strpos($value, "-"));
        $price = str_replace("-", "", $price);
        $credit = $price;

        $pTran = new transaction($moneyconn, $customer);
        $pTran->new($credit, $customer_title." ".$customer_name, "Pay-in");
    }
    else if ($shortitem == 3) {
      //support product
        $price = substr($value, stripos($value, "/")+1, -1);
        $option = substr($value, stripos($value, "(")+1, stripos($value, "/")-2);
        if ($option == "the Pantheon"){$option = "Pantheon";}
        $query = 'SELECT * FROM partners WHERE name = "'.$option.'"';
        if ($result = $conn->query($query)) {
            while ($row = $result->fetch_assoc()){
                $paccname = $row["account"];
            }
        }
        $query = 'SELECT id FROM accountsTable WHERE uname = "'.$paccname.'"';
        if ($result = $conn->query($query)) {
            while ($row = $result->fetch_assoc()){
                $pId = $row["id"];
            }
        }
        if (isset($pId)) {
            $pTran = new transaction($moneyconn, $pId);
            $pTran->new($price, $customer_title." ".$customer_name, "Support Payment");
            $custProm->promote("Journeyman");
        }
    }
}

//pay partners
print_r($sellerPayment);

foreach ($sellerPayment as $partner => $partnerArray) {
    $paid = $partnerArray["paid"];
    $shippingPaid = $partnerArray["shipping"];
    if ($partner == "Royalty"){
        $pTran = new transaction($moneyconn, 14);
        $pTran->new($paid, $customer_title." ".$customer_name, "Royalty on #$clid");
    }
    else {
        //create orders
        unset($partnerAccId);
        if ($partnerArray["digital"]==0){$ordStatus = 0;} else {$ordStatus = 2;}
        $orderItems = implode(",", $partnerArray["items"]);
        $query = sprintf('INSERT INTO dsorders (orderId, buyer, seller, paid, shipping, items, address, amount, status, codes) VALUES ("%s", %s, %s, %s, %s, "%s", "%s", %s, %s, "%s")', $clid, $customer, $partner, $paid, $shippingPaid, $orderItems, $address, $partnerArray["amount"], $ordStatus, $codeList);
        if ($conn->query($query)){
            //pay partners
            $query = "SELECT account FROM partners WHERE id = $partner";
            if ($result = $conn->query($query)) {
                while ($row = $result->fetch_assoc()){
                    $partnerAccName = $row["account"];
                }
            }
            $query = 'SELECT id, email FROM accountsTable WHERE uname = "'.$partnerAccName.'"';
            if ($result = $conn->query($query)) {
                while ($row = $result->fetch_assoc()){
                    $partnerAccId = $row["id"];
                    $partnerAccEmail = $row["email"];
                }
            }
            if (isset($partnerAccId)){
                $pTran = new transaction($moneyconn, $partnerAccId);
                $pTran->new($paid, $customer_title." ".$customer_name, "Payment of Order #$clid");
                $pTran->new($shippingPaid, $customer_title." ".$customer_name, "Shipping costs for Order #$clid");
            }
            //inform partners
            if ($partnerArray["digital"]==0){
                $partnerMail = str_replace("22222", $clid, $partnerMail);
                $headers = "From: pantheon@manyisles.ch" . "\r\n";
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                mail ($partnerAccEmail, "New Order #$clid", $partnerMail, $headers);
            }
        }
    }
}

if ($pureDigit == false) {
    $query = "UPDATE dsclearing SET status = 1 WHERE id = $clid";
}
else {
    $query = "UPDATE dsclearing SET status = 2 WHERE id = $clid";
}
$conn->query($query);


?>
