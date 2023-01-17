
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php");
$Parsedown = new parser();

echo $Parsedown->parse($_GET["q"]);

?>
