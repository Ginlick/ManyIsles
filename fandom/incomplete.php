
<?php
if (!isset($_GET["name"])){exit();}
if (preg_match("/[^0-9]/", $_GET['name'])==1){exit();}
if (preg_match("/[^0-1]{1}/", $_GET['d'])==1){exit();}

$articleId = $_GET["name"];
$direction = $_GET["d"];
$domain = 0;
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $w, false, $domain, false);


//actual stuff

$query = 'UPDATE '.$gen->database.' SET incomplete = '.$direction.' WHERE  id = '.$articleId;
if ($gen->dbconn->query($query)){
    header("Location:".$gen->artRootLink.$articleId."/article?cache=$direction&i=completed");exit;
}
else {
    echo $query;
}


?>