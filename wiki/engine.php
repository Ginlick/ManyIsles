<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/urlParsing.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/parseIWDate.php");


if (!isset($doSecurity)){
    $doSecurity = false;
}
if ($doSecurity) {
    if(!isset($_COOKIE["loggedIn"])){echo "<script>window.location.replace('/account/Account.html?error=notSignedIn')</script>";exit();}
    if(!isset($_COOKIE["loggedP"])){echo "<script>window.location.replace('/account/Account.html?error=notSignedIn')</script>";exit();}
    require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

    $uid = $_COOKIE["loggedIn"];
    if (preg_match("/^[0-9]+$/", $uid)!=1) {setcookie("loggedIn", "", time() -3600, "/");echo "<script>window.location.replace('/account/Account.html?error=notSignedIn')</script>";exit();}

    $query = "SELECT password FROM accountsTable WHERE id = ".$uid;
        if ($firstrow = $conn->query($query)) {
        while ($row = $firstrow->fetch_assoc()) {
            $checkpsw = $row["password"];
        }
    }

    require($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw.php");
}

?>