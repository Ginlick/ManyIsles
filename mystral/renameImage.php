<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

if (!isset($_POST['name'])){exit();} else {if (!checkRegger("cleanText", $_POST["name"])){exit();}}
if (!isset($_POST['value'])){exit();} else {if (!checkRegger("cleanText", $_POST["value"])){exit();}}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "mystral");
if ($gen->power < 3){exit();}

$name = $_POST['name'];
$value = substr($_POST['value'], 0, 50);

$query = "UPDATE images SET title = '$value' WHERE user = $gen->user AND name = '$name'";
if ($gen->dbconn->query($query)) {
  exit();
}
header("HTTP/1.1 500 Internal Server Error");

?>