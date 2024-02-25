<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
$user->killCache();

if (isset($_POST) AND count($_POST) > 0){
  if (isset($_POST['uname']) AND isset($_POST['psw'])){
    $subUname = str_replace("'", "", $_POST['uname']);
    $subPsw = $_POST['psw'];
    if ($user->loginDirect($subUname, $subPsw)) {
    }
  }
}

$returnObj = ["signedIn" => $user->signedIn, "emailConfirmed" => $user->emailConfirmed, "uname" => $user->uname, "fullname" => $user->fullName];
if (isset($issuesObj)){
  $returnObj["issues"] = $issuesObj;
}
header('Content-Type: application/json');
echo json_encode($returnObj);


?>
