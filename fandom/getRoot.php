<?php
//

if (isset($_GET["q"])) {$q = $_GET["q"];if (preg_match("/[0-9]*/", $_GET["q"])!=1){exit();}} else {$q = "0";}
if (isset($page)){$q = $page;}
if (!isset($dontEcho)){$dontEcho = false;}
if (!function_exists("artUrl")){
    require_once($_SERVER['DOCUMENT_ROOT']."/wiki/urlParsing.php");
}
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/getWiki.php");

if (!isset($wikiName)){
    $wikiName = "wiki";
}
if (!isset($branch)){
    $domain = "fandom";
}
else {
    $domain = $branch;
}
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
class info {}
$info = new info;
equipDom($info, $domain);

$specs = ["return" => "seen"];
$resultArray = array();
$resultArray[0] = $q;
$resultArray = array_merge($resultArray, getWiki($q, $info->database, $info->dbconn, [], $specs));

$resultArray = array_reverse($resultArray);

/*print_r($resultArray);
echo count($resultArray);
echo $resultArray[3];*/

$rootLine = " - <a href='COOLLINK'>COOLNAME</a>";
if ($info->domain == "docs" OR $info->domain == "5eS"){
    $fullLine = "";
}
else if ($info->domain == "mystral"){
    $title = "Adventurer";
    $uname = "Hansfried";
    $query = 'SELECT title, uname FROM accountsTable WHERE id = '.$info->user;
    if ($max = $info->conn->query($query)){
        while ($gay = $max->fetch_assoc()){
            $title = $gay["title"];
            $uname = $gay["uname"];
        }
    }
    $fullLine = "<a href='/mystral/hub'>$title $uname</a>";
}
else {
    $fullLine = "<a href='/fandom/home'>Fandom</a>";
}
$ongoingName = "";
foreach ($resultArray as $pageId){
    if ($pageId == 0){continue;}
    $query = 'SELECT name, shortName, root FROM '.$info->database.' WHERE id = '.$pageId." ORDER BY v DESC LIMIT 0, 1";
    if ($max = $info->dbconn->query($query)){
        while ($gay = $max->fetch_assoc()){
            $currentName = $gay["shortName"];
            $ongoingName = $currentName;
            $root = $gay["root"];
            if ($currentName == ""){
                $currentName = $gay["name"];
            }
        }
    }
    if (!isset($currentName)){continue;}
    if ($info->domain != "fandom"){
        if (($info->domain == "docs" OR $info->domain == "5eS") AND $root == 0) {
            $currentLine = str_replace("-", "", $rootLine);
        }
        else {$currentLine = $rootLine;}
        $currentLine = str_replace("COOLLINK", artUrl($info->artRootLink, $pageId, $currentName), $currentLine);
    }
    else {
        $currentLine = str_replace("COOLLINK", artUrl($info->artRootLink, $pageId, $currentName), $rootLine);
    }
    $currentLine = str_replace("COOLNAME", $currentName, $currentLine);
    $fullLine .= $currentLine;
}
if (!$dontEcho){
    echo $fullLine;
}


?>
