<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();
$dic->user->check(true, true, true);

if (!isset($_GET['lang'])){$dic->go("home?i=error");}
if (!isset($_GET['who'])){$_GET['who'] = $dic->user->user;}
if (!isset($_GET['w'])){$_GET['w'] = "";}
if (preg_match("/[^0-9]/", $_GET['who'])==1){header("Location:/dic/home?i=error");exit();}
if (preg_match("/[^a-z]/", $_GET['w'])==1){header("Location:/dic/home?i=error");exit();}
if (preg_match("/[^0-9]/", $_GET['lang'])==1){header("Location:/dic/home?i=error");exit();}

$undo = false;
$uid = $_GET['who'];
$lang = $_GET['lang'];
if ($_GET['w'] == "undo"){
    $undo = true;
}


if (!$undo){
    $query = "INSERT INTO requests (requestee, domain, request) VALUES ($uid, 'wd$lang', 'auth')";
    echo $query;
    if ($dic->conn->query($query)) {
      $dic->go("home?i=req");
    }
}
else {
  $dic->checkCredentials(true);
  $query = "DELETE FROM requests WHERE id = $uid AND domain LIKE 'wd%'";
  echo $query;
  if ($dic->conn->query($query)) {
      $dic->go("users/langhub?dicd=$wiki&i=reqDel");
  }
}





?>
