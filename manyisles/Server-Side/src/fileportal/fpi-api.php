<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/src/fileportal/fpi-engine.php");
$returnObj = ["error"=>"An error occurred."];

if (isset($_POST["code"])){
  if ($fpi = new fpi($_POST["code"], false)){
    if (isset($_POST) AND count($_POST) > 0  AND isset($_POST["intent"])){
      $intent = $fpi->files->purify($_POST["intent"]);
      if ($intent == "upload" AND isset($_FILES)){
        $returnObj = $fpi->handle($intent, $_FILES);
      }
      else if ($intent == "delete"){
        $returnObj = $fpi->handle($intent, $_POST["file"]);
      }
    }
  }
}

header('Content-Type: application/json');
echo json_encode($returnObj);

?>
