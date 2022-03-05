<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$subUname = str_replace("'", "", $_POST['uname']);
$subPsw = $_POST['psw'];
$user = new adventurer();

$return = "SignedIn";
if (isset($_COOKIE["seeker"])){
  if ( $_COOKIE["seeker"] != "" AND $_COOKIE["seeker"] != "undefined"){
    $return = $_COOKIE["seeker"];
  }
  setcookie("seeker", "", time() - 2200, "/");
}
else if (isset($_GET["back"])){
  $return = $_GET["back"];
}

if ($user->signIn($subUname, $subPsw)) {
  header("Location: $return"); exit;
}
header("Location:Account?error=signingIn");
?>
