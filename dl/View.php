<?php

if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:/dl/Goods.php");exit();}
if (isset($_GET["t"])){if (preg_match("/^[a-z]*$/", $_GET["t"])!=1){header("Location:/dl/Goods.php");exit();}}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$prodnum = $_GET["id"];
if (isset($_GET["t"])){$type = $_GET["t"];}
else {$type = "m";}

$dltype = "products";
if ($type == "d"){$dltype = "diggies";}
else if ($type == "a"){$dltype = "art";}

$sufficient = true;

$producttab = <<<YEAH
           <div class="container MEGANUM">
            <a  href="/dl/View.php?id=SENDMETOTHEPRODUCT">
                <div class="imgCont" load-image="/IndexImgs/GREATINDEXIMAGE" id="recMEGANUM">
                </div>
            <div class='titling'>GRANDTITLE</div></a>    </div>
YEAH;
$name = "";
$image="";
$partner="";
$categories="";
$tiers="";
$jacob="";
$partnerid="";
$linke="";
$popularity="";
$downloads="";
$support = "";
$sysNum = 0;
$indirectProd=0;
$query = sprintf("SELECT * FROM products WHERE id = %s", $_GET["id"]);
if ($type == "d"){$query = str_replace("products", "diggies", $query);}
else if ($type == "a"){$query = str_replace("products", "art", $query);}

if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $name = $row["name"];
      $image = $row["image"];
      $partner = $row["partner"];
      $categories = $row["type"];
      $tiers = $row["tiers"];
      $jacob = $row["jacob"];
      $linke = $row["link"];
      $popularity = $row["popularity"];
      $support = $row["support"];
      if ($type == "m"){$downloads = $row["downloads"];$sysNum = $row["gsystem"];$indirectProd=$row["indirect"];}
    }
}
$ttiers = $tiers;
if (!isset($_COOKIE["loggedIn"]) and $tiers != "g"){$sufficient = false;}
if ($name == null){header("Location: /dl/Goods.php");exit();}

if (isset($_GET["dl"])){
    $downloads++;
    $updl = sprintf("UPDATE products SET downloads = '%s' WHERE id = %s", $downloads, $_GET["id"]);
    $conn->query($updl);
}

$popularity++;
$uppop = sprintf("UPDATE products SET popularity = '%s' WHERE id = %s", $popularity, $_GET["id"]);
if ($type == "d"){$uppop = str_replace("products", "diggies", $uppop);}
else if ($type == "a"){$uppop = str_replace("products", "art", $uppop);}
$conn->query($uppop);

$pstatus = "";
$paquery = sprintf('SELECT id, status FROM partners WHERE name = "%s"', $partner);
if ($arow = $conn->query($paquery)) {
     while ($row = $arow->fetch_assoc()) {
      $partnerid = $row["id"];
      $pstatus = $row["status"];
    }       
}
if ($pstatus == "suspended"){header("Location:/dl/Goods.php");exit();}

$ppartner = $partner;
if ($partner=="Pantheon"){$partner = "the ".$partner;}
if ($partner=="Traveler"){$partner = "a ".$partner;}


$premium = true;
if ($tiers =="g" ){$tiers = "Free";$premium = false;}
if ($tiers =="1" ){$tiers = "Tier 1";}
if ($tiers =="2" ){$tiers = "Tier 2";}
if ($tiers =="3" ){$tiers = "Tier 3";}

if (isset($_COOKIE["loggedIn"])) {
$id = $_COOKIE["loggedIn"];
$uname = "";
$curpsw = "";
$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $curpsw = $row["password"];
    }
}
$usertier = "g";

require("../Server-Side/encryptData.php");
$cpsw = openssl_decrypt ($_COOKIE['loggedP'], $method, $key, 0, $iv);

  if (password_verify($cpsw, $curpsw)!=1){setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}
    else {
    $query = "SELECT tier FROM accountsTable WHERE id = ".$id;
    if ($firstrow=$conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $usertier = $row["tier"];
   }
    }
    if ($usertier != "g"){$usertier = intval($usertier);}
    }
    if ($ttiers != "g"){
        if ($usertier == "g" or $usertier<intval($ttiers)){
        $sufficient = false;
    }
    }
}

function giveSysName($sys) {
    global $type, $sysName;
    if ($type=="m"){
        if ($sys == 1){$sysName = "5eS";}
        else if ($sys == 2){$sysName = "5e";}
    }
}
giveSysName($sysNum);

?>



<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="global/dl2.css">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <title><?php echo $name; ?> | Products</title>
<style>
    .maintitle {
      font-size: calc(23px + 2.6vw);
    }
    .viewTitle {
        padding-left:20px;
    }
    #tierInfo a {
        color: #a9a9a9;
    }
.sysCont {
    padding: 0 20px;
    color:#a9a9a9;
}
</style>
</head>
<body onresize="hideSome();">
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

<h1 class=" maintitleMobile <?php if($premium == true){echo 'premium';}?>"><?php echo $name; ?></h1>

   <div class="viewMage">
<?php

$lastpart = '" download>
      <div style="position: relative;display: block;" id="contt">
        <img src="/IndexImgs/';
$lasttpart = '"class="linkim">
        <div class="overlay" style="padding-top:4.6vw;">
            <span class="viewOverlay"><i class="fas fa-arrow-down"></i></span>
            <p class="viewOverlayText">Download</p>
         </div>
      </div></a>';
if ($type =="d"){
    $lasttpart = str_replace("Download", "View", $lasttpart);
    $lasttpart = str_replace("fas fa-arrow-down", "fas fa-arrow-up", $lasttpart);
    $lastpart = str_replace("download", "target='_blank'", $lastpart);
}
if ($type=="m"){
    $lastpart = str_replace('contt"', 'contt" onclick="downloaded();"', $lastpart);
    if ($indirectProd == 1) {
        $lasttpart = str_replace("Download", "View", $lasttpart);
        $lasttpart = str_replace("fas fa-arrow-down", "fas fa-arrow-up", $lasttpart);
        $lastpart = str_replace("download", "target='_blank'", $lastpart);
    }
}
if ($sufficient == false){
    $lasttpart = str_replace("fa-arrow-down", "fa-times", $lasttpart);
    $lasttpart = str_replace("fa-arrow-up", "fa-times", $lasttpart);
    $fpart = str_replace("download", "target='_blank'", $lastpart);
    echo '<a href="/ds/tiers.php'.$fpart.$image.$lasttpart;
}
else if ($type == "m") {
    if ($indirectProd == 0){
        echo '<a href="Friiz/'.$linke.$lastpart.$image.$lasttpart;
    }
    else echo '<a href="'.$linke.$lastpart.$image.$lasttpart;
}
else if ($type == "d") {
echo '<a href="'.$linke.$lastpart.$image.$lasttpart;
}
else {
echo '<a href="Art/'.$linke.$lastpart.$image.$lasttpart;
}


?>
</div>

<div class="prodInfo">
<h1 class=" maintitle <?php if($premium == true){echo 'premium';}?>"><?php echo $name; ?></h1>

<div class="tabs-view">
    <div id="charops" class="tab" onclick="clinnation('charop')">Classes</div>
    <div id="races" class="tab" onclick="clinnation('race')">Races</div>
    <div id="rules" class="tab" onclick="clinnation('rule')">Rules</div>
    <div id="adventures" class="tab" onclick="clinnation('adventure')">Adventures</div>
    <div id="lores" class="tab" onclick="clinnation('lore')">Lore</div>
    <div id="dmss" class="tab" onclick="clinnation('dms')">DM Stuff</div>
    <div id="hmbrws" class="tab" onclick="clinnation('hmbrw')">Homebrewing</div>
    <div id="genrs" class="tab" onclick="clinnation('genr')">Generator</div>
    <div id="indxs" class="tab" onclick="clinnation('rule')">Index</div>
    <div id="viss" class="tab" onclick="clinnation('vis')">Visual</div>
    <div id="carts" class="tab" onclick="clinnation('cart')">Cartography</div>
    <div id="duns" class="tab" onclick="clinnation('dun')">Dungeons</div>
</div>

<p>From <a href="Partner.php?id=<?php echo $partnerid; ?>"><?php echo $partner; ?></a></i></p>

<p id="tierInfo"><?php

if (!isset($_COOKIE["loggedIn"]) and $ttiers != "g"){
    echo $tiers."<span onclick='".'showSignIn("general")'."'> (Sign in to Purchase)</a>";
}
else if ($sufficient==false ){
 echo $tiers."<a href='/ds/tiers.php' target='_blank'> (Purchase Tier)</a>";
}
else {
    echo $tiers;
}

?>

</p>
<?php
if ($sufficient == true){
if ($type != "d" AND $indirectProd == 0){
$message = '<p> -- <br><a href="Friiz/'.$linke.'" target="_blank"> View in Browser</a></p>';
if ($type == "a"){$message = str_replace("Friiz", "Art", $message);}
echo $message;
}}
if ($type == "m"){
    if ($support == 1){
        echo '<p><a href="/ds/support.php?which='. $name .'">Support Creator</a></p>';
    }
}
?>

</div>

<div class="jacob">
<p class="sysCont">
<?php
echo $sysName."<br>";
if ($type=="m" AND $indirectProd==0 AND $downloads != 0){
    $fakedl = 2 + $downloads;
    echo " $fakedl downloads";
}
?>
</p>
<p style="padding:0px 20px"> <?php echo $jacob; ?> </p>
<div >
<img src='/Imgs/Bar2.png' alt='RedBar'  class='separator'>
</div>
</div>


<div style="width:100%;display:inline-block;float:left">

<?php

$regcate = "^";
if ($type == "m"){
    if (strpos($categories, "c")!==false) {$regcate = $regcate."(?=.*c)";}
    if (strpos($categories, "r")!==false) {$regcate = $regcate."(?=.*r)";}
    if (strpos($categories, "u")!==false) {$regcate = $regcate."(?=.*u)";}
    if (strpos($categories, "a")!==false) {$regcate = $regcate."(?=.*a)";}
    if (strpos($categories, "l")!==false) {$regcate = $regcate."(?=.*l)";}
    if (strpos($categories, "d")!==false) {$regcate = $regcate."(?=.*d)";}
}
else if ($type == "d"){
    if (strpos($categories, "h")!==false) {$regcate = $regcate."(?=.*h)";}
    if (strpos($categories, "r")!==false) {$regcate = $regcate."(?=.*r)";}
    if (strpos($categories, "i")!==false) {$regcate = $regcate."(?=.*i)";}
}
else  {
    if (strpos($categories, "v")!==false) {$regcate = $regcate."(?=.*v)";}
    if (strpos($categories, "n")!==false) {$regcate = $regcate."(?=.*n)";}
    if (strpos($categories, "m")!==false) {$regcate = $regcate."(?=.*m)";}
}
$regcate = $regcate.".+$";

$sql = 'SELECT id, name, image, tiers FROM products WHERE partner = "'.$ppartner.'"ORDER BY popularity DESC';
if ($type == "d") {$sql = str_replace("products", "diggies", $sql);}
if ($type == "a") {$sql = str_replace("products", "art", $sql);}
$result = $conn->query($sql);
if ( $partner != "the Pantheon" and $partner != "a Traveler" and $result != null){

echo "<a href='Partner.php?id=".$partnerid."'><h1 id='moretitle' class='viewTitle'>More by This Partner</h1></a>";

$latestid = "whelp";
$nume = 0;
$partnerHasProducts = false;

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
     if ($row["id"] == $prodnum ){continue;}
     if ($nume == 8 ){break;}
     $premium = false;
     if ($row["tiers"]!="g"){$premium = true;}
     makeFirstProdTab($producttab, $row["name"], $row["image"], $row["id"], $premium, "a");
    $partnerHasProducts = true;
  }
}

echo "<div id='morebar'>
<img src='/Imgs/Bar2.png' alt='RedBar'  class='separator'>
</div>";}


echo "<h1 class='viewTitle'>Similar Products</h1>";

$nume = 0;
$searchstring = "SELECT max(id) FROM products WHERE type REGEXP '".$regcate."'";
if ($type == "d") {$searchstring = str_replace("products", "diggies", $searchstring);}
if ($type == "a") {$searchstring = str_replace("products", "art", $searchstring);}

$indexid = "whoppee";
if ($max = $conn->query($searchstring)) {
    while ($gay = $max->fetch_row()){
        $indexid = $gay[0];
        if ($indexid != null){
             for ($x = 0; $x>=0; $x++) {
                $currentsearch = $indexid - $x;
                if ($currentsearch == $prodnum){continue;}
                if (checkStat($currentsearch, $dltype) == false){continue;};
                $currsearch = sprintf("SELECT * FROM products WHERE id = %s and type REGEXP ", $currentsearch)."'".$regcate."'";
                if ($type == "d") {$currsearch = str_replace("products", "diggies", $currsearch);}
                if ($type == "a") {$currsearch = str_replace("products", "art", $currsearch);}
                $toprow = $conn->query($currsearch);
                while ($row = $toprow->fetch_assoc()) {
                         $titling = $row["name"];
                         if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
                         $image = $row["image"];
                         $link = $row["id"];
                         $premium = false;
                         if ($row["tiers"]!="g"){$premium = true;}
                         makeFirstProdTab($producttab, $titling, $image, $link, $premium, "b");
                }
                if ($currentsearch==1){ break;}
                if ($nume == 8){ break;}
             }
        }
    }
}

function checkStat($prodid, $t) {
    global $conn;
    $query = 'SELECT partner FROM '.$t.' WHERE id ='.$prodid;
    $result = $conn->query($query);
    $partner = "x";
    while ($row = $result->fetch_assoc()) {
        $partner = $row["partner"];
    }
    $query = 'SELECT status FROM partners WHERE name ="'.$partner.'"';
    $status = "active";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $status = $row["status"];
    }
    if ($status == "suspended"){return false;}
    else {return true;}
}

 function makeFirstProdTab($producttab, $titling, $image, $link, $premium, $whichone) {
        global $nume, $type;
        $nume++;
        if ($type == "d"){$link=$link."&t=d";}
        else if ($type == "a"){$link=$link."&t=a";}
        $producttab = str_replace("GREATINDEXIMAGE", $image, $producttab);
        $producttab = str_replace("GRANDTITLE", $titling, $producttab);
        $producttab = str_replace("SENDMETOTHEPRODUCT", $link, $producttab);
        $producttab = str_replace("MEGANUM", $nume, $producttab);
        if ($premium == true){$producttab = str_replace("class='titling'", "class='titling premium'", $producttab);}
        echo $producttab;
        
 }
        

?>


</div>

        <div>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
        </div>

</div>
</div>


</div>



<p id="catInfo" style="display:none;"><?php echo $categories; ?></p>
<p id="typeInfo" style="display:none;"><?php echo $type; ?></p>
<p id="sysInfo" style="display:none;"><?php echo $sysNum; ?></p>

<form action="View.php" method="_GET" style="display:none" id="dlForm"><input type="text" id="toReturnId" name="id" style="display:none" value="<?php echo $_GET["id"]; ?>"><input type="text" id="Download" name="dl" style="display:none" value="yep"><input type="submit" style="display:none"></form>

   <div class="footer" include-html="global/Gfooter.html">
   </div>
</div>
</body>
</html>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script src="global/dl2v2.js"></script>
<script>

<?php
 $efsdfjvkc = ' if (0=='.$partnerHasProducts.'){document.getElementById("moretitle").style.display = "none";document.getElementById("morebar").style.display = "none" }'; if ($partner != "the Pantheon" AND $partner != "a Traveler"){ echo $efsdfjvkc ;} ?>

function downloaded(){
    document.getElementById("dlForm").submit();
}
</script>
