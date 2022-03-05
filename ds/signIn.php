<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$subUname = str_replace("'", "", $_POST['uname']);
$subPsw = $_POST['psw'];
$user = new adventurer();
if ($user->signIn($subUname, $subPsw)) {
  header("Location: checkout1"); exit;
}

header("Location:checkout?error=signingIn");

?>
