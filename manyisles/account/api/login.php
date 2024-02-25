<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

$user = new adventurer();
$user->killCache();

if ($user->check()) {
    $url = $user->loginURL();
    header("Location:$url");
    echo $url;
}
else {echo "user";}
//header("Location:/account/home");

?>