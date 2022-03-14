<?php
require($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine;
$blog->killCache();

$postId = 0;
if (isset($_GET["p"])){$postId = $blog->baseFiling->purate($_GET['p']);}
$blog->userCheck();

if ($postId != 0){
  if ($response = $blog->like($blog->buserId, $postId)) {
    echo "success".$response; exit;
  }
}
echo "error";
?>
