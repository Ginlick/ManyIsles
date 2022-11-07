<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:../f.html");exit();}
if (preg_match("/[^0-9]/", $_GET['dir'])==1){header("Location:../f.html");exit();}
if (preg_match("/[^0-9]/", $_GET['v'])==1){header("Location:../f.html");exit();}

require($_SERVER['DOCUMENT_ROOT']."/fandom/quickCheck.php");

if ($_GET['dir'] == 0){
    $query = "UPDATE pages SET canon = 0 WHERE id =".$_GET['id']." AND v =".$_GET['v'];
    if ($conn->query($query)) {
    header("Location:f.php?show=updated&id=".$_GET['id']);exit();
    }
}
if ($_GET['dir'] == 1){
    $query = "UPDATE pages SET canon = 1 WHERE id =".$_GET['id']." AND v =".$_GET['v'];
    if ($conn->query($query)) {
    header("Location:f.php?show=updated&id=".$_GET['id']);exit();
    }
}


?>