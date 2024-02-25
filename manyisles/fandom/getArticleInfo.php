<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/parseIWDate.php");
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

$gen = new gen("view", $q, 0, false, $domain);
$dateArray = getDateArray($gen->conn, $gen->parentWiki);

$itemArray["id"] = $gen->page;
$itemArray["name"] = $gen->article->name;
$itemArray["thumbnail"] = getArtImage($gen->article->articleImg, $gen->article->banner, $gen->article->NSFW, $gen);
$itemArray["genre"] = $gen->article->cate;
$itemArray["NSFW"] = $gen->article->NSFW;
$itemArray["date"] = parseIWDate($gen->article->timeStart, $gen->article->timeEnd, $dateArray);
$itemArray["cleanlink"] = $gen->artLink;

$status = $gen->article->status;
$itemArray["status"] = $gen->article->status; if ($gen->article->incomplete == 1 && $gen->article->status != "suspended"){$itemArray["status"] == "incomplete";}

$body = substr($gen->article->body, 0, 500);
$body = txtUnparse($body, 0);
$itemArray["jacob"] = $body;
echo json_encode($itemArray);


?>
