<?php

if(isset($_GET["w"])) {if ($_GET["w"]!="1"){header("Location: basket.html");exit();} else {$newAcc = true;} } else {$newAcc = false;}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer;
if (!$user->check(true)){header("Location: checkout");exit();}
else if ($user->emailConfirmed){header("Location: checkout1");exit();}
$conn = $user->conn;

session_start();
if (!isset($_SESSION["subbasket"])) {
    if (isset($_SESSION["basket"])) {
        if ($_SESSION["basket"] == "") {
            echo "<script>window.location.replace('/ds/store');</script>";exit();
        }
    }
    else {
        echo "<script>window.location.replace('/ds/store');</script>";exit();
    }
}


?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Checkout | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/ds-g.css">
    <style>
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;"></div>
    <div style="flex: 1 0 auto;">

        <div class="flex-container">
            <div class='left-col'>
                <a href="store"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><p class="Bar" style="color:black">Checking out</p></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/8/Confirming_an_Account" target="_blank">Confirming an account</a></li>
                    <li><a class="Bar" href="/docs/6/Accounts" target="_blank">Many Isles accounts</a></li>
                    <li><a class="Bar" href="/account/Account?display=Pol" target="_blank">Many Isles account policy</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
              <?php echo $user->signPrompt(); ?>

                <h1>Step 0</h1>

                <div class="contentblock" id="makeIt">
                    <h2>Confirm Email</h2>


                    <form action='makeAccount.php' method="post" style="width:100%" id="SignUpForm">
                        <div class="container">
                            <img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:auto;padding: 0 0 2vw;' class='separator'>
                            <p>
                            <?php if ($newAcc){
                                echo "We're waiting for your email to be confirmed. Check your spam if it isn't in your inbox.<br />
                                You can continue by clicking <em>Reload</em> here once your email is confirmed.<br><br>
                                Resend a confirmation email from your <a href='/account/SignedIn.php?show=notConfirmed' target='_blank'>account page</a>.";
                            }
                            else {
                                echo "You need to confirm your email to continue.<br>
                                Resend a confirmation email from your <a href='/account/SignedIn.php?show=notConfirmed' target='_blank'>account page</a>.<br>
                                If you have confirmed it, you can click <em>Reload</em> here.";
                            }
                            ?>
                            </p>
                            <div style="text-align:center;margin-top:40px;">
                                <span class="step active"><div class="hoverinfo">Account <i class="fas fa-check-circle"></i></div></span>
                                <span class="step"><div class="hoverinfo">Address</div></span>
                                <span class="step"><div class="hoverinfo">Billing</div></span>
                            </div>
                        </div>
                    </form>

                </div>
                    <div class="checkoutBox" onclick="location.reload();">
                        <button class="checkout" type="submit">
                            <i class="fas fa-redo"></i>
                            <span>Reload</span>
                        </button>
                    </div>
            </div>





        </div>
    </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>

</body>
</html>
<script src="/Code/CSS/global.js"></script>
