<?php

if (isset($_GET["id"]) && preg_match("/^[0-9]$/", $_GET['id'])==1) {
    header("Location: /fandom/wiki/".$_GET["id"]."/article");
}
else {
    header("Location:/fandom/Karte-Caedras/2/home");
}


?>