
<?php
if (!isset($_GET["q"])){exit();}
if (!isset($_GET["w"])){exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/parseIWDate.php");

$q = $_GET["q"];
$q = str_replace('"', '', $q);
$lowq = normalizeChars($q);

if (preg_match("/^[0-9]*$/", $_GET["w"])!=1){exit();} else {$w = $_GET["w"];}
if (isset($_GET["domain"])){
    if (preg_match("/^[0-9]$/", $_GET["domain"])!=1){exit();} else {$domain = $_GET["domain"];}
}
else {
    $domain = 0;
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
class gay {} $info = new gay;
equipDom($info, $domain);

$resultArray = array();
$additive = "";
if ($info->domain == "mystral"){$additive = " AND user = ".$info->user; }

$query = "SELECT * FROM wikicategories WHERE wiki = $w".$additive;
if ($max = $info->dbconn->query($query)) {
    while ($row = $max->fetch_assoc()){
        $name = $row["name"];
        $lowname = strtolower($name);
        if ($q == "" OR strpos($lowname, $lowq) !== false){
            $insArray = [];
            $insArray["name"] = $name;
            $insArray["id"] =  $row["id"];

            $resultArray[]=$insArray;
        }
    }
}


header('Content-Type: application/json');
echo json_encode($resultArray);


?>