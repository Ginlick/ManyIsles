<?php
$dir = "0";
if (isset($_GET["d"]) AND $_GET["d"]==1){$dir = 1;}
$creator = 0;
if (isset($_GET["u"])){$creator = preg_replace("/[^0-9]/", "", $_GET['u']);}

require($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine;
$blog->killCache();
$blog->userCheck();
if ($creator != 0){
  $blog->follow($blog->buserId, $creator, $dir);
}

?>
