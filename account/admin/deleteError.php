<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer;
$user->modcheck(4);
$conn = $user->conn;

$id = $user->purify($_GET["id"], "number");
$query = "UPDATE errors SET status = 0 WHERE id = '$id'";
$user->conn->query($query);
$user->go("errors");

?>
