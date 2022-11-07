
<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/parseIWDate.php");
require("getWiki.php");

if (isset($_GET["q"])) {
    $q = $_GET["q"];
    $q = str_replace('"', '', $q);
    $lowq = normalizeChars($q);
}
else {
    $lowq = "";
}

$w = 0;
if (isset($_GET["w"])){
    if (preg_match("/[0-9]*/", $_GET["w"])!=1){exit();} else {$w = $_GET["w"];}
}

if (isset($_GET["mode"])){
    if (preg_match("/^[0-9]*$/", $_GET["mode"])!=1){exit();} else {$mode = $_GET["mode"];}
}
else {
    $mode = 0;
}
if (isset($_GET["ig"])){
    if (preg_match("/^[0-9]$/", $_GET["ig"])!=1){exit();} else {$ignore = $_GET["ig"];}
}
else {
    $ignore = 0;
}
if (isset($_GET["domain"])){
    if (preg_match("/^[0-9]$/", $_GET["domain"])!=1){exit();} else {$domain = $_GET["domain"];}
}
else {
    $domain = 0;
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
class gay {} $info = new gay;
equipDom($info, $domain);

$dateArray = getDateArray($info->dbconn, $w);
$resultArray = array();


$query = "SELECT a.*
FROM $info->database a
LEFT OUTER JOIN $info->database b
    ON a.id = b.id AND a.v < b.v
WHERE b.id IS NULL ORDER BY importance DESC LIMIT 0, 999";

if ($result = $info->dbconn->query($query)){
    $counter = 0;
    while ($row = $result->fetch_assoc()) {
        $itemArray = [];
        $name = normalizeChars($row["name"]);
        $shortName = normalizeChars($row["shortName"]);
        $queryTags = normalizeChars($row["queryTags"]);
        if ($mode != 0 AND $mode == $row["id"] AND $ignore == 0) {
            continue;
        }

        if ($row["status"]=="suspended"){
            continue;
        }
        if ($w != 0 AND $ignore != 1){
            if (getWiki($row["id"], $info->database, $info->dbconn)!=$w){
                 continue;
            }
        }

        if ($lowq == "" OR strpos($name, $lowq) !== false OR strpos($shortName, $lowq) !== false OR strpos($queryTags, $lowq) !== false){
            $counter++;
            $itemArray["id"] = $row["id"];
            $itemArray["name"] = $row["name"];
            $itemArray["thumbnail"] = getArtImage($row["sidetabImg"], $row["banner"], $row["NSFW"]);
            $itemArray["genre"] = $row["cate"];
            $itemArray["NSFW"] = $row["NSFW"];
            $itemArray["date"] = parseIWDate($row["timeStart"], $row["timeEnd"], $dateArray);
            $resultArray[] = $itemArray;
            if ($counter == 22){break;}
        }
    }
    if ($mode == 0 && $info->luckying) {
        function findRand($w) {
            global $info;
            $query = "SELECT a.*
            FROM $info->database a
            LEFT OUTER JOIN $info->database b
                ON a.id = b.id AND a.v < b.v
            WHERE b.id IS NULL ORDER BY RAND() LIMIT 1";
            if ($result = $info->dbconn->query($query)){
                while ($row = $result->fetch_assoc()) {
                    if ($w != 0){
                        if (getWiki($row["id"], $info->database, $info->dbconn)==$w){
                            return $row["id"];
                        }
                        else {
                            return findRand($w);
                        }
                    }
                }
            }
        }
        $itemArray["id"] = findRand($w);
        $itemArray["name"] = "I'm Feeling Lucky";
        $itemArray["thumbnail"] = "/wikimgs/icons/random.jpg";
        $itemArray["genre"] = "Lore";
        $itemArray["NSFW"] = 0;
        $itemArray["date"] = "type something to generate new link";
        $resultArray[] = $itemArray;
    }
}

header('Content-Type: application/json');
echo json_encode($resultArray);


?>
