<?php
if (isset($_GET['code'])){$code = preg_replace("/[^0-9a-zA-Z]/", "", $_GET['code']);}else {header("Location:/spells/list");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "spells", ["newMinP"=>1]);
$gen->spells = new spellGen($gen);
$isowner = false;

$spellList = [];
$query = "SELECT * FROM spelllists WHERE code = '$code'";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      if ($row["user"]==$gen->user){$isowner = true;}
    }
}
if ($isowner){
  $query = "DELETE FROM spelllists WHERE code = '$code' ";
  //echo $query; exit();
  if ($gen->dbconn->query($query)){
    header("Location:/spells/list?i=listDeleted");
  }
  echo "Error.";
}





?>
