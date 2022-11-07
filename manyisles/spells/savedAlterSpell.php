<?php
if (isset($_GET['list'])){$code = preg_replace("/[^0-9a-zA-Z]/", "", $_GET['list']);}else {header("Location:/spells/list");exit();}
if (isset($_GET['id'])){$id = preg_replace("/[^0-9]/", "", $_GET['id']);}else {header("Location:/spells/list");exit();}
if (isset($_GET['dir'])){$dir = preg_replace("/[^0-9]/", "", $_GET['dir']);}else {$dir = 0;}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "spells", ["newMinP"=>1]);
$gen->spells = new spellGen($gen);
$isowner = false;

$spellList = [];
$query = "SELECT * FROM spelllists WHERE code = '$code'";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $spellList = json_decode($row["list"], true);
      if ($row["user"]==$gen->user){$isowner = true;}
    }
}
if (!$isowner){$gen->redirect("/spells/list");}

if ($dir == 1){
  if (!in_array($id, $spellList)){
    $spellList[] = $id;
  }
}
else {
  if (($key = array_search($id, $spellList)) !== false) {
    unset($spellList[$key]);
  }
}

$spellList = json_encode($spellList);
$query = "UPDATE spelllists SET list = '".$spellList."' WHERE code = '$code' ";
//echo $query; exit();
if ($gen->dbconn->query($query)){
  if ($dir == 1){
    echo json_encode($gen->spells->dic("AND a.id = ".$id)[0]);
  }
  else {
    echo "Success.";
  }
  exit;
}
echo "Error.";




?>
