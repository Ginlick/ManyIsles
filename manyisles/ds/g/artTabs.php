<?php
//$itemNumArray, $conn; js funcs: showView, pruchase; basket Form

//for non-dsEngine pages (discontinued)

if (!class_exists("dsEngine")){
  require($_SERVER['DOCUMENT_ROOT']."/ds/g/dsEngine.php");
}
$myEngine = new dsEngine;

function makeArtTab($row, $itemNumArray = [], $showNoStock = false){
  global $myEngine;
  echo $myEngine->makeArtTab($row, $itemNumArray, $showNoStock);
}

?>
