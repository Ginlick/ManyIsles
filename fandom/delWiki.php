<?php

if (!isset($_POST["WIKI"]) OR !isset($_POST["psw"])){exit();}

$wiki = str_replace("/[^0-9]", "", $_POST["WIKI"]);
$psw = $_POST["psw"];
$domain = "mystral";

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $wiki, false, $domain);
if ($gen->power < 5){header("Location:".$gen->homelink);exit();}
if (!$gen->userMod->checkInputPsw($psw)){header("Location:".$gen->baseLink."wsettings?w=$wiki&i=badpsw");exit();}

function doSlaughter($id) {
  global $gen;
  echo $id." ";
  $query = "DELETE FROM $gen->database WHERE id = $id";
  if ($gen->dbconn->query($query)){
    $query = "SELECT id FROM $gen->database WHERE root = $id";
    if ($result = $gen->dbconn->query($query)) {
      while($row = $result->fetch_assoc()) {
        $newId = $row["id"];
        doSlaughter($newId);
      }
    }
  }
}
doSlaughter($wiki);

header("Location:".$gen->homelink."?i=grpdeleted");

?>
