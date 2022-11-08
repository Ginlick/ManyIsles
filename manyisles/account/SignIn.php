<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$subUname = str_replace("'", "", $_POST['uname']);
$subPsw = $_POST['psw'];
$user = new adventurer();
$user->killCache();

$return = "home";
if (isset($_COOKIE["seeker"])){
  if ( $_COOKIE["seeker"] != "" AND $_COOKIE["seeker"] != "undefined"){
    $return = $_COOKIE["seeker"];
  }
  setcookie("seeker", $return, time() - 2200, "/");
}
else if (isset($_GET["back"])){
  $return = $_GET["back"];
}

//echo $return; exit;
if ($user->signIn($subUname, $subPsw)) {
  $user->go($return);
}
$user->go("home?error=signingIn");
?>
