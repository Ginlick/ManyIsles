<?php
if (!isset($notifconn)){
    if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
        $servername = "localhost:3306";
        $username = "notifier";
        $password = "wNnQeD2FVaEw68WZYwUy!k";
        $dbname = "manyisle_notifs";
    }
    else if ($_SERVER['REMOTE_ADDR']=="::1"){
        $servername = "localhost";
        $username = "aufregendetage";
        $password = "vavache8810titigre";
        $dbname = "notifs";
    }
    $notifconn = new mysqli($servername, $username, $password, $dbname);
}

if (!isset($localAccount)){$localAccount = false;}

if ($localAccount) {
    if(!isset($_COOKIE["loggedIn"])){header("Location:/account/Account.html?error=notSignedIn");exit();}
    if(!isset($_COOKIE["loggedP"])){header("Location: /account/Account.html?error=notSignedIn");exit();}
    require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

    $uid = $_COOKIE["loggedIn"];
    if (preg_match("/^[0-9]+$/", $uid)!=1) {setcookie("loggedIn", "", time() -3600, "/");header("Location: /account/Account.html?error=notSignedIn");exit();}

    $query = "SELECT * FROM accountsTable WHERE id = ".$uid;
        if ($firstrow = $conn->query($query)) {
        while ($row = $firstrow->fetch_assoc()) {
          $uname = $row["uname"];
          $checkpsw = $row["password"];
        }
    }
    require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw.php");
}

?>
