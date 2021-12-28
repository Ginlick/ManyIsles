<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
$imageStencil = <<<NABSDAI
<div class="imageContainer" id="IMAGESRC2">
<div class="sidenav">
  <div class="shown fancyjump" style="top:10px" onclick="copyLink('https://manyisles.chIMAGESRC')"><i class="fas fa-link"></i></div>
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
$gen = new gen("act", 0, 0, false, "mystral");
if ($gen->power < 3 OR !$gen->domainSpecs["canImage"]){exit();}

$file = $_FILES['file'];
$fileTitle =  substr(preg_replace($regArray["basic"], "", $_FILES['file']["name"]), 0, 50); if ($fileTitle == ""){$fileTitle = "Image";}
$fileName = $gen->user."_".generateRandomString(22);
$fileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));
$fullFileName = $fileName.".".$fileType;
$realpath = realpath("uploadImage.php");
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$realpath = $realpath."/wikimgs/myst/".$fullFileName;
$uploadOk = 1;

$target_file = $realpath;

$check = getimagesize($_FILES["file"]["tmp_name"]);
if($check !== false) {
    $uploadOk = 1;
} else {
    echo "File is not an image.";
    $uploadOk = 0;
}
if (file_exists($target_file)) {
    $uploadOk = 0;
}
if ($_FILES["file"]["size"] > 2000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg") {
    echo "Sorry, only JPG, JPEG, PNG files are allowed.";
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo "Sorry, your image was not uploaded.";
    exit();
} else {
    $query = "INSERT INTO images (title, name, size, user) VALUES ('$fileTitle', '$fullFileName', ".$_FILES["file"]["size"].", $gen->user)";
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $gen->dbconn->query($query);
        $insert = str_replace("IMAGESRC", "/wikimgs/myst/".$fullFileName, $imageStencil);
        $insert = str_replace("IMAGENAME", $fileTitle, $insert);        
        echo $insert;
    }        
}



?>