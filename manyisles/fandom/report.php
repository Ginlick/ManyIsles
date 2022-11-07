<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^a-z]/", $_GET['w'])==1){header("Location:/fandom/home");exit();}

$undo = false;
$id = $_GET['id'];
if ($_GET['w'] == "undo"){
    $undo = true;
}



$doSecurity = true;
require($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");

require("slotChecker.php");
if ($undo == false){  $query = sprintf("INSERT INTO reported (id, uid) VALUES (%s, %s)", $_GET['id'], $uid);}
else {$query = "DELETE FROM reported WHERE id = ".$_GET['id'];}
if ($conn->query($query)) {
    if ($undo == true){header("Location:f.php?i=1&id=".$_GET['id']);exit();}
    header("Location:f.php?i=reported&id=".$_GET['id']);exit();
}




?>