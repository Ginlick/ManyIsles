<?php
//DISCONTINUED: use allBase->addConn() instead

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
$base = new useBase;
$moneyconn = $base->addConn("money");

?>
