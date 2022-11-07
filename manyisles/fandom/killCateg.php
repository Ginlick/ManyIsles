
<?php
if (!isset($_GET["id"])){exit();}
if (!isset($_GET["w"])){exit();}


$id = str_replace("/[0-9]*$/", "", $_GET["id"]);
$w = str_replace("/[0-9]*$/", "", $_GET["w"]);

$domain = "fandom";
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])==1) {$domain = $_GET["dom"];}
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $w, false, $domain, false);

$query = "DELETE FROM wikicategories WHERE id = $id AND wiki = $w";
if ($gen->domain == "mystral"){
  $query .= " AND user = ".$gen->user;
}
echo $query;
if ($gen->dbconn->query($query)){
  header("Location:".$gen->baseLink."wsettings?w=$w&u=$gen->user&i=catdel");
}
echo "error";

?>
