<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/Server-Side/transactions.php');
require_once("g/dsEngine.php");
$ds = new dsEngine(true);

if (!$ds->user->check(true, true)){$ds->go("checkoutw");}
$conn = $ds->conn; $moneyconn = $ds->moneyconn; $id = $ds->user->user;
$basketed = $ds->basketed;

if ($ds->type == "items"){
    if (isset($_SESSION["basket"])) {
        if ($_SESSION["basket"] == "") {
            header("Location: home.php");exit();
        }
    }
    else {header("Location: home.php");exit();}
}

$custTotal = 0;
$query = "SELECT credit FROM global_credit WHERE id = $id";
if ($result=$moneyconn->query($query)) {
    while ($row = $result -> fetch_assoc()) {
        $custTotal = $row["credit"];
    }
}

$custTran = new transaction($moneyconn, $id);

$stripeTotal = $basketed->totalPrice;
$creditTotal = $basketed->totalPrice;
$basketed->possibleCountries();
if ($basketed->pureDigit) {
    $backURL = "basket";
}
else {
  $backURL = "checkout1";
  if (count($basketed->deliverableCountries) == 0) {$ds->go("checkout1");}
  else if (!isset($basketed->deliverableCountries[$ds->fetchAddress()["country"]])){$ds->go("checkout1");}
}

if ($creditTotal > $custTotal OR $basketed->totalCredit > 0) {$startOn = "stripe";}
else {$startOn = "credit";}

function totalTable($method) {
    global $ds, $stripeTotal, $creditTotal;
    $basketed = $ds->basketed;
    $totalShipping = $ds->shipping();

    $calcTotal = $basketed->totalPrice;
    $table = "<tr><td>Subtotal</td><td>".$ds->makeHuman($calcTotal)."</td></tr>";
    if ($basketed->codesExist AND isset($_COOKIE["ds_codes"])){
        $calcTotal += -$basketed->fullDCodeReduction;
        $table .= "<tr><td>Codes</td><td>-".$ds->makeHuman($basketed->fullDCodeReduction)."</td></tr>";
    }
    if (!$basketed->pureDigit){
        $calcTotal += $totalShipping;
        $table .= "<tr><td>Shipping</td><td>".$ds->makeHuman($totalShipping)."</td></tr>";
    }
    if ($method == "stripe"){
      $stripeTax = $ds->calcStripeTax($calcTotal);
        $calcTotal = $calcTotal + $stripeTax;
        $stripeTax = $ds->makeHuman($stripeTax);
        $table .= "<tr><td>Transfer Fee</td><td>".$stripeTax."</td></tr>";
        $stripeTotal = $calcTotal;
    }
    else {
        $creditTotal = $calcTotal;
    }
    $table .= "<tr><td>Total</td><td>".$ds->makeHuman($calcTotal)."</td></tr>";
    return $table;
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Checkout | Digital Store</title>
    <?php
      echo $ds->giveHead();
     ?>
    <style>
        select {
            width:40%;
        }
.container {
    display:none;
}

        .theTable {
            border-collapse: collapse;
            width: 50%;
            margin: 20px auto;
            text-align: left;
        }

        .theTable tbody > tr > :nth-child(1) {
            width: 80%;
        }

        .theTable tbody > tr > :nth-child(2) {
            width: 20%;
            font-family: 'Montserrat', sans-serif;
            text-align:right;
        }

        .theTable tbody > tr > td {
            padding: 5px 0;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            font-size: calc(14px + 0.3vw);
            text-align: left;
        }
        .theTable tbody > :last-child {
            border-top: 1px solid #ddd;
            font-weight:bold;
        }
.checkoutBox.nopad {
    margin: 9px auto;
}
.checkoutBox.nopad .checkout {
    font-size: min(calc(11px + .4vw), 18px);
    margin: 0 5px;
}
.left-col input {
    margin: 0 auto 5px;
    width: 80%;
    font-size: min(calc(11px + .4vw), 18px);
    padding: 7px 5px;
}
.left-col ul {
    list-style-type: none;
    padding: 0;
    margin-top: 5px;
}
.inputErr {
    text-align: center;
    padding-bottom: 5px;
}
    @media only screen and (max-width: 900px), (max-aspect-ratio:3/4) {
        .left-col input {
            font-size: calc(2px + 2.4vw);
        }
    }
    .address {
      margin-left: 30%;
    }
    </style>
</head>
<body>
  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl = "true"></div>

        <div class="flex-container">
            <div class='left-col'>
                <a href="store"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><p class="Bar" style="color:black"><?php if ($basketed->type == "subs") {echo "Purchasing subscription"; } else {echo "Checking out"; } ?></p></li>
                </ul>
                <?php
                    echo $ds->sideBasket();
                ?>

<?php
if ($basketed->codesExist){
    echo '<img src="/Imgs/Bar2.png" alt="GreyBar" class="separator">
    <h3 class="basketTitle">Use Codes</h3>
    <p>Validate discount codes here.</p>';
    if ($ds->type == "items"){
        if (count($basketed->codeList)==5){
            echo "You already have 5 validated codes.";
            echo '
                    <div class="checkoutBox nopad">
                        <button class="checkout" onclick="executeCode();">
                            <i class="fas fa-times"></i>
                            <span>Remove</span>
                        </button>
                    </div>';
        }
        else {
            echo <<<MASFA
                    <div class="inputCont">
                        <input type="text" id="codeInput" placeholder="22-22-22"  oninput="inputGramm(this, 'code')" />
                    </div>
                    <div class="checkoutBox nopad">
                        <button class="checkout" onclick="executeCode();">
                            <i class="fas fa-times"></i>
                            <span>Remove</span>
                        </button>
                        <button class="checkout" onclick="validateCode();">
                            <i class="fas fa-arrow-right"></i>
                            <span>Validate</span>
                        </button>
                    </div>
        MASFA;
        }
        if (count($basketed->codeList)>0){
            echo "<p style='margin-bottom: 2px;'><b><u>Active Codes</u></b></p><ul>";
            foreach ($basketed->codeList as $element){
                echo "<li>".$element."</li>";
            }
            echo "</ul>";
        }
    }
        else {
            echo <<<MASFA
                    <div class="inputCont">
                        <input type="text" id="codeInput" placeholder="22-22-22"  oninput="inputGramm(this, 'code')" />
                        <p id="codeInputErr" class="inputErr">Incorrect Input!</p>
                    </div>
                    <div class="checkoutBox nopad">
                        <button class="checkout" onclick="validateCode();">
                            <i class="fas fa-arrow-right"></i>
                            <span>Validate</span>
                        </button>
                    </div>
    MASFA;
    }
}



?>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/17/Payments" target="_blank">Payment Methods</a></li>
                    <li><a class="Bar" href="/docs/16/Credit" target="_blank">Many Isles Credit</a></li>
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Data encryption and security</a></li>
                    <li><a class="Bar" href="https://stripe.com" target="_blank">Stripe</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
              <?php echo $ds->user->signPrompt(); ?>

                <h1>Step 3</h1>

                <div class="contentblock">
                    <h2>Billing</h2>

                        <div style="display:flex;justify-content:center;position:relative;">
                            <select name = "method" id ="methodChoose" onchange="switchStuff(this.value);">
                                <option value="credit">Many Isles Credit</option>
                                <option value="stripe">Stripe</option>
                            </select>
                        </div>

                        <div class="container" id="stripe">
                            <table class="theTable">
                                <tbody>
                                    <?php echo totalTable("stripe"); ?>
                                </tbody>
                            </table>

                            <p >
                            <?php
                            if ($stripeTotal < 500){
                                if ($stripeTotal < 150) {
                                    echo "<span class='alert'> Insufficient Amount! </span> Cannot transfer less than $1.50 via stripe. <a href='smolbuffer.php'>buffer</a>";
                                }
                                else {
                                    echo "<span class='alert'> Note: </span> We recommend not transferring less than $5 via stripe. <a href='buffer.php'>buffer</a>";
                                }
                            }
                            echo "<br><br><a href='basket.php'>Edit order</a></p>";
                            ?>
                        </div>
                        <div class="container" id="credit">
                            <table class="theTable">
                                <tbody>
                                    <?php echo totalTable("credit"); ?>
                                </tbody>
                            </table>
                            <p  >
                            <?php
                            if ($basketed->totalCredit > 0){
                                echo "<span class='alert'> Illegal Method! </span> Cannot buy Many Isles Credit using credit.";
                                $creditIsOk = "false";
                            }
                            else if ($creditTotal > $custTotal) {
                                echo "<span class='alert'> Insufficient Credit! </span>";
                                $creditIsOk = "false";
                            }
                            else {
                                $creditIsOk = "true";
                            }
                            echo "<br>Current balance is <b>".$ds->makeHuman($custTotal)."</b>.<br><br><a href='basket.php'>Edit order</a></p>";

                            ?>
                        </div>
                        <?php
                            if ($basketed->type=="subs"){
                                echo "<p>This amount will be charged yearly.</p>";
                            }
                        ?>
                        <?php
                        if (!$basketed->pureDigit) {
                          echo "<h2>Shipping Address</h2>";
                          echo $ds->makeAddressList();
                        }
                         ?>
                        <div style="text-align:center;margin-top:80px;">
                            <span class="step"><div class="hoverinfo">Account <i class="fas fa-check-circle"></i></div></span>
                            <span class="step"><div class="hoverinfo">Address <i class="fas fa-check-circle"></i></div></span>
                            <span class="step active"><div class="hoverinfo">Billing</div></span>
                        </div>


                </div>



                    <div class="checkoutBox spec">
                        <a href="<?php echo $backURL; ?>"><button class="checkout">
                              <i class="fas fa-arrow-right fa-flip-horizontal"></i>
                             <span>Back</span>
                        </button></a>
                        <button class="checkout" id="checkout-button"  style="transition:0.4s" onclick="checkoutNOW();">
                                <i class="fas fa-arrow-right"></i>
                                <span>Next</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="https://js.stripe.com/v3/"></script>
<script src="/Code/CSS/global.js"></script>
<script>
    function checkCookie() {
        if (document.cookie.indexOf('loggedIn') == -1) {
            window.location.href = "checkout.html";
        }
    }
    checkCookie();

    var urlParams = new URLSearchParams(window.location.search);
    var why = urlParams.get('why');

    if (why == "invalidCode") {
        createPopup("d:dsp;txt:Error. Failed to validate code.");
    }
    else if (why == "codeValidated") {
        createPopup("d:dsp;txt:Code validated");
    }
    else if (why == "error") {
        createPopup("d:dsp;txt:Error making payment.");
    }

    var whichShown = "credit";
    function switchStuff(value) {
        document.getElementById(whichShown).style.display = "none";
        document.getElementById(value).style.display = "block";
        whichShown = value;
        document.getElementById("methodChoose").value = value;
        if (value == "stripe") {}
    }
    switchStuff("<?php echo $startOn; ?>");

    function checkoutNOW() {
        if (whichShown == "stripe") {
            doStripe();
        }
        else if (whichShown == "credit") {
           if (<?php echo $creditIsOk; ?>) {window.location.href = "checkoutP.php";}
        }
    }

    <?php require(dirname($_SERVER['DOCUMENT_ROOT'])."/media/keys/stripe-pk.php");
    echo "var stripe = Stripe('$stripe_pk');"; ?>
    function doStripe() {
      if (<?php if ($stripeTotal >= 150){echo "true";} else {echo "false";}?>) {
          document.getElementById("checkout-button").style.backgroundColor = "#BABABA";
          document.getElementById("checkout-button").onclick = "";

            <?php
            if ($basketed->type == "subs"){
                echo "window.location.href  = 'create-checkout-session.php';";
            }
            else {
                echo '
                  fetch("create-checkout-session.php", {
                    method: "POST",
                  })
                    .then(function (response) {
                      return response.json();

                    })
                    .then(function (session) {
                          return stripe.redirectToCheckout({ sessionId: session.id });
                    })
                    .then(function (result) {
                      // If redirectToCheckout fails due to a browser or network
                      // error, you should display the localized error message to your
                      // customer using error.message.
                      if (result.error) {
                        alert(result.error.message);
                      }
                    })
                    .catch(function (error) {
                      console.error("Error:", error);
                    });

                '; }
                ?>
            };
      }

    function inputGramm(x, y) {
        var input = x.value;
        var patt = new RegExp("[^-A-Za-z0-9]");
        var target = "code";
        target = target + "InputErr";
        for (let element of document.getElementsByClassName("input")) {
            element.removeAttribute("style");
        }
        if (patt.test(input) && input.length != 0) { document.getElementById(target).style.display = "block"; document.getElementById(target).innerHTML = "Incorrect Input!"; }
        else { document.getElementById(target).style.display = "none"; }
    }

    function validateCode() {
        code = document.getElementById("codeInput").value;
        window.location.href = "g/validateCode.php?code="+code;
    }
    function executeCode() {
        document.cookie = "ds_codes =; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "checkout2.php";
    }

</script>
