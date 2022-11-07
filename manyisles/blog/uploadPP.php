<?php
require_once($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");

$blog = new blogEngine();
$blog->userCheck(false);
$filing = $blog->fileEngine();
$buserInfo = $blog->fetchBuserInfo();

if (!isset($_FILES["file"])) {echo "error no file present";}
$image = $_FILES["file"];

$postCode = $blog->buserId.$blog->user->generateRandomString(2);
$placedI = false;

if ($image != null) {
  if ($realpath = $filing->new($image, $postCode, "301")) {
    if ($filing->check($image, "standImg")){
      if ($buserInfo["info"]["pptype"]=="full"){$filing->deleteD($buserInfo["info"]["pp"]);}
      $placedI = $filing->add($image["tmp_name"], $realpath);
    }
  }
}

if (!$placedI){
  echo "error uploading";
}
$newBuserInfo = $buserInfo["info"];
$newBuserInfo["pp"] = $placedI;
$newBuserInfo = json_encode($newBuserInfo);

$query = "UPDATE busers SET info = '$newBuserInfo' WHERE id = ".$blog->buserId;
//echo $query;
if ($blog->blogconn->query($query)){
  echo $filing->clearmage($placedI);
  exit;
}
else {
  echo "error mysql";
}
echo "error";

?>
