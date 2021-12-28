
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
     <link rel="stylesheet" type="text/css" href="global/dl2.css">
   <title>Products</title>
</head>
<body>
<?php


if (preg_match("/^[A-Za-z0-9' ]*$/", $_GET["query"])!=1){header("Location:/dl/Goods.php");exit();}
if (preg_match("/[a-z]*/", $_GET["category"])!=1 and $_GET["category"]!= ""){header("Location:/dl/Goods.php");exit();}
if (preg_match("/[a-z]*/", $_GET["type"])!=1){header("Location:/dl/Goods.php");exit();}
if (preg_match("/[0-9]*/", $_GET["gsystem"])!=1){header("Location:/dl/Goods.php");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$producttab = <<<YEAH

           <div class="container">
            <a  href="/dl/WHATSIT.php?id=SENDMETOTHEPRODUCT&t=m">
                <div class="imgCont" load-image="/IndexImgs/GREATINDEXIMAGE" id="recMEGANUM">
                </div>
            <div class='titling'>GRANDTITLE</div></a>    </div>
YEAH;
$query = $_GET["query"];
$type = $_GET["type"];
$gsystem = $_GET["gsystem"];
if ($type=="diggie"){$type="diggies";}
$dltype = $type;
if ($dltype == "module"){$dltype = "products";}
$cate = $_GET["category"];
if ($cate == "g"){$cate = "";}
?>


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
<?php
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

 function makeFirstProdTab($producttab, $titling, $image, $link, $premium, $whichone, $MEGANUM) {
        global $type, $nowSystem;
        if ($whichone =="1"){global $nume;
        $nume++;}
         else if ($whichone =="2"){global $nume2;
        $nume2++;}
        $producttab = str_replace("GREATINDEXIMAGE", $image, $producttab);
        $producttab = str_replace("GRANDTITLE", $titling, $producttab);
        $producttab = str_replace("WHATSIT", "View", $producttab);
        $producttab = str_replace("SENDMETOTHEPRODUCT", $link, $producttab);
        $producttab = str_replace("MEGANUM", $MEGANUM, $producttab);
        if ($type == "diggies"){$producttab = str_replace("t=m", "t=d", $producttab);}
        if ($type == "art"){$producttab = str_replace("t=m", "t=a", $producttab);}
        if ($premium == true){$producttab = str_replace("class='titling'", "class='titling premium'", $producttab);}
        echo $producttab;

 }


$nume = 0;
$regcate = "^";
if ($type == "module"){
    if (strpos($cate, "c")!==false) {$regcate = $regcate."(?=.*c)";}
    if (strpos($cate, "r")!==false) {$regcate = $regcate."(?=.*r)";}
    if (strpos($cate, "u")!==false) {$regcate = $regcate."(?=.*u)";}
    if (strpos($cate, "a")!==false) {$regcate = $regcate."(?=.*a)";}
    if (strpos($cate, "l")!==false) {$regcate = $regcate."(?=.*l)";}
    if (strpos($cate, "d")!==false) {$regcate = $regcate."(?=.*d)";}
}
else if ($type == "diggies"){
    if (strpos($cate, "h")!==false) {$regcate = $regcate."(?=.*h)";}
    if (strpos($cate, "r")!==false) {$regcate = $regcate."(?=.*r)";}
    if (strpos($cate, "i")!==false) {$regcate = $regcate."(?=.*i)";}
}
else {
    if (strpos($cate, "v")!==false) {$regcate = $regcate."(?=.*v)";}
    if (strpos($cate, "n")!==false) {$regcate = $regcate."(?=.*n)";}
    if (strpos($cate, "m")!==false) {$regcate = $regcate."(?=.*m)";}
}
$regcate = $regcate.".+$";

if ($query != "") {

echo "   <div class='digCont'  style='border:none'>     <h1>Results for ".'"'.$query.'"</h1>';

$searchstring = 'SELECT max(id) FROM products WHERE type REGEXP "'.$regcate.'"';
if ($type == "diggies") {$searchstring = str_replace("products", "diggies", $searchstring);}
if ($type == "art") {$searchstring = str_replace("products", "art", $searchstring);}

$indexid = "whoppee";
if ($max = $conn->query($searchstring)) {
    while ($gay = $max->fetch_row()){
        $indexid = $gay[0];
        if ($indexid != null){
            for ($x = 0; $x>=0; $x++) {
                $currentsearch = $indexid - $x;
                if (checkStat($currentsearch, $dltype) == false){continue;};
                $currsearch = 'SELECT * FROM products WHERE id = '.$currentsearch.' AND type REGEXP "'.$regcate.'"';
                if ($type == "diggies") {$currsearch = str_replace("products", "diggies", $currsearch);}
                if ($type == "art") {$currsearch = str_replace("products", "art", $currsearch);}
                $toprow = $conn->query($currsearch);
                while ($row = $toprow->fetch_assoc()) {
                    $titling = $row["name"];
                    $categories = strtolower($row["categories"]);
                    $lowq = strtolower($query);
                    if (strpos(strtolower($titling), $lowq) !== false OR strpos($categories, $lowq) !== false) {
                        if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
                        $image = $row["image"];
                        $link = $row["id"];
                        if ($type == "module"){
                            $nowSystem = $row["gsystem"];
                            if ($nowSystem != $gsystem AND $nowSystem != 0 AND $gsystem != 0){continue;}
                        }
                        $premium = false;
                        if ($row["tiers"]!="g"){$premium = true;}
                        makeFirstProdTab($producttab, $titling, $image, $link, $premium, "1", "astr".$x);
                    }
                }
                if ($currentsearch==1){ break;}
                if ($nume==20){ break;}
            }
        }
    }
}

echo <<<YEAH
</div>
        <div>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
        </div>
YEAH;
}

if ($cate != "" or $query == ""){
echo "<div class='digCont' style='border:none'> <h1>Fitting Categories</h1>";
$nume2 = 0;

$searchstring = "SELECT max(id) FROM products WHERE type REGEXP '".$regcate."'";
if ($type == "diggies") {$searchstring = str_replace("products", "diggies", $searchstring);}
if ($type == "art") {$searchstring = str_replace("products", "art", $searchstring);}

$indexid = "whoppee";
if ($max = $conn->query($searchstring)) {
    while ($gay = $max->fetch_row()){
        $indexid = $gay[0];
        if ($indexid != null){
             for ($x = 0; $x>=0; $x++) {
                $currentsearch = $indexid - $x;
                if (checkStat($currentsearch, $dltype) == false){continue;};
                $currsearch = sprintf("SELECT * FROM products WHERE id = %s and type REGEXP ", $currentsearch)."'".$regcate."'";
                if ($type == "diggies") {$currsearch = str_replace("products", "diggies", $currsearch);}
                if ($type == "art") {$currsearch = str_replace("products", "art", $currsearch);}
                $toprow = $conn->query($currsearch);
                while ($row = $toprow->fetch_assoc()) {
                    $titling = $row["name"];
                    if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
                    $image = $row["image"];
                    $link = $row["id"];
                    if ($type == "module"){
                        $nowSystem = $row["gsystem"];
                        if ($nowSystem != $gsystem AND $nowSystem != 0 AND $gsystem != 0){continue;}
                    }
                    $premium = false;
                    if ($row["tiers"]!="g"){$premium = true;}
                    makeFirstProdTab($producttab, $titling, $image, $link, $premium, "2", "bstr".$x);
                }
                if ($currentsearch==1){ break;}
                if ($nume2 ==20){ break;}
             }
        }
    }
}
echo <<<YEAH
</div>
        <div>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
        </div>
YEAH;
}



?>

<p id="catInfo" style="display:none;"><?php echo $cate; ?></p>
<p id="querInfo" style="display:none;"><?php echo $query; ?></p>
<p id="typeInfo" style="display:none;"><?php echo $_GET["type"]; ?></p>

</div>



        </div>
   <div class="footer" include-html="global/Gfooter.html">
   </div>
</div>
</body>
</html>

<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script src="global/dl2v2.js"></script>
