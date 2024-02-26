<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

$user = new adventurer();
$user->killCache();

$user->logoutLocal();

$return = "/account/home?error=signIn";
if (isset($_GET["return_address"])){
    $url = urldecode($_GET["return_address"]);
    if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {
        $return = $url;
    }
}

header("Location:$return");

?>