<?php

if(!isset($_COOKIE["loggedIn"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$id = $_COOKIE['loggedIn'];

$getMailRow = "SELECT * FROM accountsTable WHERE id = $id";
$mailresult =  $conn->query($getMailRow);
while ($row = $mailresult->fetch_assoc()) {
    $checkpsw = $row["password"];
    $to = $row["email"];
}

$redirect = "SignedIn.php?show=emailWrongPassword";
include("../Server-Side/checkPsw.php");

$conCode = "";
require_once($_SERVER['DOCUMENT_ROOT']."/account/newConfCode.php");


if (sendConfMail($conCode, $to)){
    header("Location: SignedIn.php?show=resent");
}
else {
    echo "Your email wasn't sent; it failed for some reason.";
}

$conn->close();

?>
