
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/Parsedown.php");
$Parsedown = new Parsedown();

echo $Parsedown->text($_GET["q"]);

?>