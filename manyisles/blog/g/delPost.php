<?php
require($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine;

$postId = 0;
if (isset($_GET["p"])){$postId = $blog->baseFiling->purate($_GET['p']);}

$blog->userCheck(); $isUser = false;
$query = "SELECT buser FROM posts WHERE code = '$postId'";
if ($toprow = $blog->blogconn->query($query)) {
  if (mysqli_num_rows($toprow) == 1) {
    while ($row = $toprow->fetch_assoc()) {
      if ($blog->hasProfile($row["buser"])) {$isUser = true;}
    }
  }
}

if ($isUser) {
  if ($postId != 0){
    $query = "DELETE FROM posts WHERE code = '$postId'";
    if ($blog->blogconn->query($query)) {
      $blog->go("feed?i=postDeleted");
    }
  }
}

$blog->go("post/$postId/?i=failedDelete");
echo "error";
?>
