<?php
if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:hub.php");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/dsEngine.php");
$ds = new dsEngine;
$redirect = "../home.php";
require_once("security.php");

$clid = $_GET["id"];
$countries = $ds->countries;

$query = 'SELECT * FROM dsorders WHERE orderId = '.$clid.' AND seller = '.$pId;

if ($firstrow = $conn->query($query)) {
    if (mysqli_num_rows($firstrow) == 0) {header("Location:hub.php");exit();}
    while ($row = $firstrow->fetch_assoc()) {
        $ordUd = $row["ud"];
        $ordBuyer = $row["buyer"];
        $ordPaid = $row["paid"];
        $ordShipping = $row["shipping"];
        $ordItems = $row["items"];
        $ordNStatus = $row["status"];
        $ordAddress = $row["address"];
        $ordRegdate = $row["reg_date"];
        $ordAmount = $row["amount"];
        $ordCodes = $row["codes"];
        if ($row["seller"]!=$pId){header("Location:hub.php");exit();}
    }
}
$fullOrdId = "#".$clid."-".$ordUd;
$ordCodesArray = explode(",", $ordCodes);

$query = "SELECT * FROM accountsTable WHERE id = ".$ordBuyer;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $customerUname = $row["uname"];
      $customerTitle = $row["title"];
      $customerEmail = $row["email"];
    }
}

include("../g/makeHuman.php");
require_once("../g/ordStatus.php");

$ordExplicitStatus = ordStatus($ordNStatus, 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Order <?php echo $fullOrdId; ?> | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
    <style>

    h2 {
        padding:40px 0 10px;
        border-bottom: 1px solid #ddd;
    }
    .warning {
        font-size: min(calc(9px + .3vw), 15px);
        color: #7b7b7b;
    }
    .warning.red {
        color:#ff6767;
    }
    .pHeader {
        text-decoration:underline;
        font-weight: bold;
    }
    .credTable.orders tbody > tr > td:last-child > ul > li {
        text-align: right;
    }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="../home.php"><h1 class="menutitle">Partnership</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="hub.php"><i class="fas fa-arrow-left"></i> Hub</a></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/31/Orders" target="_blank">Orders</a></li>
                    <li><a class="Bar" href="/docs/18/Digital_Store_Extension" target="_blank">DS Publishing</a></li>
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
                <h1>Order <?php echo $fullOrdId; ?> Details </h1>
                <div class='dsBanner'><img src='/Imgs/Ranks/HighMerchant.png' alt:'Oopsie!'></div>
                <div class="checkoutBox" style="margin-bottom:0;" onclick="location.reload();">
                    <button class="checkout" type="submit">
                        <i class="fas fa-redo"></i>
                        <span>Reload</span>
                    </button>
                </div>
                <h2>Quick Overview</h2>

                <?php

                    echo "<table class='credTable orders'><thead><tr><td>Order Id</td><td>Items</td><td>Amount</td><td>Received</td><td>Shipping</td><td>Codes Used</td><td>Date</td></tr></thead><tbody>";

                    $date_array = date_parse($ordRegdate);
                    $ordPubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"]." ".$date_array["hour"].":".$date_array["minute"];

                    $ordCodesUL = "<ul>";
                    foreach ($ordCodesArray as $ordCode){
                        $ordCodesUL .= "<li>".$ordCode."</li>";
                    }
                    $ordCodesUL .= "</ul>";

                    echo "<tr>";
                    echo '<td>'.$fullOrdId.'</td>';
                    echo '<td>'.$ordItems.'</td>';
                    echo '<td>'.makeHuman($ordAmount).'</td>';
                    echo '<td>'.makeHuman($ordPaid).'</td>';
                    echo '<td>'.makeHuman($ordShipping).'</td>';
                    echo '<td>'.$ordCodesUL.'</td>';
                    echo '<td>'.$ordPubdate.'</td>';
                    echo "</tr>";

                    echo "</tbody></table>";

                ?>
                <h2>Shipping Information</h2>
                <p style="text-align:left;">
                <span class="pHeader">Contact Info</span><br>
                <?php
                    echo "Customer Id: u#".$ordBuyer."<br>";
                    echo "Name: ".$customerTitle." ".$customerUname."<br>";
                    echo "Contact: ".$customerEmail." <br><span class='warning red'>try to contact the customer as little as possible!</span>";

                ?>
                </p>
                <?php
                  $address = $ds->fetchAddress($ordBuyer);
                  echo $ds->makeAddressList($address, true);

                 ?>


                <h3>Shipping</h3>
                <p>Current status:  <?php echo $ordExplicitStatus; ?></p>

<?php

if ($ordNStatus == 0){
    echo '<a href="changeOrderStatus.php?id='.$clid.'&dir=1"><button class="checkout">Order Sent</button></a>';
    echo "<p class='warning'>Click this button once you have sent the order.</p>";
}
else if ($ordNStatus == 1){
    echo '<a href="changeOrderStatus.php?id='.$clid.'&dir=1"><button class="checkout">Order Received</button></a>';
    echo "<p class='warning'>Click this button once you have confirmation the order arrived. You might not have access to this information; then leave it as such.</p>";
}
if (isset($_GET["recStatChange"])){
    echo "<a href='changeOrderStatus.php?id=$clid&dir=-1' class='warning red'>undo</a>";
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

</script>
