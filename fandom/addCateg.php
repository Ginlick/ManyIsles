
<?php
if (!isset($_GET["q"])){exit();}
if (!isset($_GET["w"])){exit();}


$q = $_GET["q"];
$q = str_replace('"', '', $q);
$q = str_replace('<', '', $q);
if (preg_match("/^[0-9]*$/", $_GET["w"])!=1){exit();} else {$w = $_GET["w"];}

$domain = "fandom";
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $w, false, $domain, false);

$additive = ""; $moreVs = ""; $moreVIs = "";
if ($gen->domain == "mystral"){$additive = " AND user = ".$gen->user; $moreVs = ", user"; $moreVIs = ', "'.$gen->user.'"';}

$query = 'SELECT * FROM wikicategories WHERE name = "'.$q.'" AND wiki = '.$w.$additive;
$result = $gen->dbconn->query($query);
if ($result->num_rows == 0 AND $q != "") {
        $query ='INSERT INTO wikicategories (name, wiki'.$moreVs.') VALUES ("'.$q.'", '.$w.$moreVIs.')';
        if ($gen->dbconn->query($query)){
            echo "success";
        }
}
else {
    echo "success";
}


?>