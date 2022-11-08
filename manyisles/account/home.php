<?php
require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();

if ($user->signedIn){
  require_once("home-2.php");
}
else {
  $user->killCache();
  require_once("home-1.php");
}

?>
