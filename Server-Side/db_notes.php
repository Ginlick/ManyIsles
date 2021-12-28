<?php
if (!function_exists("giveNonn")){
    function giveNonn() {
        if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
            $servername = "localhost:3306";
            $username = "poet";
            $password = "7Ap_s8s9222we!";
            $dbname = "manyisle_notes";
        }
        else if ($_SERVER['REMOTE_ADDR']=="::1"){
            $servername = "localhost";
            $username = "aufregendetage";
            $password = "vavache8810titigre";
            $dbname = "notes";
        }
        return new mysqli($servername, $username, $password, $dbname);
    }
}

?>