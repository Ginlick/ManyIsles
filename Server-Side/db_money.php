<?php

if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
    $servername = "localhost:3306";
    $username = "treasurer";
    $password = "Pantheon4Money";
    $dbname = "manyisle_money";
}
else {
    $servername = "localhost";
    $username = "aufregendetage";
    $password = "vavache8810titigre";
    $dbname = "money";
}
$moneyconn = new mysqli($servername, $username, $password, $dbname);


?>
