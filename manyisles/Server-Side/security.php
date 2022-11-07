<?php
if(!isset($_COOKIE["loggedIn"])){echo "<script>window.location.replace('/account/Account?error=notSignedIn');</script>"; exit();}
if (!isset($conn)){require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");}

$uid = $_COOKIE["loggedIn"];
if (preg_match("/^[0-9]+$/", $uid)!=1) {setcookie("loggedIn", "", time() -3600, "/");header("Location: /account/Account?error=notSignedIn");exit();}

$query = "SELECT * FROM accountsTable WHERE id = ".$uid;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $checkpsw = $row["password"];
    }
}
require($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw.php");
?>
