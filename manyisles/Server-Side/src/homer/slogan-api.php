<?php
require $_SERVER['DOCUMENT_ROOT']."/Server-Side/src/homer/homer.php";
$homer = new homer();
$homer->parser->killCache();
$slogan = $homer->giveSlogan();
echo $slogan;
?>
