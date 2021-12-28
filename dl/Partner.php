<?php
if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:/dl/Partner.php?id=1");}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$producttab = <<<YEAH

           <div class="container MEGANUM">
            <a  href="/dl/View.php?id=SENDMETOTHEPRODUCT">
                <div class="imgCont" load-image="/IndexImgs/GREATINDEXIMAGE" id="recMEGANUM">
                </div>
            <div class='titling'>GRANDTITLE</div></a> </div>
YEAH;
$name = "";
$image="";
$jacob="";
$query = sprintf("SELECT * FROM partners WHERE id = %s", $_GET["id"]);
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $name = $row["name"];
      $image = $row["image"];
      $jacob = $row["jacob"];
      $status = $row["status"];
    }
}
if ($name == null){header("Location: /dl/Goods.php");}
if ($status == "suspended"){$jacob = "<span style='color:red'>This partnership is currently suspended, and all its products are temporarily unavailable in the digital library.</a>";}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
     <link rel="stylesheet" type="text/css" href="global/dl2.css">
    <title><?php echo $name; ?> | Products</title>
    <style>
    </style>
</head>
<body  onresize="hideSome();">
<div class="all-container">
   <div include-html="global/gprods.html">
   </div>


<div class="maincontain">

    <div  include-html="global/gsearch.html" class="search">
    </div>

    <div>
    <div include-html="global/gmenu.html" class="sideMenu">
    </div>
    </div>

<div class="content">

<h1 class="maintitleMobile" style="padding-bottom:5px;"> <?php echo $name; ?></h1>

<div class="viewMage" load-image="PartIm/<?php echo $image; ?>" id="viewMage">
</div>

<div style="width:60%;display:inline-block;float:left;padding:5px">
<h1 class="maintitle" style="padding-bottom:5px;"> <?php echo $name; ?></h1>

<p class="jacobp"> <?php echo $jacob; ?> </p>

</div>

        <div>
            <img src="/Imgs/Bar2.png" alt="RedBar" class='separator'>
        </div>
<div >


<?php

if ($status != "suspended"){
    $sql = 'SELECT * FROM products WHERE partner = "'.$name.'"ORDER BY popularity DESC';
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) > 0) {
       echo '  <div class="digCont"><h1 style="margin-left:2.15%;">By this Partner</h1>';
       makeRow($result, "m");
    }

    $sql = 'SELECT * FROM diggies WHERE partner = "'.$name.'"ORDER BY popularity DESC';
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) > 0) {
       echo '  <div class="digCont"><h1 style="margin-left:2.15%;">Tools by this Partner</h1>';
       makeRow($result, "d");
    }

    $sql = 'SELECT * FROM art WHERE partner = "'.$name.'"ORDER BY popularity DESC';
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) > 0) {
       echo '  <div class="digCont"><h1 style="margin-left:2.15%;">Art by this Partner</h1>';
       makeRow($result, "a");
    }
}
function makeRow($result, $type){
global $nume;
$nume = 0;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($nume == 8 ){break;}
        $premium = false;
        if ($row["tiers"]!="g"){$premium = true;}
        global $producttab;
        $titling = $row["name"];
        if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
        makeFirstProdTab($producttab, $titling, $row["image"], $row["id"], $premium, $type);
    }
}
echo '</div>';
}
 function makeFirstProdTab($producttab, $titling, $image, $link, $premium, $type) {
        global $nume;
        $nume++;
        $producttab = str_replace("GREATINDEXIMAGE", $image, $producttab);
        $producttab = str_replace("GRANDTITLE", $titling, $producttab);
        $producttab = str_replace("SENDMETOTHEPRODUCT", $link."&t=".$type, $producttab);
        $producttab = str_replace("MEGANUM", $nume, $producttab);
        if ($premium == true){$producttab = str_replace("class='titling'", "class='titling premium'", $producttab);}
        echo $producttab;
        
 }

?>
</div>
        </div></div>
<p style="display:none;" id="partId"><?php echo $_GET["id"]; ?></p>

   <div class="footer" include-html="global/Gfooter.html">
   </div>
</div>
</body>
</html>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script src="global/dl2v2.js"></script>
