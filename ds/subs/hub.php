<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/security.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

$query = "DELETE FROM ds_asubs WHERE buyer = $uid AND validity = 0";
$moneyconn->query($query);

require_once("../g/makeHuman.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Subscriptions | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
    <style>

        .credTable.prods tbody > tr > :nth-child(2) {
            width: 20%;
        }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="/ds/store"><h1 class="menutitle">Subscriptions</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="/account/SignedIn.php"><i class="fas fa-arrow-left"></i> Account</a></li>
                    <li><a class="Bar" href="/ds/store"><i class="fas fa-arrow-left"></i> Digital Store</a></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
                <h1>Subscriptions Hub</h1>
                <p>View your active plans here.</p>
                <div>
                    <button class="checkout" type="submit" onclick="window.location='hub.php'">
                        <i class="fas fa-redo"></i>
                        <span>Reload</span>
                    </button>
                </div>
                <?php
                    $query = "SELECT * FROM ds_asubs WHERE buyer = $uid";
                    if ($toprow = $moneyconn->query($query)) {
                        if (mysqli_num_rows($toprow) != 0) {
                            echo "<table class='credTable prods' style='margin-top:60px;'><thead><tr><td></td><td>Name</td><td>Price</td><td>Validity</td><td></td></tr></thead><tbody>";
                            while ($row = $toprow->fetch_assoc()) {
                                $datas = json_decode($row["datas"], true);
                                $query = "SELECT * FROM dssubs WHERE id = ".$datas["subId"];
                                if ($toprow2 = $conn->query($query)) {
                                    while ($row2 = $toprow2->fetch_assoc()) {
                                        $period_start = $row["reg_date"];
                                        $period_end = strtotime($period_start. " + ".$row["validity"]." days");
                                        $datas2 = json_decode($row2["datas"], true);
                                        if ($datas["paymode"]=="stripe"){
                                            $href = "create-portal-session.php?customer_id=".$datas["stripe_customer"];
                                        }
                                        else {
                                            $href = "sub?id=".$row["id"];
                                        }
                                        $inset = ""; if ($row["status"]=="canceled"){$inset = "style='color:var(--col-red)'";}
                                        echo "<tr>";
                                        echo '<td><img src="'.$row2["image"].'" alt="thumbnail" /></td>';
                                        echo '<td>'.$row2["name"].'</td>';
                                        echo '<td>'.makeHuman($datas2["price"]).'</td>';
                                        echo '<td '.$inset.'>'.date("d.m.Y", $period_end).'</td>';
                                        echo '<td><a href="'.$href.'"><button class="checkout homescreen"><i class="fas fa-arrow-right"></i> Edit</button></a></td>';
                                        echo "</tr>";
                                    }
                                }
                            }
                            echo "</tbody></table>";
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

</script>


