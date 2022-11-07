<?php
if (isset($_POST['code'])){$code = preg_replace("/[^0-9a-zA-Z]/", "", $_POST['code']);}else {header("Location:/spells/list");exit();}
if (isset($_POST['spellToAdd'])){$module = preg_replace("/[^0-9]/", "", $_POST['spellToAdd']);}else {header("Location:/spells/list");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "spells", ["newMinP"=>1]);
$gen->spells = new spellGen($gen);
$isowner = false;

$modCodeName = ""; foreach ($gen->modules as $key => $mod){if ($mod["code"]==$module){$modCodeName = $key;}}

$index = 1;$modList = [];
$query = "SELECT * FROM spelllists WHERE code = '$code'";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $index = $row["wiki"];
      $modList = json_decode($row["modules"], true);
      if ($row["user"]==$gen->user){$isowner = true;}
    }
}
if (!$isowner){$gen->redirect("/spells/list?view=saved");}
if ($modList == null){$modList = [];}

if ($modCodeName != "" AND !in_array($modCodeName, $modList)) {
  $modList[] = $modCodeName;

  $modList = json_encode($modList);
  $query = "UPDATE spelllists SET modules = '$modList' WHERE code = '$code' ";
  //echo $query; exit();
  if ($gen->dbconn->query($query)){
    header("Location:/spells/saved/$code?i=modAdded"); exit();
  }
  echo "Error.";
}
header("Location:/spells/saved/$code?i=modNotAdded");






?>
