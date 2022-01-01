<?php
$filename = $_GET["dl"];
$basename = basename($filename);

if (isset($_GET["dlid"])){
  $dlid = substr(preg_replace("/[^0-9]/","",  $_GET['dlid']), 0, 555);

  require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dl.php");
  $query = "UPDATE products SET downloads = downloads + 1 WHERE id = $dlid";
  $dlconn->query($query);
}
if (isset($_GET["name"])){
  $basename = $_GET["name"];
}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
$filing = new smolEngine();

$filing->download($filename, $basename);

?>
