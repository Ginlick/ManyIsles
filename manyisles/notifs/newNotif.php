<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_notifs.php");

function newNotif($accId, $domain, $reference, $redirect) {
    global $notifconn;
    include($_SERVER['DOCUMENT_ROOT']."/notifs/newUser.php");
    echo "checked<br>";

    $query = 'INSERT INTO notifs_'.$accId.' (domain, reference, redirect) VALUES ("HEREDOMAIN", "HEREREF", "HEREREDIR")';
    $query = str_replace("HEREDOMAIN", $domain, $query);
    $query = str_replace("HEREREF", $reference, $query);
    $query = str_replace("HEREREDIR", $redirect, $query);

    $notifconn->query($query);
}



?>


