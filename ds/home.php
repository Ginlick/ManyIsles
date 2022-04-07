<?php
if (isset($_GET["clear"])){
    if ($_GET["clear"] == "subs"){
        if ( session_status() !== PHP_SESSION_ACTIVE ) {session_start();}
        unset($_SESSION["subbasket"]);
    }
}
require_once("g/dsEngine.php");
$ds = new dsEngine;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Digital Store</title>
    <?php
      echo $ds->giveHead();
     ?>
    <style>
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl = "true"></div>

        <div class="flex-container">
            <div class='left-col'>
                <a href="store"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="/home"><i class="fas fa-arrow-left"></i> Home</a></li>
                </ul>
                <?php
                    echo $ds->sideBasket();
                ?>

                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/18/Digital_Store_Extension" target="_blank">Digital Store FAQ</a></li>
                    <li><a class="Bar" href="/docs/33/Become_Publisher" target="_blank">Publish own items</a></li>
                </ul>
            </div>

            <div id='content' class='column'>

                <h1>Browse</h1>
                <p>Explore the Many Isles digital store</p>


<?php
$latestid = "whelp";
    $query = "SELECT * FROM dsprods";
        if ($toprow = $ds->conn->query($query)) {
        $counter = 0;
            while ($row = $toprow->fetch_assoc()) {
                echo $ds->makeArtTab($row, $ds->basketed->itemNumArray);
            }
        }


?>

            </div>
        </div>

    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>

    <div id="modal" class="modal" onclick="pop('ded')">
    </div>

    <div id="outOfStock" class="modCol">
        <div class="modContent">
            <img src="/Imgs/PopTrade.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
            <h1>Error: Items Out of Stock</h1>
            <p>
                An item in your basket was out of stock and was removed. <br>We are sorry for the inconvenience.
            </p>
            <div class="checkoutBox">
                <button class="checkout" onclick="pop('ded');">
                    <span> ok </span>
                </button>
            </div>
        </div>
    </div>

<form id="basket" action="basket.php" method="POST"  enctype="multipart/form-data" style="display:none;visibility:hidden;">
    <input style="display:none" name="basketing" id="basketing" value="1" />
    <input style="display:none" name="quickBuy" value="1" />
    <input style="display:none" name="goTo" id="goTo" value="home.php" />
</form>

</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why =="itemDeleted"){
    createPopup("d:dsp;txt:Error. This item was deleted.;dur:7000");
}
function showView(element, newimg) {
    element.src = newimg;
}

function purchase(item){
    document.getElementById("basketing").value = item;
    document.getElementById("basket").submit();
}

var urlParams = new URLSearchParams(window.location.search);
var show = urlParams.get('show');

if (show !== null) {
    pop(show);
}
function pop(x) {
    if (x == "ded") {
        document.getElementById("modal").style.display = "none";
        for (let element of document.getElementsByClassName("modCol")) {
            element.setAttribute("style", "display:none;");
        }
    }
    else if (document.getElementById(x) != null) {
        document.getElementById("modal").style.display = "block";
        document.getElementById(x).style.display = "block";
    }
}

</script>
