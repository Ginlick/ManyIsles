<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:../f.html");exit();}

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

if (!isset($_COOKIE["loggedIn"])) {header("Location:/account/Account.html?error=notSignedIn");exit();}
$uid = $_COOKIE["loggedIn"];

$query = "SELECT * FROM accountsTable WHERE id = ".$uid;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $curpsw = $row["password"];
    }
    }
 $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query="SELECT * FROM poets WHERE id = ".$uid;
$result =  $conn->query($query);
while ($row = $result->fetch_assoc()) {
    if ($row["admin"] != 1) {
        header("Location: /account/SignedIn.php");exit();
    }
    if ($uid == $_GET['id'] AND $row["super"] != 1){
        header("Location:admin.php?i=0");exit();
    }
}

$query = "DELETE FROM slots WHERE id =".$_GET['id'];
if ($conn->query($query)) {
header("Location:admin.php?i=1");exit();
}



?>