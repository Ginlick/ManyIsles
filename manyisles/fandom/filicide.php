<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
$id = $_GET['id'];

$domain = 0;
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", $id, 0, false, $domain);

$base =  $gen->artRootLink.$_GET['id']."/".$gen->article->shortName;
$query = "DELETE a FROM $gen->database a, $gen->database b 
WHERE a.v < b.v AND a.id = b.id AND a.id = $id";
echo $query;
if ($gen->dbconn->query($query)){
    $deletedNum = $gen->dbconn->affected_rows;
    header("Location:$base?del=$deletedNum");
}

?>