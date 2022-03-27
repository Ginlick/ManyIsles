<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/accStat.php");
header('Content-Type: application/json');

$base = new useBase;
$base->construct(); $conn = $base->conn;
$base->killCache();

if (isset($_GET["name"])) {if (preg_match("/^[a-zA-Z0-9 ]*$/", $_GET["name"])!=1){echo "[]";exit();} $name = $_GET["name"];} else {exit();}
if (isset($_GET["w"])) {if (preg_match("/^[0-9]*$/", $_GET["w"])!=1){echo "[]";exit();} $wiki = $_GET["w"];} else {exit();}

$itemArray = [];

$query = "SELECT * FROM poets WHERE uname = '$name' LIMIT 1";
if ($max = $conn->query($query)){
    while ($row = $max->fetch_assoc()){
        $itemArray["status"] = getAccStat($conn, $row["id"], $wiki, true);
        $itemArray["name"] = $name;
        $itemArray["edits"] = $row["edits"];
        $itemArray["id"] = $row["id"];
    }
}

echo json_encode($itemArray);

?>
