<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
$dl = new dlengine();
$writingNew = true;
$dl->partner();
$filing = new fileEngine($dl->user->user);

$prodId = 0;
$writingNew = true;
if (isset($_POST['prodId']) AND $_POST['prodId'] != 0){
  $prodId = substr(preg_replace("/[^0-9]/", "", $_POST['prodId']), 0, 222);
  $writingNew = false;
}
$genre = 1;
$gsystem = 0;
$support = 0;
$external = 0;$wantsExternal = 0;
$tier = 0;
$image = false; $file = false;$placedI = false; $placedF = false;
$status = "active";

$pname = substr(preg_replace("/[^A-Za-z0-9\&\'\- ]/", "", $_POST['pname']), 0, 70);
$spname = substr(preg_replace("/[^A-Za-z0-9\&\'\- ]/", "", $_POST['spname']), 0, 35);
$jacob = substr(str_replace('"', '%double_quote%', $_POST['description']), 0, 2500);
$genre = substr(preg_replace("/[^0-9]/", "", $_POST['genre']), 0, 2);
$subgenre = substr(preg_replace("/[^a-z]/", "", $_POST['subgenre']), 0, 50);
$gsystem = substr(preg_replace("/[^0-9]/","",  $_POST['gamesys']), 0, 2);
$keywords = substr(preg_replace("/[^A-Za-z0-9\&\'\-, ]/", "", $_POST['keywords']), 0, 60);
$support = substr(preg_replace("/[^0-1]/", "", $_POST['supportProd']), 0, 1);
$format = substr(preg_replace("/[^A-Za-z0-9\'\- ]/", "", $_POST['format']), 0, 60);
$tier = substr(preg_replace("/[^0-9]/", "", $_POST['tier']), 0, 2);
$placedF = substr(str_replace('"', '', $_POST['link']), 0, 500);
if(isset($_POST['wantsExternal']) AND $_POST['wantsExternal']==1){$wantsExternal = 1;$external = 1;}

if (!$writingNew){
  if (isset($_FILES["image"])) {
    $image = $_FILES["image"];
  }
  if (!$dl->checkOwner($prodId)) {
    $dl->go("Product?i=error", "p");
  }
}
else {
  $query = "SELECT id FROM products ORDER BY id DESC LIMIT 1";
  if ($toprow = $dl->dlconn->query($query)) {
      while ($row = $toprow->fetch_assoc()) {
          $prodId = $row["id"] + 1;
      }
  }

  if (!isset($_FILES["image"]) OR (!isset($_FILES["file"]) AND !isset($placedF))) {
    echo ("fileFail");exit;
  }
  $image = $_FILES["image"];
}
if (isset($_FILES["file"])) {
  $file = $_FILES["file"];
}
else {
  $external = 1;
}
if ($dl->ppower < 1 OR $tier > 3){
  $tier = 0;
}

//act

if ($image){
  if ($realpath = $filing->new($image, $prodId, "461")) {
    if ($filing->check($image, "standImg")){
      $placedI = $filing->add($image["tmp_name"], $realpath);
    }
    if (!$placedI){
      echo "imgFail";
      if ($writingNew){$placedI="/IndexImgs/GMTips.png";$status="paused";}
    }
  }
}
if ($file){
  $external = 1;
  if ($realpath = $filing->new($file, $prodId, "462")) {
    $filetype = $dl->typeDets[$genre]["type"];
    if ($filing->check($file, $filetype)){
      $placedF = $filing->add($file["tmp_name"], $realpath);
      $external = 0;
    }
    if (!$placedF){
      echo "fileFail";
      if ($writingNew){$status="paused";}
    }
  }
}

if ($external == 1 AND !$writingNew) {
  if ($wantsExternal == 0) {
    $placedF = ""; $external = 0;
  }
}
if($external == 1){echo "external";}

$more = [];
if ($format != ""){
  $more["format"] = $format;
}
if ($genre == 1){
  $more["gsystem"]=$gsystem;
}
$more["indirect"]=$external;
$more = json_encode($more);

if ($writingNew) {
  $baseCommand = 'INSERT INTO products (name, shortName, image, partner, genre, subgenre, categories, tier, description, link, support, more, status) VALUES
   ("'.$pname.'", "'.$spname.'","'.$placedI.'", "'.$dl->partId.'", "'.$genre.'","'.$subgenre.'", "'.$keywords.'","'.$tier.'","'.$jacob.'", "'.$placedF.'", "'.$support.'", \''.$more.'\', "'.$status.'")';
}
else {
  $baseCommand = 'UPDATE products SET
  name =  "'.$pname.'", shortName = "'.$spname.'", genre = "'.$genre.'", subgenre = "'.$subgenre.'", categories = "'.$keywords.'", tier = "'.$tier.'", description = "'.$jacob.'", support = "'.$support.'", more = \''.$more.'\' ';
  if ($placedI){
    $baseCommand .= ', image = "'.$placedI.'" ';
  }
  if ($placedF){
    $baseCommand .= ', link = "'.$placedF.'" ';
  }
  $baseCommand .= " WHERE id = ".$prodId;
}

if ($dl->dlconn->query($baseCommand)){
  echo "success";
}

?>
