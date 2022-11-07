<?php
//for non-dsEngine pages (discontinued)

if (!class_exists("dsEngine")){
  require($_SERVER['DOCUMENT_ROOT']."/ds/g/dsEngine.php");
}
$myEngine = new dsEngine;

if (!function_exists ("alertStock")) {
    function alertStock($stock){
      global $myEngine;
      return $myEngine->alertStock($stock);
    }
}
if (!function_exists ("prodStatSpan")) {
    function prodStatSpan($status){
      global $myEngine;
      return $myEngine->alertStatus($status);
    }
}
if (!function_exists ("hasAnyStock")) {
    function hasAnyStock($specs, $baseStock){
      global $myEngine;
      return $myEngine->hasAnyStock($specs, $baseStock);
    }
}
?>
