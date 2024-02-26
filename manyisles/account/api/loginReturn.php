<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

$user = new adventurer();
$user->killCache();


if (!isset($_GET["state"]) OR !isset($_GET["code"])){
    header("Location:/account/home?error=loginError");
}

$user->loginConfirm($_GET["code"], $_GET["state"]);


header("Location:/account/home");

?>