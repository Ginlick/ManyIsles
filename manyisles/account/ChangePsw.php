<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;
if (!$user->checkInputPsw($_POST['oldpsw'])){header("Location: SignedIn.php?show=wrongPassword");exit();}

if (preg_match("/[A-Za-z0-9]{1,}/", $_POST['newpsw'])==1){
    $hashedPsw = password_hash($_POST['newpsw'], PASSWORD_DEFAULT);
    $query = 'UPDATE accountsTable SET password = "'.$hashedPsw.'" WHERE id = '.$id;
    if ($result = $conn->query($query)) {
      $user->signIn($user->uname, $_POST['newpsw']);
      header("Location: SignedIn?show=pswAccomplished");exit();
    }
} else {header("Location: SignedIn?show=pswWrongPsw");}
$conn->close();

?>
