<?php
//for non-dsEngine pages (discontinued)

if (!class_exists("dsEngine")){
  require($_SERVER['DOCUMENT_ROOT']."/ds/g/dsEngine.php");
}
$myEngine = new dsEngine;

if (!function_exists ("clearImgUrl")) {
    function clearImgUrl($image) {
      global $myEngine;
      return $myEngine->clearImgUrl($image);
    }
}
if (!function_exists ("makeHuman")) {
    function makeHuman($ordiprice) {
      global $myEngine;
      return $myEngine->makeHuman($ordiprice);
    }
}
if (!function_exists ("linki")) {
    function linki($id, $link, $name = "item") {
      global $myEngine;
      return $myEngine->linki($id, $link, $name);
    }
}
function detailsUL($specs, $codes = []){
  global $myEngine;
  return $myEngine->detailsUL($specs, $codes);
}
function detailsLine($name, $specs){
  global $myEngine;
  return $myEngine->detailsLine($specs, $name);
}

?>
