<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

if(!isset($_COOKIE["loggedIn"])){header("Location: checkout.html");exit();}

$id = $_COOKIE["loggedIn"];


$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $checkpsw = $row["password"];
      $confirmed = $row["emailConfirmed"];
      $customerEmail = $row["email"];
    }
}

$redirect = "checkout.html";
include("../Server-Side/checkPsw.php");
if ($confirmed == NULL){header("Location: checkoutw.php");exit();}

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

$line_items = [];
$totalPrice = 0;
$fullDCodeReduction = 0;
$sideBasket = false;
$countriesMatter = true;
$prodImage; $prodName;
$priceRef = "";
$itemId = null;

require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/loopBasket.php");
$basketed = new loopBasket($conn, $inbasket, true, false, true, $type);

foreach ($basketed->itemArray as $item){
    if ($basketed->type == "subs"){
        $prodName = $item["name"];
        $prodImage = "https://manyisles.ch/IndexImgs/goldenMyst.png";
        $priceRef = $item["row"]["datas"]["stripeId"];
        $itemId = $item["id"];
    }
    else {
        $prodName = $item["row"]["name"];
        $prodImage = $item["row"]["thumbnail"];
    }
}

require("g/shipping.php");
if($totalShipping === null){exit();}
$totalPrice = $basketed->totalPrice - $basketed->fullDCodeReduction;
$totalPrice += $totalShipping;

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


$stripeTax = $totalPrice*0.029;
$stripeTax = round($stripeTax);
$stripeTax = $stripeTax + 32;
$totalPrice = $totalPrice + $stripeTax;

if ($basketed->prodNum>1){
    $prodName = "Multiple Products";
    $prodImage = "https://manyisles.ch/Imgs/FaviconDS.png";
}

$price_dataCont = [
    'price_data' => [
        'currency' => 'usd',
        'unit_amount' => $totalPrice,
        'product_data' => [
        'name' => $prodName,
        'images' => [$prodImage],
        ],
    ],
];
if ($basketed->type == "subs"){
    $price_dataCont = [
        "price" => $priceRef
    ];
}

$multipleArray = [
    'quantity' => 1,
];
$multipleArray = array_merge($multipleArray, $price_dataCont);
array_push($line_items, $multipleArray);

$mode = "payment";
if ($basketed->type == "subs"){$mode = "subscription";$clearingId=1;}


if ($basketed->type == "items"){
    $query = "SELECT * FROM address WHERE id = ".$id;
    $result = $conn->query($query);
    $address = null;
    while ($row = $result->fetch_assoc()) {
        if ($row == null AND $pureDigit == false) {header("Location: checkout1.php");exit();}
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

    $query = sprintf('INSERT INTO dsclearing (buyer, total, purchase, address, country, codes) VALUES (%s, %s, "%s", "%s", "%s", "%s")', $id, $totalPrice, $_SESSION["basket"], $address, $country, $codeList);

    if ($conn->query($query)) {
        $clearingId = $conn->insert_id;
    }
}
else if ($basketed->type == "subs"){
    $fullDatas = json_encode(["subId"=>$itemId, "stripeId"=> $priceRef, "paymode"=>"stripe"]);
    $query = sprintf('INSERT INTO ds_asubs (buyer, datas, status, plan) VALUES (%s, \'%s\', "pending", '.$itemId.')', $id, $fullDatas);
    if ($moneyconn->query($query)) {
        $clearingId = $moneyconn->insert_id;
    }
}


/*print_r($line_items);

  $testArray = [[
    'price_data' => [
      'currency' => 'usd',
      'unit_amount' => 2000,
      'product_data' => [
        'name' => 'Stubborn Attachments',
        'images' => ["https://i.imgur.com/EHyR2nP.png"],
      ],
    ],
    'quantity' => 1,
  ]];
print_r($testArray);*/


require_once('stripe-php-7.75.0/init.php');
require_once("keys/stripe-sk.php");
header('Content-Type: application/json');

$metaInfo = ["clid"=>$clearingId, "type"=>$basketed->type];
$successLink = '/ds/success?type='.$basketed->type;


\Stripe\Stripe::setApiKey($stripe_sk);
$checkout_session = \Stripe\Checkout\Session::create([
  'customer_email' => $customerEmail,
  'payment_method_types' => ['card'],
  'line_items' => $line_items,
  'mode' => $mode,
  'success_url' => $YOUR_DOMAIN . $successLink,
  'cancel_url' =>  $YOUR_DOMAIN .'/ds/checkout2?w=fail',
  "metadata" => $metaInfo,
]);

if ($basketed->type == "subs"){
    header("Location: " . $checkout_session->url);
}
else {
    echo json_encode(['id' => $checkout_session->id]);  
}

?>

