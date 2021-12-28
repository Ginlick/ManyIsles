<?php
if(!isset($_COOKIE["loggedIn"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}
if (preg_match("/[1-3]/", $_POST['region'])!=1){header("Location: SignedIn.php");exit();}

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

$id = $_COOKIE['loggedIn'];
$query = "SELECT password, email FROM accountsTable WHERE id = ".$id;
$result =  $conn->query($query);
while ($row = $result->fetch_assoc()) {$curpsw = $row["password"];$email = $row["email"];}
$cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query = "UPDATE accountsTable SET region = ".$_POST["region"]." WHERE id = ".$id;
if ($conn->query($query)) {
    header("Location: SignedIn.php");
}


$conn->close();

?>
