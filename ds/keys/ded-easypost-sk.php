<?php
if ($_SERVER['DOCUMENT_ROOT'] == "/users/aufregendetage/Sites") {
    $easypost_sk = 'EZTK223e03afeb6f4b67a5da3e907c32739dBieJmP5cmUvPUhTqZaeKEw';
}
else if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
    $easypost_sk = 'EZAK223e03afeb6f4b67a5da3e907c32739do4LagoKSMMT2vZis74wP3A';
}
else {
    mail ("pantheon@manyisles.ch", "Attempt to Get easypost key", $_SERVER['DOCUMENT_ROOT']);
}
?>