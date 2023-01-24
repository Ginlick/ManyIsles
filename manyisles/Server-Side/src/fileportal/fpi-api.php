<?php
$imageStencil = <<<NABSDAI
<div class="imageContainer" id="IMAGESRC2">
<div class="sidenav">
  <div class="shown fancyjump" style="top:10px" onclick="copyLink('IMAGESRC')"><i class="fas fa-link"></i></div>
  <div class="trans" style="top:75px" onclick="renameImage('IMAGESRC2')"><i class="fas fa-pen"></i></div>
  <div class="trans" style="top:140px" onclick="deleteImage('IMAGESRC2')"><i class="fas fa-trash"></i></div>
</div>
<div load-image="IMAGESRC"></div>
<div class="titleCont">
<h3 id="titleIMAGESRC2">IMAGENAME</h3>
</div>
</div>
NABSDAI;
$codes = [
  221 => ["name" => "mystral", "filetype" => "mystimg"]
];
$returnObj = ["error"=>"An error occurred."];
$canUpload = false;

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
$files = new fileengine($user->user);
$files->killCache();

if (isset($_POST["code"]) AND isset($codes[$_POST["code"]])){
  if (isset($_POST) AND count($_POST) > 0 AND isset($_FILES)){

    //check basic credentials
    $code = $_POST["code"];
    if ($code == 221){ //mystral image
      require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
      $mgen = new gen("act", 0, 0, false, "mystral", ["notArticle"=>true]);
      if ($mgen->power >= 3){$canUpload = true;}else{$returnObj["error"]="Image cap reached.";}
    }

    //execute
    $intent = "upload"; if (isset($_POST["intent"])){$intent = $_POST["intent"];}
    if ($intent == "upload"){
      $fileNum = min(11, count($_FILES));
      //check upload credentials
      if ($code == 221){
        if (!$mgen->domainSpecs["canImage"]){$canUpload = false;}
      }
      //upload file
      if ($canUpload){
        foreach ($_FILES as $file){
          if ($fileTitle = $files->new($file, $files->generateRandomString(22), "221")) { //some codes will require different naming (pre-set, not random)
            if ($files->check($file, $codes[$code]["filetype"])){ //maybe: a better image checker too; see if it's inappropriate, virus...
              if ($placed = $files->add($file["tmp_name"], $fileTitle)) {
                $toInsert = ["name" => $placed, "url" => $files->clearmage($placed)];
                //custom post-processing
                if ($code == 221){
                  $firstName = $files->purate(basename($file["name"]), "quotes");
                  $query = "INSERT INTO images (title, name, size, user) VALUES ('$firstName', '$placed', ".$file["size"].", $user->user)";
                  $mgen->dbconn->query($query);
                  $insert = str_replace("IMAGESRC2", $placed, $imageStencil);
                  $insert = str_replace("IMAGESRC", $toInsert["url"], $insert);
                  $insert = str_replace("IMAGENAME", $firstName, $insert);
                  $toInsert["galleryHTML"] = $insert;
                }
                extendObj($returnObj, $toInsert);
              }
            }
          }
        }
      }
    }
    else if ($intent == "delete"){
      if (!isset($_POST["file"])){$canUpload = false;} $name = $files->purify($_POST["file"], "quotes");
      if ($canUpload){
        if ($files->delete($name, $code)){
          $toInsert = ["name"=> $name, "url" => ""];
          //custom post-processing
          if ($code == 221){
            $query = "DELETE FROM images WHERE user = $user->user AND name = '$name'";
            $mgen->dbconn->query($query);
          }
          extendObj($returnObj, $toInsert);
        }
      }
    }
  }
}

function extendObj(&$extendObj, $toInsert){
  if (isset($extendObj["error"])){unset($extendObj["error"]);$extendObj["files"] = [];}
  $extendObj["files"][] = $toInsert;
}

header('Content-Type: application/json');
echo json_encode($returnObj);


?>
