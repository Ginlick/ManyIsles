<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}

if ($user->sendConfirmer()){
  $user->go("SignedIn?show=resent");
}
else {
  echo "Your email wasn't sent; it failed for some reason.";
}


?>
