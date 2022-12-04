<?php

require_once("g/dsEngine.php");
$ds = new dsEngine;
$ds->killCache();
$basketed = $ds->basketed;
$basketed->possibleCountries();
if ($basketed->pureDigit) {
    $ds->go("checkout2");exit();
}

if (!$ds->user->check(true, true)){$ds->go("checkoutw");}
if (count($basketed->inbasket) == 0) {$ds->go("store");}

$conn = $ds->conn;

$ds->fetchAddress();
$autofillA = true; $showAddressImpossible = false;
if (!$ds->address["exists"]){
  $autofillA = false;
}
else if (!isset($basketed->deliverableCountries[$ds->address["country"]])) {
  $autofillA = false;
  $showAddressImpossible = true;
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
            a.remove:hover {
            color:#EA210D !important;
            text-decoration: underline;
        }
    </style>
</head>
<body>
  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl = "true"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="store"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><p class="Bar" style="color:black">Checking out</p></li>
                </ul>
                <?php
                    echo $ds->sideBasket();
                ?>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/15/Digital%20Store" target="_blank">Digital Store documentation</a></li>
                    <li><a class="Bar" href="/docs/6/Accounts" target="_blank">Many Isles accounts</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
              <?php echo $ds->user->signPrompt(); ?>

                <h1>Step 2</h1>

                <div class="contentblock">
                    <h2>Delivery Address</h2>
                    <?php
                    if ($showAddressImpossible){
                      echo "<p><span class='alert'>Note:</span> The basket cannot be shipped to your saved address.</p>";
                    }

                    ?>
                    <form action='makeAddress.php' method="post" style="width:100%" id="SignUpForm">
                        <div class="container">
<?php

if (count($basketed->deliverableCountries) == 0){
    echo "<p>It is impossible to deliver all items to the same address. Consider removing some.<br><br> We recommend removing these items: ";
    $cunter = 0;
    foreach ($basketed->difficultShippingItems as $itemId => $isIt) {
        if ($isIt > 0){
            $query = "SELECT name FROM dsprods WHERE id = $itemId";
            if ($result = $conn->query($query)) {
                while ($row = $result->fetch_assoc()) {
                    $cunter++;
                    if ($cunter != 1) {echo ", ";}
                    echo "<a class='remove' href='trash.php?sender=checkout1.php%3Fwhy%3DdelItem&allItem=".$itemId."' >".$row["name"]."</a>";
                }
            }
        }
    }
    echo ".</p>";
}
else {
  echo '
                           <table>
                                 <label for="fname"><i class="fa fa-user"></i> Full Name</label>
                                    <input type="text" id="fname" name="fullname" placeholder="Hans D. Schleer"'; if ($autofillA) {echo 'value="'.$ds->address["fullname"].'"';} ; echo ' >
                                    <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
                                    <input type="text" id="adr" name="address" placeholder="542 W. 15th Street"'; if ($autofillA) {echo 'value="'.$ds->address["address"].'"';}  echo '>
                                    <label for="city"><i class="fa fa-institution"></i>City, (State)</label>
                                    <input type="text" id="city" name="city" placeholder="New York, NY"'; if ($autofillA) {echo 'value="'.$ds->address["city"].'"';} echo '>
                                    <label for="zip">Zip</label>
                                    <input type="text" id="zip" name="zip" placeholder="10001" '; if ($autofillA) {echo 'value="'.$ds->address["zip"].'"';}  echo '>
                                    <label for="state">Country</label>

';
echo '<select id="state" name="state">';
foreach ($basketed->deliverableCountries as $key => $value) {
    if ($autofillA){if($key == $ds->address["country"]){$selected = "selected";}else {$selected = "";}}
    else if ($key == "US"){$selected = "selected";}
    else {$selected = "";}
    echo "<option value='$key' $selected>$value</option>";
}
echo "</select></table>";

                                if ($basketed->pureDigit == true){
                                    echo "<p>Since you do not have any physical items in your basket, you can skip this step.";
                                }
                                else {
                                    echo "<p>We will send your order by standard postal services.<br>You are responsible for any mis-inputs.</p>";
                                }

}
?>
                            <div style="text-align:center;margin-top:40px;">
                                <span class="step"><div class="hoverinfo">Account <i class="fas fa-check-circle"></i></div></span>
                                <span class="step active"><div class="hoverinfo">Address</div></span>
                                <span class="step"><div class="hoverinfo">Billing</div></span>
                            </div>
                        </div>

                    </form>
                </div>




                <div class="checkoutBox"  onclick="document.getElementById('SignUpForm').submit();">
                    <button class="checkout">
                        <i class="fas fa-arrow-right"></i>
                        <span>Next</span>
                    </button>
                </div>

            </div>
        </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>

<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why =="delItem"){
    let num = urlParams.get('num');
    createPopup("d:dsp;txt:Item removed from basket");
}

    function pop(x) {
        if (x == "ded") {
            document.getElementById("modal").style.display = "none";
            $(".modCol").hide();
        }
        else {
            document.getElementById("modal").style.display = "block";
            document.getElementById(x).style.display = "block";
        }
    }

    function inputGramm(x, y) {
        var input = x.value;
        var patt = new RegExp("[^A-Za-z0-9 ]");
        var target = "uname";
        if (y == "e") { patt = new RegExp("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]$"); target = "email"; }
        else if (y == "p") { patt = new RegExp("[^A-Za-z0-9 ]"); target = "psw"; }
        target = "#".concat(target).concat("InputErr");
        $("input").removeAttr("style");
        if (y != "e") {
            if (patt.test(input) && input.length != 0) { $(target).show(); }
            else { $(target).hide(); }
        }
        else {
            if (!patt.test(input) && input.length != 0) { $(target).show(); }
            else { $(target).hide(); }
        }
    }

    function checkCookie() {
        if (document.cookie.indexOf('loggedIn') == -1) {
            window.location.href = "checkout";
        }
    }
    checkCookie();



    </script>
