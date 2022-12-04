<?php
if (isset($_COOKIE["admin"])){$admin = true;}else {$admin = false;}
$redirect = "/account/BePartner";
require_once("security.php");

require_once("../g/ordStatus.php");
require_once("../g/makeHuman.php");
require_once("../g/alertStock.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Partnership Hub | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
    <style>

    h2 {
        padding:80px 0 10px;
        border-bottom: 1px solid #ddd;
    }
    .credTable.prods tbody > tr > :nth-child(3) {
        width: 10%;
    }
    .credTable.prods tbody > tr > :nth-child(4) {
        width: 10%;
    }
    .credTable.prods tbody > tr > :nth-child(5) {
        width: 15%;
    }
    .credTable tr > :last-child {
        min-width: 90px;
    }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="../home.php"><h1 class="menutitle">Partnership</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="/account/Publish.php"><i class="fas fa-arrow-left"></i> Partnership Overview</a></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="../home.php">Browse Store</a></li>
                    <li><a class="Bar" href="/account/home">Account</a></li>
                    <li><a class="Bar" href="partner.php?id=<?php echo $pId; ?>"></i> View Public Page</a></li>
                    <li><a class="Bar" href="/docs/18/Digital_Store_Extension" target="_blank">DS Publishing</a></li>
                    <li><a class="Bar" href="/docs/19/Publishing_Obligations">DS Publishing Conditions</a></li>
                    <li><a class="Bar" href="/docs/4/Partnerships" target="_blank">Partnerships</a></li>
                    <li><a class="Bar" href="countryArrays.php">Country Codes</a></li>
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
                <h1>Digital Store Hub</h1>
                <div class='dsBanner'><img src='/Imgs/Ranks/HighMerchant.png' alt:'Oopsie!'></div>
                <?php if ($admin) {echo "<p>Viewing as admin. <a href='killAdmin.php'>stop</a></p>";} ?>
                <div class="checkoutBox" style="margin-bottom:0;">
                    <a href="settings.php"><button class="checkout" type="submit">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </button></a>
                    <button class="checkout" type="submit" onclick="window.location='hub.php'">
                        <i class="fas fa-redo"></i>
                        <span>Reload</span>
                    </button>
                </div>
                <?php
                    $query = "SELECT * FROM dsorders WHERE seller = $pId  AND status = 0 ORDER BY ud DESC";
                    if ($admin){$query = "SELECT * FROM dsorders WHERE status = 0 ORDER BY ud DESC";}
                    if ($toprow = $conn->query($query)) {
                        if (mysqli_num_rows($toprow) != 0) {
                            echo "<h2 id='hPendingO'>Pending Orders</h2><table class='credTable orders'><thead><tr><td>Order Id</td><td>Items</td><td>Paid</td><td>Shipping</td><td>Date</td><td>Status</td><td></td></tr></thead><tbody>";

                            while ($row = $toprow->fetch_assoc()) {
                                $ordUd = $row["ud"];
                                $ordClid = $row["orderId"];
                                $ordBuyer = $row["buyer"];
                                $ordPaid = $row["paid"];
                                $ordShipping = $row["shipping"];
                                $ordItems = $row["items"];
                                $ordXStatus = $row["status"];
                                $ordRegdate = $row["reg_date"];

                                $date_array = date_parse($ordRegdate);
                                $ordPubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"]." ".$date_array["hour"].":".$date_array["minute"];

                                echo "<tr>";
                                echo '<td>#'.$ordClid."-".$ordUd.'</td>';
                                echo '<td>'.$ordItems.'</td>';
                                echo '<td>'.makeHuman($ordPaid).'</td>';
                                echo '<td>'.makeHuman($ordShipping).'</td>';
                                echo '<td>'.$ordPubdate.'</td>';
                                echo '<td>'.ordStatus($ordXStatus, 1).'</td>';
                                echo '<td><a href="order.php?id='.$ordClid.'"><button class="checkout homescreen"><i class="fas fa-arrow-right"></i> More</button></a></td>';
                                echo "</tr>";

                            }
                            echo "</tbody></table>";
                        }
                    }

                ?>
                <h2 id="hPublications">Publications</h2>
                    <?php
                        $query = 'SELECT * FROM dsprods WHERE sellerId = "'.$pId.'"';
                        if ($admin){$query = "SELECT * FROM dsprods";}
                        if ($toprow = $conn->query($query)) {
                            if (mysqli_num_rows($toprow) != 0) {
                                echo "<table class='credTable prods'><thead><tr><td></td><td></td><td>Stock</td><td>Status</td><td></td></tr></thead><tbody>";

                                while ($row = $toprow->fetch_assoc()) {
                                    if ($row["status"]=="deleted"){continue;}
                                    $articleName = $row["name"];
                                    $articleId = $row["id"];
                                    if ($row["link"] != null){$link = $row["link"];}else {$link = $row["id"]."/".str_replace(" ", "_", $row["name"]);}

                                    echo "<tr>";
                                    echo '<td><img src="'.clearImgUrl($row["thumbnail"]).'" alt="thumbnail" /></td>';
                                    echo '<td><a href="../'.$link.'" target="_blank">'.$articleName.'</a></td>';
                                    echo '<td>'.alertStock(hasAnyStock($row["specifications"], $row["stock"])).'</td>';
                                    echo '<td>'.prodStatSpan($row["status"]).'</td>';
                                    echo '<td><a href="item.php?id='.$articleId.'"><button class="checkout homescreen"><i class="fas fa-arrow-right"></i> Edit</button></a></td>';
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";
                            }
                            else {
                                echo "<p>No published items yet.</p>";
                            }
                        }

                    ?>

                <div class="checkoutCont" style="padding:30px 0 0">
                    <a href="item.php"><button class="checkout">
                        <i class="fas fa-plus"></i>
                        <span>Publish New</span>
                    </button></a>
                </div>

                <h2 id="hProcessedO">Processed Orders</h2>

                <?php
                    $query = "SELECT * FROM dsorders WHERE seller = $pId  AND status != 0 ORDER BY ud DESC";
                    if ($admin){$query = "SELECT * FROM dsorders WHERE status != 0 ORDER BY ud DESC";}
                    if ($toprow = $conn->query($query)) {
                        if (mysqli_num_rows($toprow) != 0) {
                            echo "<table class='credTable orders'><thead><tr><td>Order Id</td><td>Items</td><td>Paid</td><td>Shipping</td><td>Date</td><td>Status</td><td></td></tr></thead><tbody>";

                            while ($row = $toprow->fetch_assoc()) {
                                $ordUd = $row["ud"];
                                $ordClid = $row["orderId"];
                                $ordBuyer = $row["buyer"];
                                $ordPaid = $row["paid"];
                                $ordShipping = $row["shipping"];
                                $ordItems = $row["items"];
                                $ordXStatus = $row["status"];
                                $ordRegdate = $row["reg_date"];

                                $date_array = date_parse($ordRegdate);
                                $ordPubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"]." ".$date_array["hour"].":".$date_array["minute"];

                                echo "<tr>";
                                echo '<td>#'.$ordClid."-".$ordUd.'</td>';
                                echo '<td>'.$ordItems.'</td>';
                                echo '<td>'.makeHuman($ordPaid).'</td>';
                                echo '<td>'.makeHuman($ordShipping).'</td>';
                                echo '<td>'.$ordPubdate.'</td>';
                                echo '<td>'.ordStatus($ordXStatus, 1).'</td>';
                                echo '<td><a href="order.php?id='.$ordClid.'"><button class="checkout homescreen"><i class="fas fa-arrow-right"></i> More</button></a></td>';
                                echo "</tr>";

                            }
                            echo "</tbody></table>";
                        }
                        else {
                            echo "<p>No processed orders yet.</p>";
                        }
                    }

                ?>

            </div>
        </div>
    </div>


    <div w3-include-html="../g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why =="delItem"){
    let num = urlParams.get('num');
    createPopup("d:dsp;dur:22000;txt:Item Deleted;b:1;bTxt:Undo;bHref:restoreItem.php?id="+num);
}
else if (why =="activated"){
    createPopup("d:dsp;txt:Digital Store extension activated!");
}
</script>
