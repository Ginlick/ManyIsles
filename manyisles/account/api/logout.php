<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

$user = new adventurer();
$user->killCache();

$return = "";
if (isset($_GET["return_address"])) {$return = $_GET["return_address"];}

header("Location:".$user->logoutURL($return));

?>