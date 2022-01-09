<?php

require_once("g/sideBasket.php");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Tiers | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="g/ds-g.css">
    <style>
.tierCont {
    text-align:center;
    display:flex;
    padding: calc(10px + 1vw) 0;
}
.tierblock {
    width:28%;
    min-height:100%;
    border:1px solid #c2bea9;
    margin: 0 2.66%;
    box-shadow: 0 4px 22px 0 rgba(0, 0, 0, 0.2), 0 6px 22px 0 rgba(0, 0, 0, 0.19);
    border-radius:5px;
    text-align:left;
    display:inline-block;
}
.tierblock:hover {
    box-shadow: 0 4px 50px 0 rgba(0, 0, 0, 0.4), 0 6px 22px 0 rgba(0, 0, 0, 0.25);
    cursor:pointer;
}

        .tierblock h2 {
            margin: 10px 0 0;
            font-size: 2vw;
        }
        .tierblock .img {
            width: 40%;margin:auto;
        }
        .img img {
            width:100%;
        }
        .tierblock p {
            margin: 5px 0 10px;
            font-size: min(1vw, 15px);
        }
        .tierblock ul {
            padding-left:3.5vw;
        }
        .tierblock li {
            padding: 5px 0;
            font-size: min(calc(1.29vw + 2px), 18px);
            text-align: left;
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
                    <li><a class="Bar" href="/docs/10/Ranking" target="_blank">Many Isles tiers</a></li>
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div id='content' class='column'>

                <h1>Purchase Tier</h1>
                <p>click to select</p>
            <div class="tierCont">
                <div class="tierblock" id="0" onclick="selectTier(0);">
                    <h2>Tier 1</h2>
                    <p class="titlename"><i>Imperial Soldier</i></p>
                    <div class="img"><img src="/Imgs/Ranks/single/ImperialSoldier.png"/></div>
                    <h2>$2</h2>
                    <ul>
                        <li>Tier 1 access to the <a href="/dl/Goods.php" target="_blank">digital library</a></li>
                        <li>Imperial Soldier title</li>
                        <li>Access to the <b>#shattered-sun</b> Discord channel</li>
                        <li>Support the Many Isles! :)</li>

                    </ul>
                </div>
                <div class="tierblock" id="1" onclick="selectTier(1);">
                    <h2>Tier 2</h2>
                    <p class="titlename"><i>Grand Wizard</i></p>
                    <div class="img"><img src="/Imgs/Ranks/single/GrandWizard.png"/></div>
                    <h2>$5</h2>
                    <ul>
                        <li>Tier 2 access to the <a href="/dl/Goods.php" target="_blank">digital library</a></li>
                        <li>Grand Wizard title</li>
                        <li>Support the Many Isles! :)</li>
                    </ul>
                </div>
                <div class="tierblock" id="2" onclick="selectTier(2);">
                    <h2>Tier 3</h2>
                    <p class="titlename"><i>Legendar</i></p>
                    <div class="img" style="width:50%;"><img src="/Imgs/Ranks/single/Legendar.png"/></div>
                    <h2>$10</h2>
                    <ul>
                        <li>Tier 3 access to the <a href="/dl/Goods.php" target="_blank">digital library</a></li>
                        <li>Legendar title</li>
                        <li>Easy access to the Pantheon</li>
                        <li>Access to the <b>#awake-lion</b> Discord channel</li>
                        <li>Be an excellent supporter! :)</li>
                    </ul>
                </div>
            </div>
            <div>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
        </div>
                <form id="basket" action="basket.php" method="POST"  enctype="multipart/form-data">
                    <input style="display:none" name="basketing" id="basketing" value="1" />
                    <input style="display:none" name="orderDetails" id="orderDetails" value="[price:1]" />
                    <input style="display:none" name="goTo" id="goTo" value="nope" />
                    <div class="checkoutBox spec">
                        <button class="checkout" onclick="submitSpecial(1);">
                              <i class="fas fa-shopping-basket"></i>
                             <span>Basket</span>
                        </button>
                        <button class="checkout" onclick="submitSpecial(0);">
                              <i class="fas fa-arrow-right"></i>
                             <span>Basket</span>
                        </button>
                    </div>
                </form>


                <p style="margin-top:50px;">Tiers give your account certain benefits. A purchase is permanent, no subscription required. For more information, check out this <a href="/wiki/h/diglib/tiers.html" target="_blank">wiki article</a>.</p>
            </div>

        </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>

</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var sel = parseInt(urlParams.get('i'));
var selectedTier = 0;
if (sel.typeof == "int" && 0 < sel < 3){
    selectedTier = sel;
}


    function selectTier(x) {
        document.getElementById(0).removeAttribute("style");
        document.getElementById(1).removeAttribute("style");
        document.getElementById(2).removeAttribute("style");
        document.getElementById(x).style.border = "3px solid #f0c026";
        document.getElementById(x).style.boxShadow = "0 4px 50px 0 rgba(0, 0, 0, 0.4), 0 6px 22px 0 rgba(0, 0, 0, 0.25)";
        selectedTier = x;
    }
    selectTier(selectedTier);


function submitSpecial(x) {
    if (x==1){
        document.getElementById("goTo").value = "tiers.php";
    }
    let subArray = [];
    subArray.push("tier:" + selectedTier);
    document.getElementById("orderDetails").value = subArray.join();

    document.getElementById("basket").submit();
}
</script>
