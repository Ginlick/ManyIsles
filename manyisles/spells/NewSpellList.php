<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 0, false, "spells");
$gen->spells = new spellGen($gen);
if (!$gen->userMod->check(true)) {
  $gen->redirect("/spells/index");
}
if ($gen->domainSpecs["totalLists"]>=$gen->mystData["lists"]){
  $gen->redirect("list?i=indexFull");
}

$class = "other"; $race = "other"; $deity = "other";$index = 1;$kickMyths = false;
if (isset($_POST["class"])){$class=purate($_POST["class"], "basic2");}
if (isset($_POST["race"])){$race=purate($_POST["race"], "basic2");}
if (isset($_POST["deity"])){$deity=purate($_POST["deity"], "basic2");}
if (isset($_POST["index"])){$index=purate($_POST["index"], "posint");}
if (isset($_POST["kickMyths"])){if ($_POST["kickMyths"]=="on"){$kickMyths = true;}}

if (!isset($gen->spells->usableIndexes[$index])){$index = 1;}
$code = $gen->generateRandomString(10);

$spellList = [];
$dic = $gen->spells->dic("AND a.parentWiki = $index");
foreach ($dic as $details) {
  if ($kickMyths AND $details["Level"]>9){continue;}
  if (isset($details["Source"]) AND $details["Source"] != ""){continue;}
  $classList = explode(", ", $details["Class"]);
  if ($details["Class"] == "Any" OR in_array($class, $classList) OR in_array("Any ".$race, $classList) OR in_array("Any Acolyte of ".$deity, $classList)){
    array_push($spellList, $details["id"]);
  }
}
$spellList = json_encode($spellList);

$name = str_replace("'", "", $race." ".$class." of ".$deity);

$query = "INSERT INTO spelllists (user, code, wiki, name, list) VALUES ($gen->user, '$code', $index, '$name', '$spellList')";
//echo $query; exit();
if ($gen->dbconn->query($query)) {
    header("Location: saved/".$code);exit();
}
echo "Error.";



?>
