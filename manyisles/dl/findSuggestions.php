
<?php
$genre = 0; $subgenre = "";
if (isset($_GET["c"])) {$subgenre = $_GET["c"];if (preg_match("/[a-z]*/", $_GET["c"])!=1){exit();}} else {$cate = "";}
if (isset($_GET["t"])) {$genre = $_GET["t"];if (preg_match("/[a-z]*/", $_GET["t"])!=1){exit();}} else {$type = "module";}
//if (isset($_GET["z"])) {$checkSupport = $_GET["z"];if (preg_match("/[0-9]*/", $_GET["z"])!=1){exit();}} else {$checkSupport = 0;}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once("global/engine.php");
$dl = new dlengine($conn);

$q = $_GET["q"];
$q = str_replace('"', '', $q);

$resultArray = $dl->results(["query"=>$q, "genre"=>$genre, "subgenre"=>$subgenre], "array", 22);

header('Content-Type: application/json');
echo json_encode($resultArray);


?>
