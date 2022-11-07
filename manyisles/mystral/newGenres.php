<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

if (preg_match("/[^0-9]/", $_POST['wId'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_POST['dom'])==1){header("Location:/fandom/home");exit();}
$bannList = str_replace("'", '', $_POST['genreList']);
$bannList = str_replace("<", '', $bannList);

$parentWiki = $_POST['wId'];
$domain = $_POST['dom'];

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $parentWiki, false, $domain);
if ($gen->power < 3 OR !$gen->changeableGenre){header("Location:$gen->artRootLink/$gen->parentWiki/home");exit();}


$query="SELECT id FROM wiki_settings WHERE id = '$gen->WSet' ";
$result =  $gen->dbconn->query($query);
if ($result->num_rows == 0){
    $query = "INSERT INTO wiki_settings (id, mods) VALUES ('$gen->WSet', '$gen->user')"; $gen->dbconn->query($query);
}

//work 


$query = 'UPDATE wiki_settings SET genres = \'bannList\' WHERE id = "'.$gen->WSet.'"';

$query = str_replace("bannList", $bannList, $query);

echo $query; 

if ($gen->dbconn->query($query)){
    header("Location:".$gen->baseLink."wsettings?i=bigup&w=$parentWiki");
}
else {
    echo "Error.";
}


?>