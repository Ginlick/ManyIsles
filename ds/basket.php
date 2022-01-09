<?php

require_once("g/sideBasket.php");
$includeDCodes = true;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Basket | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="g/ds-tables.css">
    <style>
            .credTable.orders {
                width: 80%;
                font-size: min(calc(12px + .2vw), 15px);
            }
        .credTable.orders ul li {
                            font-size: min(calc(12px + .2vw), 15px);

        }
            .credTable tbody > tr > :nth-child(1) {
                width: 15%;
            }

            .credTable tbody > tr > :nth-child(2) {
                width: 20%;
            }
            .credTable tbody > tr > :nth-child(3) {
                width: 20%;
            }
            .credTable tbody > tr > :nth-child(4) {
                width: 10%;
            }
            .credTable tbody > tr > :nth-child(5) {
                width: 10%;
                font-weight:bold;
                color: #f0c026;
            }
            td.remove a:hover {
            color:#EA210D !important;
            text-decoration: underline;
        }

    </style>
</head>
<body>
  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl = "true"></div>

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
                    <li><a class="Bar" href="/wiki/h/digsto.html" target="_blank">Digital Store FAQ</a></li>
                    <li><a class="Bar" href="/wiki/h/digsto/codes.html" target="_blank">Digital Store discount codes</a></li>
                    <li><a class="Bar" href="/wiki/h/digsto/pportal.html" target="_blank">Your money in the Many Isles</a></li>
                </ul>
            </div>

            <div id='content' class='column'>

                <h1>Basket</h1>

                <table class="credTable orders">
                    <tbody id="theTableBody">


<?php
    if ($basketed->type == "subs"){
        foreach ($basketed->itemArray as $itemDetails) {
            echo "<tr>";
            echo '<td>'.$itemDetails["name"].' subscription</a></td>';
            echo '<td>'.makeHuman($itemDetails["price"]).' / year</td>';
            echo "</tr>";
        }
    }
    else {
        $cunter = 1;
        foreach ($basketed->itemArray as $itemDetails) {
            $articleName = $itemDetails["row"]["name"];
            if ($itemDetails["row"]["id"]==3){$articleName = "Support ".$itemDetails["addName"];}
            else if ($itemDetails["row"]["id"]==1){$articleName = $itemDetails["addName"]." Tier";}
            $link = linki($itemDetails["row"]["id"],$itemDetails["row"]["link"],$itemDetails["row"]["shortname"]);

            $fullUL = detailsUL($itemDetails["prodSpecs"], $itemDetails["codeReducs"]);

            echo "<tr>";
            echo '<td><img src="'.clearImgUrl($itemDetails["row"]["thumbnail"]).'" alt="thumbnail" /></td>';
            echo '<td><a href="'.$link.'">'.$articleName.'</a></td>';
            echo '<td>'.$fullUL.'</td>';
            echo '<td>'.makeHuman($itemDetails["price"]).'</td>';
            echo '<td class="remove"><a href="trash.php?which='.$cunter.'">Remove</a>';
            echo "</tr>";
            $cunter++;
        }
    }
?>

                    </tbody>
                </table>
            <?php if ($basketed->prodNum != 0){
                echo '              <div class="checkoutBox">';
                if ($basketed->type == "subs"){
                    echo '
                    <button class="checkout" onclick="window.location.href = \'store?clear=subs\';">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>';
                }

                echo '   <a href="checkout1.php">
                    <button class="checkout">
                        <i class="fas fa-arrow-right"></i>
                        <span>Checkout</span>
                    </button>
                    </a>
                    </div> ';
                }
            else {echo "<p><a href='home.php'>Browse</a> the digital store</p>";}
            ?>

            </div>
        </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>

</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>

var urlParams = new URLSearchParams(window.location.search);
var show = urlParams.get('show');
if (show =="outOfStock"){
    createPopup("d:dsp;txt:Error. Some items in your basket were out of stock.");
}
</script>
