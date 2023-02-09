<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_GET['dir'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_GET['v'])==1){header("Location:/fandom/home");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", $_GET['id'], 0, false, "fandom", ["igRev" => true]);
$gen->killCache();

if ($_GET['dir'] == 0){
    $query = "UPDATE pages SET canon = 0 WHERE id =".$_GET['id']." AND v =".$_GET['v'];
    if ($gen->conn->query($query)) {
    header("Location:f.php?show=updated&id=".$_GET['id']);exit();
    }
}
if ($_GET['dir'] == 1){
    $query = "UPDATE pages SET canon = 1 WHERE id =".$_GET['id']." AND v =".$_GET['v'];
    if ($gen->conn->query($query)) {
    header("Location:f.php?show=updated&id=".$_GET['id']);exit();
    }
}


?>