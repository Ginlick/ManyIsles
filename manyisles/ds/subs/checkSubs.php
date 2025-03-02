<?php

$subject = "Plan Cannot be Renewed";
$message = <<<MYGREATMAIL
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://kartecaedras.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Insufficient Credit</h1>
    <p style="
            text-align: center;
            font-size: calc(8px + 0.9vw);
            color: black;
            padding:10px;
    ">
        Your AWESOMECOOL subscription will run out in three days, and you do not have enough credit on your account to renew it. <a href="https://kartecaedras.ch/ds/credit">Purchase more</a> so you don't lose your subscription's features!<br />
        View your subscription on your <a href="https://kartecaedras.ch/ds/subs/hub">hub</a>.

    </p>
</body>
</html>
MYGREATMAIL;

$headers = "From: pantheonmanyisles.ch" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";


require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/transactions.php");

$query = "DELETE FROM ds_asubs WHERE validity = 0";
$moneyconn->query($query);

$subsArray = []; 
$query = "SELECT * FROM dssubs";
if ($result = $conn->query($query)){
    while ($row = $result->fetch_assoc()) {
        $subsArray[$row["id"]] = $row;
        $subsArray[$row["id"]]["datas"] = json_decode($row["datas"], true);
    }
}

$query = "SELECT * FROM ds_asubs";
if ($result = $moneyconn->query($query)){
    while ($row = $result->fetch_assoc()) {
        $datas = json_decode($row["datas"], true);
        if ($datas["paymode"]!="credit"){continue;}

        $today = new DateTime("today"); // This object represents current date/time with time set to midnight
        $match_date = DateTime::createFromFormat( "Y-m-d H:i:s", $row["reg_date"] );
        $match_date->add(date_interval_create_from_date_string($row["validity"].' days'));
        $match_date->setTime( 0, 0, 0 ); // set time part to midnight, in order to prevent partial comparison

        $diff = $today->diff( $match_date );
        $diffDays = (integer)$diff->format( "%R%a" ); // Extract days count in interval

        switch( $diffDays ) {
            case $diffDays <= 0:
                $transactions = new transaction($moneyconn, $row["buyer"]);
                $cost = $subsArray[$datas["subId"]]["datas"]["price"]; $tname = $subsArray[$datas["subId"]]["shortName"]." plan";
                if ($row["status"]!="canceled" AND $transactions->new(- $cost, "Digital Store", $tname)) {
                    $period = $subsArray[$datas["subId"]]["datas"]["period"];
                    $query = "UPDATE ds_asubs SET validity = validity + $period WHERE id = ".$row["id"];
                    echo $query;
                    $moneyconn->query($query);
                }
                else {
                    $query = "DELETE FROM ds_asubs WHERE id = ".$row["id"];
                    echo $query;
                    $moneyconn->query($query);
                }
                break;

            case 3:
                if ($row["status"]=="canceled"){break;}
                $transactions = new transaction($moneyconn, $row["buyer"]);
                if ($transactions->total_credit < $subsArray[$datas["subId"]]["datas"]["price"]){
                    $query = "SELECT email FROM accountsTable WHERE id = ".$row["buyer"];
                    if ($result = $conn->query($query)){
                        while ($gay = $result->fetch_assoc()) {
                            $cusmail = $gay["email"];
                        }
                    }

                    $mail = str_replace("AWESOMECOOL", $subsArray[$datas["subId"]]["name"], $message);
                    mail($cusmail, $subject, $mail, $headers);
                }
                break;
        }
        
    }
}


?>