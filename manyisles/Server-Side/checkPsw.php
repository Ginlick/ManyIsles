<?php
//DISCONTINUED: use promote.php's $user->check() instead!


require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true, true)){
  if (isset($redirect) && $redirect != ""){
    header("Location: ".$redirect);exit();
  }
  else {
    header("Location: /account/home?error=notSignedIn");exit();
  }
}

?>
