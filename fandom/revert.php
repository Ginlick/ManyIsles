<?php

if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();} else {$id = $_GET['id'];}
if (preg_match("/[^0-9]/", $_GET['dir'])==1){header("Location:/fandom/home");exit();}

$domain = 0;
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", $_GET['id'], 0, false, $domain, ["igRev" => true]);



$query = "SELECT MIN(v) FROM $gen->database WHERE id = $id";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_row()) {
        $minv = $row[0];
    }
}
if (!isset($minv)){exit();}

$base =  $gen->artRootLink.$_GET['id']."/";
if ($_GET['dir'] == 0){
    $query = "SELECT MAX(v) FROM $gen->database WHERE id = ".$_GET['id']. " AND status = 'active'";
    $firstrow = $gen->dbconn->query($query);
    while ($row = $firstrow->fetch_row()) {
        $maxv = $row[0];
        if ($maxv == $minv){header("Location:$base?i=cant");exit();}
    }
    $query = "UPDATE $gen->database SET status = 'reverted' WHERE id =".$_GET['id']." AND v = ".$maxv;
    if ($gen->dbconn->query($query)) {
        echo "<script>window.location.replace('$base?show=updated&cache=".rand()."')</script>";exit();
    }
}
else if ($_GET['dir'] == 1){
    $query = "SELECT MIN(v) FROM $gen->database WHERE id = $id AND status = 'reverted'";
    $firstrow = $gen->dbconn->query($query);
    while ($row = $firstrow->fetch_row()) {
        $nextv = $row[0];
    }
    $query = "UPDATE $gen->database SET status = 'active' WHERE id = $id AND v = ".$nextv;
    if ($gen->dbconn->query($query)) {
        echo "<script>window.location.replace('$base?show=updated&cache=".rand()."')</script>";exit();
    }
}
else if ($_GET['dir'] == 2){
    $query = "DELETE FROM $gen->database WHERE id = ".$_GET['id']." AND status = 'reverted'";
    if  ($gen->dbconn->query($query)){
        echo "<script>window.location.replace('$base?show=aged&cache=".rand()."')</script>";exit();
    }
}
header("Location:$base?show=fail&cache=1");exit();



?>
