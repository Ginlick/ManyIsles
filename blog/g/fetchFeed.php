<?php
$mode = "chronology";
if (isset($_GET["m"])){$mode = preg_replace("/[^a-z]/", "", $_GET['m']);}
$buser = 0;
if (isset($_GET["u"])){$buser = preg_replace("/[^0-9]/", "", $_GET['u']);}

require($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine;


$query = "SELECT * FROM posts ";
if ($buser != 0 AND $buser != ""){
  $query .= " WHERE buser = $buser ";
}
if ($mode == "likes"){
  $query .= " ORDER BY likes DESC";
}
else if ($mode == "random") {
  $query .= " ORDER BY RAND()";
}
else if ($mode == "chronologyflip"){
  $query .= " ORDER BY id ASC";
}
else {
  $query .= " ORDER BY id DESC";
}

$total = 0;
if ($toprow = $blog->blogconn->query($query)) {
  while ($row = $toprow->fetch_assoc()) {
    $total++;
    if ($total>22) {continue;}
    echo $blog->genPost($row);
  }
}

?>
