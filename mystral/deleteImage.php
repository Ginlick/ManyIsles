<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

if(!isset($_POST["name"])){exit();}
if (!isset($_POST['name'])){exit();} else {if (!checkRegger("cleanText", $_POST["name"])){exit();}}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "mystral", ["notArticle"=>true]);
if ($gen->power < 3){exit();}
echo "hi";
$name = $_POST['name'];
$query = "DELETE FROM images WHERE user = $gen->user AND name = '$name'";
if ($gen->dbconn->query($query)) {
  if (mysqli_affected_rows($gen->dbconn) > 0){
    if ($gen->files->deleteD($name)){
      echo "success";
    }
  }
}

?>
