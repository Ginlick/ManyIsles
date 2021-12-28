<?php
if(!isset($_GET["id"])) {header("Location: /Code/CodeMain.html");exit();}
if(!isset($_GET["chek"])) {header("Location: /Code/CodeMain.html");exit();}

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
$id = $_GET["id"];
$ciphertext = urldecode($_GET["chek"]);
$ciphertext = str_replace(" ", "+", $ciphertext);
echo $ciphertext;

        $key = "That's/*GAY22";
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))
        {
            echo $original_plaintext;
        }


$query = "SELECT * FROM newmails WHERE id = ".$id;
if ($result = $conn->query($query)){
    while ($row = $result->fetch_assoc()){
        if ($original_plaintext == $row["ud"]){
            $query = 'UPDATE accountsTable SET email = "'.$row["email"].'" WHERE id = '.$id;
            $conn->query($query);
            $query = 'DELETE FROM newmails WHERE id = '.$id;
            $conn->query($query);
            header("Location: SignedIn.php?show=emailChangConf");exit();
        }
    }
}

echo "sorry....";
header("Location: SignedIn.php?show=emailWrongPassword");

$conn->close();

?>
