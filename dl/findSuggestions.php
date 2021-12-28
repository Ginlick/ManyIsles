
<?php
if (isset($_GET["c"])) {$cate = $_GET["c"];if (preg_match("/[a-z]*/", $_GET["c"])!=1){exit();}} else {$cate = "";}
if (isset($_GET["t"])) {$type = $_GET["t"];if (preg_match("/[a-z]*/", $_GET["t"])!=1){exit();}} else {$type = "module";}
if (isset($_GET["s"])) {$gsystem = $_GET["s"];if (preg_match("/[0-9]*/", $_GET["s"])!=1){exit();}} else {$gsystem = 0;}
if (isset($_GET["z"])) {$checkSupport = $_GET["z"];if (preg_match("/[0-9]*/", $_GET["z"])!=1){exit();}} else {$checkSupport = 0;}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$q = $_GET["q"];
$q = str_replace('"', '', $q);

$resultArray = array();
$totalResults = 0;

if ($type=="diggie"){$type="diggies";}
$dltype = $type;
if ($dltype == "module"){$dltype = "products";}
if ($cate == "g"){$cate = "";}
$shorttype = substr($type, 0, 1);

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





$searchstring = 'SELECT max(id) FROM '.$dltype.' WHERE type REGEXP "'.$regcate.'"';

$indexid = "whoppee";
if ($max = $conn->query($searchstring)) {
    while ($gay = $max->fetch_row()){
        $indexid = $gay[0];
        if ($indexid != null){
            for ($x = 0; $x>=0; $x++) {
                $currentsearch = $indexid - $x;
                if (checkStat($currentsearch, $dltype) == false){continue;};
                $currsearch = 'SELECT * FROM '.$dltype.' WHERE id = '.$currentsearch;
                $toprow = $conn->query($currsearch);
                while ($row = $toprow->fetch_assoc()) {
                    $name = $row["name"];
                    $link = "View.php?id=".$row["id"]."&t=".$shorttype;
                    $categories = $row["categories"];
                    $type = $row["type"];
                    if ($type == "module"){
                        $nowSystem = $row["gsystem"];
                        if ($nowSystem != $gsystem AND $nowSystem != 0 AND $gsystem != 0){continue;}
                    }
                    $lowq = strtolower($q);
                    $lowname = strtolower($name);
                    $lowcategories = strtolower($categories);
                    if ($checkSupport == 1 AND $row["support"]==0){continue;}
                    if (!preg_match("/".$regcate."/", $type)) {continue;}
                    if ($q == ""){
                        $totalResults++;
                        $resultArray[$name]=$link;
                    }
                    else if (strpos($lowname, $lowq) !== false OR strpos($lowcategories, $lowq) !== false) {
                        $totalResults++;
                        $resultArray[$name]=$link;
                    }
                }
                if ($currentsearch==1){ break;}
                if ($totalResults == 22){ break;}
            }
        }
    }
}


header('Content-Type: application/json');
echo json_encode($resultArray);


?>