<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/ds/g/dsEngine.php');
$ds = new dsEngine;
if (!$ds->user->check(true)){
  $ds->go("store");
}

session_destroy();

    $phrase = "You should receive your purchase by postal services within a few weeks. It may take a moment for any digital products you purchased to take effect.<br>
                                You will receive a receipt by mail.<br><br>
                                You can always view your order from your <a href='/account/home?display=orders'>account page</a>.";
if (isset($_GET["type"])){
    if ($_GET["type"] == "subs"){
        $phrase = "Your subscription was created. It may take a moment to take effect.<br><br>You can view and edit your active plans from your <a href='/ds/subs/hub'>hub</a>.";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout Completed | Digital Store</title>
    <?php echo $ds->giveHead(); ?>
    <style>

    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;"></div>

        <div class="flex-container">
            <div class='left-col'>
                <a href="home.php"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><p class="Bar" style="color:black">Checking out</p></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                </ul>
            </div>

            <div id='content' class='column'>

                <h1>Payment Completed</h1>

                <div class="contentblock">
                    <h2>Thank you for your order</h2>

                    <form action='makeAddress.php' method="post" style="width:100%" id="SignUpForm">
                        <div class="container">
                            <p><?php echo $phrase; ?>
                            </p>
                        </div>

                    </form>
                </div>




                <a href="store"><div class="checkoutBox">
                    <button class="checkout">
                        <i class="fas fa-arrow-right"></i>
                        <span>Home</span>
                    </button>
                </div></a>

            </div>
        </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>

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
