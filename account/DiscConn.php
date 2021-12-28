<?php
if (preg_match("/#[0-9]{4}$/", $_POST['discSubmit'])==0){header("Location: SignedIn.php?show=discWrong");exit();}



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
$id = $_COOKIE["loggedIn"];

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
$result = $conn->query($query);
while ($row = $result -> fetch_assoc()) {
    $psw = $row["password"];
    $emailConfirmed = $row["emailConfirmed"];
}
if ($emailConfirmed == "") {header("Location: SignedIn.php?show=notConfirmed");exit();}
$cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
if (password_verify($cpsw, $psw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query = 'SELECT * FROM accountsTable WHERE discname = "'.$_POST['discSubmit'].'"';
$result = $conn->query($query);

if ($result != false) {
    if ($result->num_rows != 0) {
        while ($row = $result -> fetch_assoc()) {
            if ($row["discname"] == $_POST['discSubmit'] && $row["id"] != $id) {
                header("Location: SignedIn.php?show=discDuplicate");exit();
            }
        }
    }
}
else {echo "gae";}

$query = 'UPDATE accountsTable SET discname = "'.$_POST['discSubmit'].'" WHERE id = '.$id;
echo $query;
if ($conn->query($query)) {
    header("Location: SignedIn.php?show=discSucc");exit();
}

 ?>

</body>
</html>