<?php
if(isset($_GET["w"]) && $_GET["w"]=="1") {$newAcc = true;} else {$newAcc = false;}
require_once("g/dsEngine.php");
$ds = new dsEngine;
$ds->killCache();

if (!$ds->user->check(true)){header("Location: checkout");exit();}
else if ($ds->user->emailConfirmed){header("Location: checkout1");exit();}
$conn = $ds->conn;

?>


<!DOCTYPE html>
<html>
<head>
    <title>Checkout | Digital Store</title>
    <?php echo $ds->giveHead(); ?>
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
                <?php
                    echo $ds->sideBasket();
                ?>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/8/Confirming_an_Account" target="_blank">Confirming an account</a></li>
                    <li><a class="Bar" href="/docs/6/Accounts" target="_blank">Many Isles accounts</a></li>
                    <li><a class="Bar" href="/docs/44/Terms_of_Service" target="_blank">Terms of Service</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
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
                                If necessary, resend a confirmation email from your <a href='/account/home?show=notConfirmed' target='_blank'>account page</a>.";
                            }
                            else {
                                echo "You need to confirm your email to continue.<br>
                                If necessary, resend a confirmation email from your <a href='/account/home?show=notConfirmed' target='_blank'>account page</a>.<br><br>
                                Once your email is confirmed, you can click <em>Reload</em> here.";
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
