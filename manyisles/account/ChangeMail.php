<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;
if (!$user->checkInputPsw($_POST['psw'])){header("Location: SignedIn.php?show=wrongPassword");exit();}

$conCode = $user->newConfirmCode();

$subject = "Confirm New Email";
$message = <<<MYGREATMAIL
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://manyisles.ch/Imgs/PopupBar.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Confirm Email</h1>
    <p style="
            text-align: center;
            font-size: calc(8px + 0.9vw);
            color: black;
            padding:10px;
    ">
        Please confirm your new email to set it as your account email. Ignore this message if you do not wish to do so.

    </p>
    <button class="popupButton" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;display:block;margin-top:10px;margin-bottom:auto;margin-right:auto;margin-left:auto;background-color:red;border:0px;border-radius:4px;font-weight:bold;color:white;font-size:20px;"><a href="https://manyisles.ch/account/checkMail.php?id=massiveTreeofLife&chek=XOXOXOXO" style="text-decoration:none;color:white;">Confirm</a></button>
</body>
</html>
MYGREATMAIL;
$headers = "From: pantheon@manyisles.ch" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

$query = "DELETE FROM newmails WHERE id = ".$id;
$conn->query($query);

$query = 'SELECT * FROM accountsTable WHERE email = "'.$_POST['newmail'].'"';
$result = $conn->query($query);

if ($result != false) {
    if ($result->num_rows != 0) {
        header("Location: SignedIn.php?show=emailDoubleMail");exit();
    }
}

if (preg_match("/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/", $_POST['newmail'])==1){
    $query = 'INSERT INTO newmails (id, email) VALUES ('.$id.', "'.$_POST['newmail'].'")';
    $conn->query($query);
    $query = "SELECT ud FROM newmails WHERE id = ".$id;
    if ($result = $conn->query($query)) {
        while ($row = $result->fetch_assoc()){$ud = $row["ud"];}
        echo $ud."<br>";
        $key = "That's/*GAY22";
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($ud, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
        echo $ciphertext."<br>";


        $mailcipher = urlencode($ciphertext);
        echo $mailcipher."<br>";

        $message = str_replace("massiveTreeofLife", $conCode, $message);
        $message = str_replace("XOXOXOXO", $mailcipher, $message);
        mail($_POST['newmail'], $subject, $message, $headers);
        echo $message;
        header("Location: SignedIn.php?show=emailAccomplished");exit();
    }
} else {header("Location: SignedIn.php?show=emailWrongPsw");}
$conn->close();

?>
