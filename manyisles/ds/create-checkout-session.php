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
$stripeTax = $ds->calcStripeTax($totalPrice);
$totalPrice = $totalPrice + $stripeTax;

if (!$basketed->pureDigit){
  if (count($basketed->deliverableCountries) == 0) {$ds->go("checkout1");}
  else if (!isset($basketed->deliverableCountries[$ds->fetchAddress()["country"]])){$ds->go("checkout1");}
}

//generate stripe's product object
$line_items = [];

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
if ($basketed->type == "subs"){$mode = "subscription";$clid=1;}

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

  $paidInfo = ["method" => "Stripe", "extraFee" => $stripeTax, "codeReduction" => $basketed->fullDCodeReduction];
  $paidInfo = json_encode($paidInfo);

  $query = sprintf('INSERT INTO dsclearing (buyer, total, purchase, address, country, codes, paidInfo) VALUES (%s, %s, "%s", "%s", "%s", "%s", \'%s\')', $id, $totalPrice, implode(",", $basketed->inbasket), $address, $country, $codeList, $paidInfo);
  if ($conn->query($query)) {
      $clid = $conn->insert_id;
  }
  else {
    $ds->go("checkout2?why=error");
  }
}
else if ($basketed->type == "subs"){
    $fullDatas = json_encode(["subId"=>$itemId, "stripeId"=> $priceRef, "paymode"=>"stripe"]);
    $query = sprintf('INSERT INTO ds_asubs (buyer, datas, status, plan) VALUES (%s, \'%s\', "pending", '.$itemId.')', $id, $fullDatas);
    if ($moneyconn->query($query)) {
        $clid = $moneyconn->insert_id;
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
header('Content-Type: application/json');
$stripe_sk = $ds->give_stripe_sk();
$YOUR_DOMAIN = "https://".$ds->giveServerInfo("servername");

$metaInfo = ["clid"=>$clid, "type"=>$basketed->type];

$successLink = '/ds/success?type='.$basketed->type;


\Stripe\Stripe::setApiKey($stripe_sk);
$checkout_session = \Stripe\Checkout\Session::create([
  'customer_email' => $ds->user->email,
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
