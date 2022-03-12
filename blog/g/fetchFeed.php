<?php
require($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine;

$mode = "new"; $type = "posts";
if (isset($_POST["m"])){$mode = preg_replace("/[^a-z]/", "", $_POST['m']);}
$buser = 0;$severalBusers = false;$reference = "";$offset = 0; $tags = []; $settings = [];
if (isset($_POST["u"])){
  if (preg_match("/^[0-9]$/", $_POST['u'])){
    $buser = $_POST['u'];
  }
  else {
    $severalBusers = true;
    $buser = $_POST['u'];
    if (gettype($buser)!="array"){
      $buser = $blog->getArray($buser);
    }
  }
}
if (isset($_POST["t"])){$type = preg_replace("/[^a-z]/", "", $_POST['t']);}
if ($type != "comments"){$type = "posts";}
if (isset($_POST["r"])){$reference = $blog->baseFiling->purate($_POST["r"]);}
if (isset($_POST["o"])){$offset = preg_replace("/[^0-9]/", "", $_POST['o']);}
if (isset($_POST["s"])){$settings = $blog->getArray($_POST["s"]);}
if (isset($_POST["t"])){$tags = $blog->getCommaArr($_POST["t"]);}

$hasWhere = true;
$query = "SELECT * FROM $type ";
if ($reference != ""){
  $query .= " WHERE refPost = '$reference' ";
}
else if ($buser != 0 AND $buser != ""){
  if ($severalBusers){
    if (count($buser)>0){
      $query .= " WHERE "; $sayOr = false;
      foreach ($buser as $single){
        if ($sayOr){$query.=" OR ";}else {$sayOr = true;}
        $query .= " (buser = $single) ";
      }
    }
    else {
      $query .= " WHERE buser = 0";
    }
  }
  else {
    $query .= " WHERE (buser = $buser) ";
  }
}
else {$hasWhere = false;}

//tags
if (count($tags) > 0) {
  $addition = "";
  $dontDo = true;
  foreach ($tags as $tag){if ($tag != ""){$dontDo = false;}}
  if (!$dontDo){
    if (!$hasWhere){
      $addition = " WHERE ("; $hasWhere = true;
    }
    else {
      $addition = " AND (";
    } $first = true;
    foreach ($tags as $tag){
      if ($tag == ""){continue;}
      if ($first){$first = false;}else {$addition .= " OR ";}
      $addition .= ' (genre LIKE \'%"'.$tag.'",%\') ';
    }
    $addition .= ")";
  }
}
$query .= $addition;

if ($mode == "likes"){
  $query .= " ORDER BY likes DESC";
}
else if ($mode == "random") {
  $query .= " ORDER BY RAND()";
}
else if ($mode == "old"){
  $query .= " ORDER BY id ASC";
}
else {
  $query .= " ORDER BY id DESC";
}
if ($mode == "random"){
  $query .= " LIMIT 8";
}
else {
  $query .= " LIMIT $offset, 8";
}
//echo $query; exit;

$total = 0;
if ($toprow = $blog->blogconn->query($query)) {
  if (mysqli_num_rows($toprow)==0){echo "No more posts."; exit;}
  while ($row = $toprow->fetch_assoc()) {
    if ($type == "comments") {
      echo $blog->genComment($row);
    }
    else {
      $explore = false; if (isset($settings["explore"])){$explore = true;}
      echo $blog->genPost($row, 0, $explore);
    }
  }
}

?>
