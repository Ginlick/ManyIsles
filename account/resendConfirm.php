<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;

$conCode = "";
require_once($_SERVER['DOCUMENT_ROOT']."/account/newConfCode.php");

if (sendConfMail($conCode, $user->email)){
    header("Location: SignedIn?show=resent");
}
else {
    echo "Your email wasn't sent; it failed for some reason.";
}

$conn->close();

?>
