<?php
//requires checkpsw, two cookies

function doJs() {
    echo '<script>
    if (document.cookie.indexOf("loggedIn=") == -1) { window.location.replace("Account.html"); }
</script>
'; exit();
}

if (preg_match("/^[0-9]+$/", $_COOKIE["loggedIn"])!=1) {setcookie("loggedIn", "", time() -3600, "/");header("Location: /account/Account.html?error=notSignedIn");exit();}

$psw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");

if (isset($redirect) && $redirect != ""){
    if ($checkpsw == null){header("Location: ".$redirect);exit();}
    if (!isset($_COOKIE["loggedIn"]) OR !isset($_COOKIE["loggedP"])) {header("Location: ".$redirect);exit();}
    if (password_verify($psw, $checkpsw)!=1){setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");header("Location: ".$redirect);exit();}
}
else if (isset($mellow) AND $mellow) {
    if ($checkpsw == null){doJs();}
    if (!isset($_COOKIE["loggedIn"]) OR !isset($_COOKIE["loggedP"])) {doJs();}
    if (password_verify($psw, $checkpsw)!=1){setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");doJs();}
}
else {
    if ($checkpsw == null){exit();}
    if (!isset($_COOKIE["loggedIn"]) OR !isset($_COOKIE["loggedP"])) {exit();}
    if (password_verify($psw, $checkpsw)!=1){setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}
}

?>
