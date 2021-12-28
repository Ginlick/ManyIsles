<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_GET['w'])==1){header("Location:/fandom/home");exit();}

require($_SERVER['DOCUMENT_ROOT']."/fandom/quickCheck.php");
$override = true;
require("slotChecker.php");
$redirect = "/fandom/wiki/".$_GET['id']."/article";

if ($_GET['w'] == 0){
    $query = "UPDATE pages SET status = 'suspended' WHERE id =".$_GET['id'];
    if ($conn->query($query)) {
    header("Location:$redirect?i=1");exit();
    }
}
else if ($_GET['w'] == 1){
    $query = "UPDATE pages SET status = 'active' WHERE id =".$_GET['id'];
    if ($conn->query($query)) {
    header("Location:$redirect?i=1");exit();
    }
}


?>