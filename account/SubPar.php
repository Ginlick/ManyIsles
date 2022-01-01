<?php

if (preg_match("/[^A-Za-z0-9'\- ]/", $_POST["pname"])==1){$dl->go("BePartner?why=wrongTitle", "p");}

require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
$dl = new dlengine();
$writingNew = true;
if ($dl->partner(false)){
  $writingNew = false;
}
else if (!$dl->user->emailConfirmed){
  $dl->go("BePartner", "p");
}
$filing = new fileEngine($dl->user->user);


$pname = $_POST['pname'];
$jacob = $_POST['jacob'];
$jacob = str_replace('"', '', $jacob);
$jacob = str_replace('<', '', $jacob);
$imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
$status = "active";
$realpath = "4522_".purate($pname).".".$imageFileType;

$nomage = true;
$badmage = true;
$placed = $realpath;
if ($imageFileType != "") {
  $nomage = false;
  if ($filing->check($_FILES["image"], "standImg")){
    if ($placed = $filing->add($_FILES["image"]["tmp_name"], $realpath)) {
      $badmage = false;
    }
  }
}


if (!$writingNew) {
  $dewIt = 'UPDATE partners SET name = "'.$pname.'", image = "'.$placed.'", jacob = "'.$jacob.'" WHERE id = '.$dl->partId;
  if ($badmage) {
    $dewIt = 'UPDATE partners SET name = "'.$pname.'", jacob = "'.$jacob.'" WHERE id = '.$dl->partId;
  }
}
else {
  $dewIt = sprintf('INSERT INTO partners (name, image, user, jacob, status) VALUES ("%s", "%s", "%s", "%s", "active")', $pname, $placed, $dl->user->user, $jacob);
}

if ($dl->conn->query($dewIt)){
  $dl->user->promote("Trader");
  if (!$nomage AND $badmage) {
    $dl->go("Publish?i=badmage", "p");
  }
  else if ($writingNew){
    $dl->go("Publish?i=created", "p");
  }
  $dl->go("Publish?i=updated", "p");
}
else {echo "<br>Sorry, it appears there has been an error.<br>Query:".$dewIt;}


?>
