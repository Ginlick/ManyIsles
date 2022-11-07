<?php

if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:/dl/Goods.php");exit();}
if (isset($_GET["t"])){if (preg_match("/^[a-z]*$/", $_GET["t"])!=1){header("Location:/dl/Goods.php");exit();}}

header("Location:/dl/item/".$_GET["id"]."/");


?>
