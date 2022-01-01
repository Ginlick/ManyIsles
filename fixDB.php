<?php

//disable engine part that makes privs be subdomain first!

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dl.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
$dl = new dlengine();

function clearmage($image) {
  if (!str_contains($image, "/")){
      $image = "/IndexImgs/".$image;
  }
  return $image;
}
function clearlink($image, $genre) {
  if (!str_contains($image, "/")){
    if ($genre == 3){
      $image = "/dl/Art/".$image;
    }
    else {
      $image = "/dl/Friiz/".$image;
    }
  }
  return $image;
}

$query = "SELECT id, image, link, genre FROM products";
if ($max = $dlconn->query($query)) {
  while ($row = $max->fetch_assoc()){
    $image = clearmage($row["image"]);
    $file = clearfile($row["link"], $row["genre"]);
    $query = "UPDATE products SET image = '$image', link = '$link' WHERE id = ".$row["id"];
    //$dlconn->query($query);
  }
}

?>
