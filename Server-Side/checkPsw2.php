<?php
//requires checkpsw, two cookies

function checkNudePsw($checkpsw){
  if (preg_match("/^[0-9]+$/", $_COOKIE["loggedIn"])!=1) {setcookie("loggedIn", "", time() -3600, "/");return false;}
  if ($checkpsw == null){return false;}
  $psw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (!isset($_COOKIE["loggedIn"]) OR !isset($_COOKIE["loggedP"])) {return false;}
  if (password_verify($psw, $checkpsw)!=1){return false;}

  return true;
}




?>
