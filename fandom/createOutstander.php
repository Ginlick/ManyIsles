
<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

$articleName = $_GET["name"];
$articleWiki = $_GET["wiki"];

if (!isset($_GET["name"]) OR $articleName == "" OR !isset($_GET["wiki"])){exit();}
if (!checkRegger("wikiName", $articleName)){echo "ah";exit();}
if (preg_match("/^[0-9]+$/", $articleWiki)!=1){header("Location:/fandom/home?x=4");exit();}
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]+$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}
else {
    $domain = 0;
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $articleWiki, false, $domain);
echo "currentSlot:".$gen->slotAt;

if ($gen->canedit){
    $query = "SELECT max(id) FROM $gen->database";
    $firstrow = $gen->dbconn->query($query);
    while ($row = $firstrow->fetch_row()) {
        $id = $row[0]+1;
    }
    if (!isset($id)){exit();}

    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, status, cate) VALUES ('.$id.', 0, "'.$articleName.'", "'.$articleName.'",  "default", '.$articleWiki.', "outstanding", "'.$gen->defaultGenre.'")';
    $gen->dbconn->query($query);
}


/*
if (){
    echo $id.", ".$articleName;
}
else {
    echo $query;
}*/


?>