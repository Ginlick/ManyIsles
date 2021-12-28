<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

$session = $_POST["session"];

$session = json_decode($session, true);
$data = $session["data"];
$object2 = $data["object"];
$customer_email = $object2["customer_email"];

$success_url = $object2["success_url"];
$parsed_url = parse_url($success_url);
parse_str($parsed_url["query"], $parsed_query);
$clid = $parsed_query["cl"];

$query = "SELECT * FROM dsclearing WHERE id = $clid";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $customer = $row["buyer"];
        $purchase = $row["purchase"];
        $address = $row["address"];
    }
}

$query = "SELECT * FROM accountsTable WHERE id = $customer";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $customer_tier = $row["tier"];
    $customer_name = $row["uname"];
    $customer_title = $row["title"];
}

$purchase = explode(",", $purchase);
$inbasket = $purchase;

$sideBasket = false;
require_once("g/loopBasket.php");

if ($pureDigit == false) {
$bigMail = <<<BIGMAIL
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
            <p style="text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;margin-right:5%;margin-left:5%;">
                Your payment has been received and we will shortly send your items by postal service.<br />
            </p>

            PRODLINESTUDD

            <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
            <p style="text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
                If you have any questions or problems, please contact us.<br /><br />
            </p>


        </div>
    </div>

</body>
</html>

BIGMAIL;
$itemLine = <<<STUDD

            <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
            <div style="width:15%;margin:2.5%;float:left;display:block;position:relative">
                <img src="/ds/images/thumbnails/COOLIMAGE" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
            </div>
            <h2 style="width:80%;float:left;position:relative;text-align:left;font-size:calc(8px + 2.5vw);color:black;margin-bottom:0px;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">COOLTITLE</h2>
            <p style="width:80%;float:left;position:relative;text-align:left;font-size:calc(8px + 1.5vw);color:grey;margin-top:5px;margin-bottom:5px;">
                COOLPRICE
            </p>

STUDD;

    $fullLine = NULL;
    foreach ($purchase as $x => $value) {
        if (stripos($value, ":")) {
            $shortitem = substr($value, 0, strpos($value, ":"));       
        }
        else {$shortitem = $value;}
        $query = "SELECT * FROM dsprods WHERE id = ".$shortitem;
        if ($result = $conn->query($query)) {
            while ($row = $result->fetch_assoc()) {
                if (stripos($row["price"], ",")) {
                    $option = substr($value, strpos($value, ":"));
                    $option = str_replace(":", "", $option);
                    $option = intval($option) - 1;
                    $priceOptions = explode(",", $row["price"]);
                    $price = $priceOptions[$option];
                }
                else {$price = $row["price"];}
                $price = "$".$price;

                $prodname = $row["name"];
                $prodimg = $row["image"];
                $currentLine = str_replace("COOLPRICE", $price, $itemLine);
                $currentLine = str_replace("COOLTITLE", $prodname, $currentLine);
                $currentLine = str_replace("COOLIMAGE", $prodimg, $currentLine);
                $fullLine = $fullLine.$currentLine;
            }
        }
    }

    $bigMail = str_replace("PRODLINESTUDD", $fullLine, $bigMail);

    $headers = "From: pantheon@manyisles.ch" . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    mail ("pantheon@manyisles.ch", "Order $clid Cleared", $bigMail, $headers);
    mail ($customer_email, "Order Cleared", $bigMail, $headers);

}
else {

    foreach ($purchase as $x => $value) {
        if ($value == "") {continue;}
        if (stripos($value, ":")) {
            $shortitem = substr($value, 0, strpos($value, ":"));       
        }
        else if (stripos($value, "-")) {
            $shortitem = substr($value, 0, strpos($value, "-"));       
        }
        else {$shortitem = $value;}

        if ($shortitem == 1) {
            $tieroption = substr($value, -1, strpos($value, ":"));
            if ($tieroption == 1){$newtitle = "Imperial Soldier";}
            if ($tieroption == 2){$newtitle = "Grand Wizard";}
            if ($tieroption == 3){$newtitle = "Legendar";}
            if ($customer_tier != "g"){$customer_tier = intval($customer_tier);}
            if ($customer_tier == "g" or $customer_tier<intval($tieroption)){
                $query = "UPDATE accountsTable SET tier = $tieroption, title = '$newtitle' WHERE id = $customer";
                $conn->query($query);
                $customer_tier = $tieroption;
            }
        }
        else if ($shortitem == 2){
            $price = substr($value, strpos($value, "-"));
            $price = str_replace("-", "", $price);
            $credit = $price*0.971;
            $credit = round($credit);
            $credit = $credit - 32;

            $query = "SELECT * FROM global_credit WHERE id = ".$customer;
            if ($result = $moneyconn->query($query)) {
                if (mysqli_num_rows($result) == 0) {
                    echo "there's nothing";
                    $reference = rand(10000000, 99999999);
                    $reference = $customer.$reference;
                    $squery = "INSERT INTO global_credit (id, credit, reference) VALUES ($customer, 0, '$reference')";
                    $moneyconn->query($squery);
                    $squery = sprintf("CREATE TABLE transfers_%s (motive VARCHAR(200), source VARCHAR(200), amount INT, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)", $reference);
                    $moneyconn->query($squery);
                }
            }
            if ($result = $moneyconn->query($query)) {
                while ($row = $result->fetch_assoc()) {
                    $reference = $row["reference"];
                    $oldTotal = $row["credit"];
                }
                $newTotal = $oldTotal + $credit;
                $query = "INSERT INTO transfers_".$reference." (motive, source, amount) VALUES ('Pay-in', '".$customer_title." ".$customer_name."', $credit)";
                if ($moneyconn->query($query)) {
                    $query = "UPDATE global_credit SET credit = $newTotal WHERE id = $customer";
                    $moneyconn->query($query);
                }
            }
            
        }

    }
}

session_start();
session_destroy();

if ($pureDigit == false) {
    $query = "UPDATE dsclearing SET status = 1 WHERE id = $clid";
}
else {
    $query = "DELETE FROM dsclearing WHERE id = $clid";
}
$conn->query($query);

?>

