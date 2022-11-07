<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/security.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");

require_once("../g/makeHuman.php");

if (isset($_GET["id"])){
    if (preg_match("/^[0-9]+$/", $_GET["id"])!=1){exit();} else {$sid = $_GET["id"];}
} else {echo "<script>window.location.replace('hub');</script>";exit();}

$row1 = [];$row21 = [];
$query = "SELECT * FROM ds_asubs WHERE id = $sid";
if ($toprow = $moneyconn->query($query)) {
    if (mysqli_num_rows($toprow) == 0) {echo "<script>window.location.replace('hub');</script>";exit(); }
    while ($row = $toprow->fetch_assoc()) {
        $row1 = $row;
        $datas = json_decode($row["datas"], true);
        if ($datas["paymode"]!="credit"){exit;}
        if ($row["buyer"]!=$uid){exit();}
        $query = "SELECT * FROM dssubs WHERE id = ".$datas["subId"];
        if ($toprow2 = $conn->query($query)) {
            while ($row2 = $toprow2->fetch_assoc()) {
                $row21 = $row2;
                $period_start = $row["reg_date"];
                $period_end = strtotime($period_start. " + ".$row["validity"]." days");
                $datas2 = json_decode($row2["datas"], true);
            }
        }
    }
}

if ($row1["status"]=="canceled"){
    $word = "will be deleted";
    $button =    '  <a href="subMod.php?id='.$sid.'&dir=1"><button class="checkout" type="submit">
                        <span>Renew Plan</span>
                    </button></a>';
    $summore ="<p style='color:var(--col-red)'>canceled</p>";
}
else {
    $word = "renews";
    $button =    '  <a href="subMod.php?id='.$sid.'&dir=0"><button class="checkout" type="submit">
                        <i class="fas fa-times"></i>
                        <span>Cancel Plan</span>
                    </button></a>';
    $summore ="<p>".makeHuman($datas2["price"])." per year<br>Payment Method: Many Isles credit</p>";

}
 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Edit Subscription | Digital Store</title>
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
    <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="/ds/store"><h1 class="menutitle">Edit Subscription</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="hub"><i class="fas fa-arrow-left"></i> Hub</a></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
                <h1><?php  echo $row21["name"]; ?></h1>
                <?php echo $summore; ?>
                <p>Your plan was created on <?php echo date("F j, Y", strtotime($period_start)); ?>.<br>Your plan <?php echo $word; ?> on <?php echo date("F j, Y", $period_end); ?>.</p>
                <div style="padding-top:50px"><?php echo $button; ?></div>
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
if (why == "updated"){
    createPopup("d:dsp;txt:Subscription updated!");
}
</script>


