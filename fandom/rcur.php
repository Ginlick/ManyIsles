<?php
if (preg_match("/[^0-9]/", $_GET['who'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^a-z]/", $_GET['w'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_GET['wiki'])==1){header("Location:/fandom/home");exit();}

$undo = false;
$id = $_GET['who'];
$wiki = $_GET['wiki'];
if ($_GET['w'] == "undo"){
    $undo = true;
}


if (!$undo){
    $doSecurity = true;
    require($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");
    require("slotChecker.php");

    $query = "INSERT INTO requests (requestee, domain, request) VALUES ($id, 'wf$wiki', 'auth')";
    if ($conn->query($query)) {
        header("Location:/fandom/wiki/$wiki/home?i=reqCur"); exit();
    }
}
else {
    require($_SERVER['DOCUMENT_ROOT']."/fandom/quickCheck.php");
    $query = "DELETE FROM requests WHERE id = $id";
    if ($conn->query($query)) {
        header("Location:/fandom/wsettings.php?w=$wiki&i=reqCurDel"); exit();
    }
}





?>