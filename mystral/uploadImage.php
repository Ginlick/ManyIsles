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
if(!isset($_FILES["file"])){exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "mystral", ["notArticle"=>true]);
if ($gen->power < 3 OR !$gen->domainSpecs["canImage"]){exit();}

$file = $_FILES['file'];
$firstName = basename($file["name"]);
if ($fileTitle = $gen->files->new($file, $gen->generateRandomString(22), "221")) {
  if ($gen->files->check($file, "mystimg")){
    if ($placed = $gen->files->add($file["tmp_name"], $fileTitle)) {
      $query = "INSERT INTO images (title, name, size, user) VALUES ('$firstName', '$placed', ".$_FILES["file"]["size"].", $gen->user)";
      $gen->dbconn->query($query);
      $insert = str_replace("IMAGESRC2", $placed, $imageStencil);
      $insert = str_replace("IMAGESRC", $gen->files->clearmage($placed), $insert);
      $insert = str_replace("IMAGENAME", $firstName, $insert);
      echo $insert;
    }
  }
}


?>
