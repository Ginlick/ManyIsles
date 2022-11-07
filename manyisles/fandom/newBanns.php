<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

if (preg_match("/[^0-9]/", $_POST['wId'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_POST['dom'])==1){header("Location:/fandom/home");exit();}
$bannList = str_replace("'", '', $_POST['bannList']);
$bannList = str_replace("<", '', $bannList);

$parentWiki = $_POST['wId'];
$domain = $_POST['dom'];

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $parentWiki, false, $domain);
$conn = $gen->dbconn;
if ($gen->power < 3){header("Location:$gen->artRootLink/$gen->parentWiki/home");exit();}


$query="SELECT id FROM wiki_settings WHERE id = '$gen->WSet' ";
$result =  $conn->query($query);
if ($result->num_rows == 0){
    $query = "INSERT INTO wiki_settings (id, mods) VALUES ('$gen->WSet', '$gen->user')"; $conn->query($query);
}

//work 


$query = 'UPDATE wiki_settings SET banners = \'bannList\' WHERE id = "'.$gen->WSet.'"';

$query = str_replace("bannList", $bannList, $query);

echo $query;

if ($conn->query($query)){
    header("Location:".$gen->baseLink."wsettings?i=bigup&w=$parentWiki");
}
else {
    echo "Error.";
}


?>