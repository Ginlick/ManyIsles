<?php
//discontinued
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/dsEngine.php");
$ds = new dsEngine;

function doSideBasket() {
  global $ds;
  echo $ds->sideBasket();
}
$basketed = $ds->basketed;

?>
