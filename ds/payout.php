<?php


if(!isset($_COOKIE["loggedIn"])) {header("Location: home.php");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: home.php");setcookie("loggedIn", "", time() -3600, "/");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

$id = $_COOKIE["loggedIn"];
$query = "SELECT * FROM accountsTable WHERE id = ".$id;
$result = $conn->query($query);
while ($row = $result -> fetch_assoc()) {
    $checkpsw = $row["password"];
}
$redirect = "Location: Account.html?error=notSignedIn";
include("../Server-Side/checkPsw.php");


$query = "SELECT credit FROM global_credit WHERE id = $id";
if ($result=$moneyconn->query($query)) {
    while ($row = $result -> fetch_assoc()) {
        $custTotal = $row["credit"];
    }
}
if ($custTotal == 0 OR $custTotal == "") {
    header("Location: credit.php");exit();
}

function makeHuman($ordiprice) {
    $price = substr($ordiprice, 0, -2) . "." . substr($ordiprice, -2);
    $price = str_replace(".00", "", $price);
    $price = str_replace(" ", "", $price);
    return $price;
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Credit | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="g/ds-g.css">
    <style>
        .inputErr {
            font-size: calc(9px + .3vw);
            color: red;
            text-align: left;
            margin: 0;
            padding-left: .4vw;
            display: none;
        }
        #infoSpan {
            display:none;
        }
        .container {
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;"></div>
    <div style="flex: 1 0 auto;">

        <div class="flex-container">
            <div class='left-col'>
                <a href="home.php"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="home.php">Browse</a></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/wiki/h/digsto/credit.html" target="_blank">Many Isles Credit</a></li>
                    <li><a class="Bar" href="https://stripe.com" target="_blank">Payment portal information</a></li>
                    <li><a class="Bar" href="/wiki/h/digsto.html" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div class='column'>

                <div class="contentblock">
                    <h2>Credit Payout</h2>
                    <p>Withdraw money from your Many Isles Credit. <span id="infoSpan">Note that a fee of 2.9% + .32$ applies to payments through stripe.</span></p>
                    <form id="SignUpForm" >

                        <div class="container">
                            <label for="creditAmount"><i class="fas fa-coins"></i> Payout Amount ($)</label>
                            <input type="text" id="creditAmount" name="creditAmount" placeholder="10.50" pattern="[0-9]+\.[0-9]{2}$" oninput="inputGramm(this)" required>
                            <p id="InputErr" class="inputErr">Illegal Input!</p>
                        </div>

                        <p>You currently have <b>$<?php echo makeHuman($custTotal); ?></b>. <span id="howMuchInfo"></span></p>


                        <div>
                            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                        </div>
                        <div class="checkoutBox spec">
                            <button class="checkout" onclick="submitNormal();">
                                <i class="fas fa-arrow-right"></i>
                                <span>Payout</span>
                            </button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>


</body>
</html>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script>
    if (document.cookie.indexOf('loggedIn') == -1) {
        window.location.href = "checkout.html";
    }
    else if (document.cookie.indexOf('loggedP') == -1) {
        window.location.href = "checkout.html";
    }

    var custTotal = parseFloat(<?php echo $custTotal; ?>) / 100;

    function inputGramm(x) {
        var input = x.value;
        let value = parseFloat(input);
        $("#InputErr").hide();
        $("#howMuchInfo").html("");
        if (isNaN(value)){
            $("#InputErr").show();           
            $("#InputErr").html("Incorrect Format!");           
        }
        else if (value < 5) {
            $("#infoSpan").show();
            $("#InputErr").show();
            $("#InputErr").html("Insufficient Amount!");           
        }
        else if (value > custTotal) {
            $("#InputErr").show();
            $("#InputErr").html("Too Large Amount!");
        }
        else {
            let inEffect =  value *0.971;
            inEffect = inEffect - 0.32;
            $("#howMuchInfo").html("You will receive <b>$"+inEffect.toFixed(2)+"</b>.");
        }
    }

</script>


