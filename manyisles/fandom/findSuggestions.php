<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
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
$sourceMode = false;
if (isset($_GET["src"]) AND $_GET["src"] == 1){$sourceMode = true;}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
$info = new gen("view", 0, $w);
equipDom($info, $domain);

$dateArray = getDateArray($info->dbconn, $w);
$resultArray = array();

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
                    return $row;
                }
                else {
                    return findRand($w);
                }
            }
        }
    }
}
if (!isset($_GET["todo"]) OR $_GET["todo"] == "searchSuggs"){
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
            if ($row["cate"] == "Source"){
                $shortName = "Source: " . $shortName;
            }
            else if ($sourceMode){continue;}        
            if ($mode != 0 AND $mode == $row["id"] AND $ignore == 0) {
                continue;
            }

            if ($row["status"]=="suspended"){
                continue;
            }
            $wikiName = "Unknown";
            if ($w != 0 AND $ignore != 1){
                if (getWiki($row["id"], $info->database, $info->dbconn)!=$w){
                     continue;
                }
                $wikiName = getWikiName($w, $info->database, $info->dbconn);
            }

            if ($lowq == "" OR strpos($name, $lowq) !== false OR strpos($shortName, $lowq) !== false OR strpos($queryTags, $lowq) !== false){
                $counter++;
                $itemArray["id"] = $row["id"];
                $itemArray["name"] = $row["name"];
                $itemArray["thumbnail"] = getArtImage($row["sidetabImg"], $row["banner"], $row["NSFW"]);
                $itemArray["genre"] = $row["cate"];
                $itemArray["NSFW"] = $row["NSFW"];
                $itemArray["wiki"]= ["id"=>$w, "name"=>$wikiName];
                $itemArray["date"] = parseIWDate($row["timeStart"], $row["timeEnd"], $dateArray);
                $itemArray["year"] = date("Y", strtotime($row["reg_date"]));
                $resultArray[] = $itemArray;
                if ($counter == 22){break;}
            }
        }
        if ($mode == 0 && $info->luckying) {
            $itemArray["id"] = findRand($w)["id"];
            $wikiName = getWikiName($w, $info->database, $info->dbconn);
            $itemArray["name"] = "I'm Feeling Lucky";
            $itemArray["thumbnail"] = "/wikimgs/icons/random.jpg";
            $itemArray["genre"] = "Lore";
            $itemArray["NSFW"] = 0;
            $itemArray["date"] = "type something to generate new link";
            $itemArray["wiki"]= ["id"=>$w, "name"=>$wikiName];
            $itemArray["year"] = 2022;
            $resultArray[] = $itemArray;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($resultArray);
}
else if ($_GET["todo"] == "lucky"){
    $page = findRand($w);
    $id = $page["id"];
    $wikiName = getWikiName($id, $info->database, $info->dbconn);
    $redirect = $info->baseLink.parse2Url($wikiName)."/".$id."/".parse2Url($page["shortName"]);
    $info->killCache();
    header("Location:$redirect"); exit;
}




?>
