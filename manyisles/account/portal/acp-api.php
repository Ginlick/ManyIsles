<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
$user->killCache();

if (isset($_POST) AND count($_POST) > 0){
  if (isset($_POST["email"])) { //signCreate
    if (isset($_POST['uname']) AND isset($_POST['email']) AND isset($_POST['psw']) AND isset($_POST['region'])){

      $madeReturn = $user->createAccount($_POST['uname'], $_POST['email'], $_POST['psw'], $_POST['region']);
      if ($madeReturn !== true){
        $issuesObj = ["madeReturn" => $madeReturn];
      }
    }
  }
  else { //signIn
    if (isset($_POST['uname']) AND isset($_POST['psw'])){
      $subUname = str_replace("'", "", $_POST['uname']);
      $subPsw = $_POST['psw'];

      //echo $return; exit;
      if ($user->signIn($subUname, $subPsw)) {
      }
    }
  }
}

$returnObj = ["signedIn" => $user->signedIn, "emailConfirmed" => $user->emailConfirmed];
if (isset($issuesObj)){
  $returnObj["issues"] = $issuesObj;
}
header('Content-Type: application/json');
echo json_encode($returnObj);


?>
