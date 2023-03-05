<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/parseIWDate.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/getWiki.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/parse.php");
header('Content-Type: application/json');

if (!isset($local)) {
  if (isset($_GET["id"])) {if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){echo "[]";exit();} $q = $_GET["id"];} else {exit();}
  if (isset($_GET["dom"])){
      if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
  }
  else {
      $domain = 0;
  }
}
else {
  //want $q
  if (!isset($domain)){
      $domain = 0;
  }
}


class gay {} $info = new gay;
equipDom($info, $domain);
$parse = new parse($info->dbconn, $q);
$parentWiki = getWiki($q, $info->database, $info->dbconn);

$dateArray = getDateArray($info->dbconn, $parentWiki, $domain);

$itemArray = [];
$query = "SELECT * FROM $info->database WHERE id = ".$q." ORDER BY v DESC LIMIT 0, 1";
if ($max = $info->dbconn->query($query)){
    while ($row = $max->fetch_assoc()){
        $itemArray["id"] = $row["id"];
        $itemArray["name"] = $row["name"];
        $itemArray["thumbnail"] = getArtImage($row["sidetabImg"], $row["banner"], $row["NSFW"], $info);
        $itemArray["genre"] = $row["cate"];
        $itemArray["NSFW"] = $row["NSFW"];
        $itemArray["date"] = parseIWDate($row["timeStart"], $row["timeEnd"], $dateArray);

        $itemParWik = getWiki($row["id"], $info->database, $info->dbconn);
        $itemParWikName = getWikiname($itemParWik, $info->database, $info->dbconn);
        $itemArray["cleanlink"] = $info->baseLink.parse2Url($itemParWikName)."/".$row["id"]."/".parse2Url($row["shortName"]);

        if ($row["incomplete"]==1 AND $row["status"] != "suspended"){$itemArray["status"] = "incomplete";}
        else {$itemArray["status"] =  $row["status"];}

        $jacob = $parse->bodyParser(substr($row["body"], 0, 2000));
        if (str_contains($jacob, "{")) {
            $jacob = substr($jacob, 0, strpos($jacob, "{"));
        }
        else if (str_contains($jacob, "[")) {
            $jacob = substr($jacob, 0, strpos($jacob, "["));
        }
        else {
            $jacob = substr($jacob, 0, 500);
        }
        $itemArray["jacob"] = $jacob;
    }
}

echo json_encode($itemArray);

?>
