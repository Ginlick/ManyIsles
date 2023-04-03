<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
$user->killCache();

if (isset($_POST) AND count($_POST) > 0){
  if (!isset($_POST["recaptcha-token"])){
    $issuesObj = ["madeReturn" => "captcha"];
  }
  else {
    //get captcha score
    $captchaInfo = $user->giveServerInfo("captcha");
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $captchaInfo["sk"], 'response' => $_POST["recaptcha-token"], "remoteip"=>$_SERVER['REMOTE_ADDR']);
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = json_decode(file_get_contents($url, false, $context), true);
    //act
    if (!$result["success"] OR !isset($result["score"]) OR $result["score"] < 0.5){
      $issuesObj = ["madeReturn" => "captcha"];
    }
    else {
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
          if ($user->signIn($subUname, $subPsw)) {
          }
        }
      }
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
