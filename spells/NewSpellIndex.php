<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "spells", ["newMinP"=>1]);
$gen->spells = new spellGen($gen);

$name = "New Index";$visibility = 1;$index = null;
if (isset($_GET["wikiName"])){$name = purate($_GET["wikiName"], "wikiName");}
if (isset($_GET["visibility"])){$visibility = purate($_GET["visibility"], "posint");}
if (isset($_GET["index"])){$index = purate($_GET["index"], "posint");}
if (!$gen->power > 4){$visibility = 1;}
if ($gen->domainSpecs["totalIndexes"]>=$gen->mystData["indexes"]){
  $gen->redirect("list?i=indexFull");
}

$query = "SELECT id FROM wiki_settings ORDER BY id DESC LIMIT 1";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $id = $row["id"] + 1;
    }
}
$query = "SELECT id FROM spells ORDER BY id DESC LIMIT 1";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $spid = $row["id"] + 1;
    }
}

$query = 'INSERT INTO wiki_settings (id, wikiName, mods, visibility) VALUES ('.$id.', "'.$name.'", '.$gen->user.', '.$visibility.')';
if ($gen->dbconn->query($query)){
  if ($index != null){
    $indSpells = $gen->spells->dic("AND a.parentWiki = ".$gen->parentWiki, false);
    foreach ($indSpells as $spell){
      $details = json_encode($spell);
      $authors = $gen->user;
      $name = $spell["Name"];
      $query = "INSERT INTO spells (id, v, parentWiki, name, details, authors) VALUES ($spid, 0, $id, \"".$name."\", '".$details."', '$authors')";
      $gen->dbconn->query($query);
      $spid++;
    }
  }
  header("Location:list?w=$id&i=indexCreated");
  exit();
}

echo "Error.";




?>
