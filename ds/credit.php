<?php

require_once("g/sideBasket.php");

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
        <div class="flex-container">
            <div class='left-col'>
                <a href="home.php"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="home.php">Browse</a></li>
                </ul>
                <?php
                    doSideBasket();
                ?>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/16/Credit" target="_blank">Many Isles Credit</a></li>
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div class='column'>

                <div class="contentblock">
                    <h2>Many Isles Credit</h2>
                    <p>Your Credit will be available for all transactions within the Many Isles, free of any charge. <span id="infoSpan">Note that a fee of 2.9% + .32$ applies to payments through stripe.</span></p>
                    <form id="SignUpForm" action="basket.php" method="POST" enctype="multipart/form-data">

                        <div class="container">
                            <label for="creditAmount"><i class="fas fa-coins"></i> Credit Amount ($)</label>
                            <input type="text" id="creditAmount" name="creditAmount" placeholder="10.50" oninput="inputGramm(this)" required>
                            <p id="InputErr" class="inputErr">Insufficient amount!</p>
                        </div>




                        <div>
                            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                        </div>
                        <input style="display:none" name="basketing" id="basketing" value="2" />
                        <input style="display:none" name="goTo" id="goTo" value="nope" />
                        <div class="checkoutBox spec">
                            <button class="checkout" onclick="submitSpecial();">
                                <i class="fas fa-shopping-basket"></i>
                                <span>Basket</span>
                            </button>
                            <button class="checkout" onclick="submitNormal();">
                                <i class="fas fa-arrow-right"></i>
                                <span>Basket</span>
                            </button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>

    function inputGramm(x) {
        var input = x.value;
        $("#InputErr").hide();
        if (input < 5) {
            $("#infoSpan").show();
            $("#InputErr").show();
        }
    }

function submitSpecial() {
    document.getElementById("goTo").value = "credit.php";
    submitNormal();
}
function submitNormal() {
    if (document.getElementById("creditAmount").value != ""){
        let sponsoring = parseFloat(document.getElementById("creditAmount").value);
        sponsoring = sponsoring * 100.0;
        sponsoring = sponsoring.toFixed(0);
        document.getElementById("creditAmount").value = sponsoring;
        document.getElementById("basket").submit();
    }
}
</script>


