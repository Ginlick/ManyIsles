<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->signedIn){$user->go("/account/home?error=notSignedIn");}
if(!isset($_GET["code"])) {$user->go("/home");}
$code = $_GET["code"];

$query = "SELECT * FROM newmails WHERE id = ".$user->user;
if ($result = $user->conn->query($query)){
    while ($row = $result->fetch_assoc()){
        if ($code == $row["code"]){
            $query = 'UPDATE accountsTable SET email = "'.$row["email"].'" WHERE id = '.$user->user;
            $user->conn->query($query);
            $query = 'DELETE FROM newmails WHERE id = '.$user->user;
            $user->conn->query($query);
            $user->go("/account/home?show=emailChangConf");
        }
    }
}

echo "sorry....";
$user->go("/account/home?show=emailWrongPassword");
?>
