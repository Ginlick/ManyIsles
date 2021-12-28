<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");



$return = "SignedIn.php";
if (isset($_COOKIE["seeker"])){
  $return = $_COOKIE["seeker"];
  setcookie("seeker", "", time() - 2200);
}

require("../Server-Side/encryptData.php");
$storedPassword = openssl_encrypt ($_POST['psw'], $method, $key, 0, $iv);

if(!isset($_COOKIE["loggedIn"])) {
    echo "1";
    if ($userrow = $conn->query(sprintf("SELECT * FROM accountsTable WHERE uname='%s';", $_POST['uname']))) {
        echo "2";
        if ($userrow->num_rows == 1) {
            echo "3";
            while ($row = $userrow->fetch_assoc()) {
                echo "4";
                if (password_verify($_POST['psw'], $row["password"])==1) {
                    $id = $row["id"];
                    setcookie("loggedIn", $id, time()+1900800, "/");
                    setcookie("loggedP", $storedPassword, time()+1900800, "/");
                    echo "success";
                    header("Location: $return");
                }
               else {header("Location: Account.html?error=signingIn");}
            }
        }
        else {header("Location: Account.html?error=signingIn");}
    }
    else {header("Location: Account.html?error=signingIn");}
}
else {header("Location: SignedIn.php");}
?>
