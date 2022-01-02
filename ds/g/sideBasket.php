<?php
if ( session_status() !== PHP_SESSION_ACTIVE ) {session_start();}

if (isset($_POST["creditAmount"])) {if (preg_match("/[0-9]{1,}$/", $_POST['creditAmount'])!=1){header("Location: home.php");echo"nup";session_destroy();exit();} }
if (isset($_POST["supportPair"])) {if (preg_match("/[(]+[a-zA-Z0-9' ]+\/[0-9]+[)]$/", $_POST['supportPair'])!=1){header("Location: home.php?2=".$_POST['supportPair']);echo"nup";session_destroy();exit();} }
if (isset($_POST["orderDetails"])) {if (preg_match("/[^a-zA-Z0-9:', ]/", $_POST['orderDetails'])==1){header("Location: home.php");echo"nup";session_destroy();exit();} }
if (isset($_POST["specified"])) {if (preg_match("/[0-9]{1}$/", $_POST['specified'])!=1){header("Location: home.php");echo"nup";session_destroy();exit();} }

if (!isset($conn)){
    require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
}
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

if (!isset($_SESSION["basket"])) {$_SESSION["basket"]="";}
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/makeHuman.php");
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/loopBasket.php");

$inbasket = explode(",", $_SESSION["basket"]);
if (isset($_POST["basketing"]) AND count($inbasket) < 11) {
    if (preg_match("/[^0-9]/", $_POST['basketing'])===1){header("Location: /ds/store?w=3");session_destroy();exit();}

    if (isset($_POST["quickBuy"])) {
        if ($_POST["basketing"] == 2){array_push($inbasket, "2-1000");}
        else if ($_POST["basketing"] == 3){array_push($inbasket, "3(the Pantheon/500)");}
        else {array_push($inbasket, $_POST["basketing"]."[]");}
    }
    else if (isset($_POST["orderDetails"])){
        $insertTo = str_replace(",", "-", $_POST['orderDetails']);
        $topush = $_POST["basketing"]."[".$insertTo."]";
        array_push($inbasket, $topush);
    }
    else if (isset($_POST["supportPair"])) {
        $topush = $_POST["basketing"].$_POST["supportPair"];
        array_push($inbasket, $topush);
    }
    else if (!isset($_POST["specified"]) AND !isset($_POST["creditAmount"])){
        array_push($inbasket, $_POST["basketing"]);
    }
    else if (isset($_POST["creditAmount"])) {
        if ($_POST["creditAmount"]>10000){$creditAmount = 10000;}else {$creditAmount = $_POST["creditAmount"];}
        $topush = $_POST["basketing"]."-".$creditAmount;
        array_push($inbasket, $topush);
    }
    $_SESSION["basket"] = implode(",", $inbasket);
    unset($_SESSION["subbasket"]);
}

if (isset($_POST["goTo"])) {
    if ($_POST["goTo"] != "nope" AND $_POST["goTo"] != "" ){
        header("Location:".$_POST["goTo"]);exit();
    }
}

if (!isset($codesMatter)){$codesMatter = false;}
$type = "items";
if (isset($_SESSION["subbasket"])) {$inbasket = $_SESSION["subbasket"];$type="subs";}
$basketed = new loopBasket($conn, $inbasket, true, false, $codesMatter, $type);
function doSideBasket() {
    global $conn, $moneyconn, $basketed;

    if (isset($_COOKIE["loggedIn"]) AND str_contains($_SERVER['REQUEST_URI'], "/store")){
        $query = "SELECT ud FROM dsorders WHERE buyer = ".$_COOKIE["loggedIn"];
        if ($result = $conn->query($query)) {
            if (mysqli_num_rows($result) != 0) {
                echo "<ul class='myMenu'><li><a class='Bar' href='/account/SignedIn.php?display=orders' target='_blank'>My Orders</a></li></ul>";
            }
        }
        $query = "SELECT id FROM ds_asubs WHERE buyer = ".$_COOKIE["loggedIn"];
        if ($result = $moneyconn->query($query)) {
            if (mysqli_num_rows($result) != 0) {
                echo "<ul class='myMenu'><li><a class='Bar' href='subs/hub'>My Subscriptions</a></li></ul>";
            }
        }
    }

    if (($_SESSION["basket"] != "" AND $basketed->type == "items") OR (isset($_SESSION["subbasket"]) AND $_SESSION["subbasket"] != "" AND $basketed->type == "subs")){

        echo '
        <div class="toBeHidden">
        <img src="/Imgs/Bar2.png" alt="GreyBar" class="separator">
        <a href="/ds/basket.php"><h3 class="basketTitle">Basket</h3></a>
        <table class="basketTable">
            <tbody>';

        if ($basketed->type == "subs"){
            foreach ($basketed->itemArray as $item) {
                echo "<tr>";
                echo '<td><img src="'.$item["row"]["image"].'" alt="thumbnail" /></td>';
                echo '<td>'.$item["row"]["shortName"].'</td>';
                echo '<td>'.makeHuman($item["price"]).'</td>';
                echo "</tr>";
            }
        }
        else {
            foreach ($basketed->itemArray as $item) {
                $name = $item["row"]["name"];
                if ($item["row"]["shortname"] != null) {$name = $item["row"]["shortname"];}
                $link = linki($item["row"]["id"], $item["row"]["link"], $item["row"]["name"]);
                echo "<tr>";
                echo '<td><img src="'.clearImgUrl($item["row"]["thumbnail"]).'" alt="thumbnail" /></td>';
                echo '<td><a href="'.$link.'">'.$name.'</a></td>';
                echo '<td>'.makeHuman($item["price"]).'</td>';
                echo "</tr>";
            }
        }


        echo '
                <tr>
                    <td></td>
                    <td><b>Subtotal</b></td>
                    <td><b>'.makeHuman($basketed->totalPrice).'</b></td>
                </tr>';
        echo '
            </tbody>
        </table>';

        if (strpos($_SERVER['REQUEST_URI'], "checkout")===false AND strpos($_SERVER['REQUEST_URI'], "basket")===false) {
               echo '         <div class="checkoutBox" style="margin:2vw auto">
                            <a href="/ds/checkout1.php">
                            <button class="checkout">
                                <i class="fas fa-arrow-right"></i>
                                <span>Checkout</span>
                            </button>
                            </a>
                        </div>
                        </div>';
            //mobile
            $bottomad = '
                <div class="bottomad-container">
                    <a href="/ds/basket.php">
                    <div class="bottomad">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    </a>
                </div>';

            if ($basketed->prodNum > 0) {
                $bottomad = str_replace("</i>", '</i><div class="bottomadProdnum">'.$basketed->prodNum.'</div>', $bottomad);
            }
            echo $bottomad;
        }
        else {
            echo "</div>";
        }
    }
}

?>
