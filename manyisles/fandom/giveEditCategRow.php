
<?php

if (!isset($_GET["q"])){exit();}
if (preg_match("/[0-9,]*/", $_GET["q"])!=1){exit();} else {$w = $_GET["q"];}
$domain = 0;
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 0, false, $domain);

$categArray = explode(",", $w);
$categRow = "";
foreach ($categArray as $link){
    if ($link == ""){continue;}
    $name = "";
    $query = "SELECT name FROM wikicategories WHERE id = ".$link; if ($gen->domain == "mystral"){$query .= " AND user = $gen->user";}
    if ($max = $gen->dbconn->query($query)) {
        while ($gay = $max->fetch_row()){
            $name = $gay[0];
        }
    }
    if ($name != ""){
        if ($categRow == ""){
            $categRow = "<span onclick='removeCateg(". $link . ")' id='removableCateg".$link."'>". $name ."</span>";

        }
        else {
            $categRow = $categRow."<span onclick='removeCateg(". $link . ")' id='removableCateg".$link."'>, ". $name ."</span>";
        }
    }
}

echo $categRow;


?>