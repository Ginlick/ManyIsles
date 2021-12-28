<?php
if(!isset($_COOKIE["loggedIn"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}

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
$query = "SELECT password FROM accountsTable WHERE id = ".$id;
$result =  $conn->query($query);
while ($row = $result->fetch_assoc()) {$curpsw = $row["password"];}
$cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}
if ($_POST['oldpsw'] != $cpsw) {header("Location: SignedIn.php?show=pswWrongPassword");exit();}

if (preg_match("/[A-Za-z0-9]{1,}/", $_POST['newpsw'])==1){
    $hashedPsw = password_hash($_POST['newpsw'], PASSWORD_DEFAULT);
    $query = 'UPDATE accountsTable SET password = "'.$hashedPsw.'" WHERE id = '.$id;
    if ($result = $conn->query($query)) {
        $storePassword = openssl_encrypt ($_POST['newpsw'], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
        setcookie("loggedP", $storePassword, time()+604800, "/");
        header("Location: SignedIn.php?show=pswAccomplished");exit();
    }
} else {header("Location: SignedIn.php?show=pswWrongNpsw");}
$conn->close();

?>
