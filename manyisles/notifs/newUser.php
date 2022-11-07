<?php
//requires: $notifconn, $accId
$query = "SELECT id FROM notifs_$accId ORDER BY id DESC LIMIT 0, 1";
if (!$notifconn->query($query)){
    $query = "CREATE TABLE notifs_$accId (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(99) DEFAULT 'gen',
    reference VARCHAR(999) NOT NULL,
    redirect TEXT NOT NULL,
    addInfo TEXT,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
    ";
    $notifconn->query($query);
}







?>


