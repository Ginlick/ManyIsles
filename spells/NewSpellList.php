<?php

if (preg_match("/[^A-Za-z0-9'-, ]{2,}/", $_GET['body'])==1){header("Location: SpellIndex.html");exit();}

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
if(isset($_COOKIE["spellLists"])){header("Location: SpellList.html");exit();}


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

$gae="";
$query = "SELECT * FROM spelllists WHERE id = ".$id;
 if ($result = $conn->query($query)) {
    while ($row = $result->fetch_row()) {
    if ($row[0]!= null){header("Location: SetSLCook.php");exit();}
}
}

$query = sprintf('INSERT INTO spelllists (id, a) VALUES ("%s", "%s")', $id,  $_GET['body']);
 if ($conn->query($query)) {
    echo "inserted";
    setcookie("spellLists", $_GET['body'], time()+604800, "/");
    header("Location: SavedList.html?sl="."a");exit();
}

else echo $query;




?>
