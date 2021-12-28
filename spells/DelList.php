<?php

if (preg_match("/[^a-e]{1}/", $_GET['which'])==1){header("Location: SpellList.html");exit();}


$servername = "localhost:3306";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "manyisle_accounts";

if ($_SERVER['REMOTE_ADDR']=="::1"){
$servername = "localhost";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "accounts";
}
$conn = new mysqli($servername, $username, $password, $dbname);


if(!isset($_COOKIE["loggedIn"])){header("Location: /account/Account.html?error=signingIn");exit();}
if(!isset($_COOKIE["loggedP"])){header("Location: /account/Account.html?error=signingIn");exit();}
if(!isset($_COOKIE["spellLists"])){header("Location: SpellList.html");exit();}


$id = $_COOKIE["loggedIn"];

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $curpsw = $row["password"];
    }
    }

 $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: /account/Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query = sprintf("UPDATE spelllists SET %s = null WHERE id = %s", $_GET["which"], $id);
 if ($conn->query($query)) {
    header("Location: SetSLCook.php");exit();
}

else {echo $query;}



?>
