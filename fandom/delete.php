<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
$id = $_GET['id'];

$domain = 0;
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", $id, 0, false, $domain);
if ($gen->power < 5){header("Location:/fandom/home");exit();}

if ($gen->parentWiki != $id){
    $query = "UPDATE $gen->database SET root = $gen->parentWiki WHERE root = $id";
    if ($gen->dbconn->query($query)){
        $query = "DELETE FROM $gen->database WHERE id = $id";
        if ($gen->dbconn->query($query)){
            header("Location:".$gen->artRootLink.$gen->parentWiki."/home?i=deleted");exit();
        }
    }
}
else {
    header("Location:".$gen->artRootLink.$gen->parentWiki."/home");exit();
}

?>