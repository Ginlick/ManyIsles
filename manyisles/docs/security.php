<?php
// $conn, $parentWiki, $id

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

if (isset($_COOKIE["loggedIn"])) {
    $uid = $_COOKIE["loggedIn"];
    if (preg_match("/^[0-9]+$/", $uid)!=1) {setcookie("loggedIn", "", time() -3600, "/");header("Location: /account/Account.html?error=notSignedIn");exit();}

    $query = "SELECT password FROM accountsTable WHERE id = ".$uid;
        if ($firstrow = $conn->query($query)) {
        while ($row = $firstrow->fetch_assoc()) {
            $checkpsw = $row["password"];
        }
    }

    require($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw.php");

    $super = false;
    $query="SELECT banned, super FROM poets WHERE id = ".$uid;
    $result =  $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        if ($row["super"]==1){$super = true;}
        if ($row["banned"]==1){$super = 0;}
    }
}


?>