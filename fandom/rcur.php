<?php
if (preg_match("/[^0-9]/", $_GET['who'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^a-z]/", $_GET['w'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_GET['wiki'])==1){header("Location:/fandom/home");exit();}

$undo = false;
$uid = $_GET['who'];
$wiki = $_GET['wiki'];
if ($_GET['w'] == "undo"){
    $undo = true;
}


if (!$undo){
    $doSecurity = true;
    //require($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");
    require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
    $user = new adventurer($uid);$user->check(true); $conn = $user->conn;
    require("slotChecker.php");

    $query = "INSERT INTO requests (requestee, domain, request) VALUES ($uid, 'wf$wiki', 'auth')";
    if ($conn->query($query)) {
        header("Location:/fandom/wiki/$wiki/home?i=reqCur"); exit();
    }
}
else {
  require($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
  $page = new gen("act", 0, $wiki);
  if ($page->power<3){header("Location:/fandom/wsettings.php?w=$wiki"); exit();}
  $query = "DELETE FROM requests WHERE id = $uid";
  if ($page->conn->query($query)) {
      header("Location:/fandom/wsettings.php?w=$wiki&i=reqCurDel"); exit();
  }
}





?>
