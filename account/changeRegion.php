<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;

if (preg_match("/[1-3]/", $_POST['region'])!=1){header("Location: SignedIn.php");exit();}

$query = "UPDATE accountsTable SET region = ".$_POST["region"]." WHERE id = ".$id;
if ($conn->query($query)) {
    header("Location: SignedIn");
}


$conn->close();

?>
