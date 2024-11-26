<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

$user = new adventurer();
$user->killCache();

if ($user->check()) {
    if ($url = $user->loginURL()){
        header("Location:$url");
        exit;
    }
}

header("Location:/account/home?error=loginError");

?>