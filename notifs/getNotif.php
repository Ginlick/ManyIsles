<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_notifs.php");
$notifsHere = true;

function getNotifNumber($accId, $reference) {
    global $notifconn;

    $query = "SELECT * FROM notifs_$accId WHERE reference = '$reference' ORDER BY id DESC LIMIT 0, 99";
    if ($result = $notifconn->query($query)) {
        return mysqli_num_rows($result);
    }
}



?>


