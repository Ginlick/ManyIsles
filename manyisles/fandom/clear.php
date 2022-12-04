<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:../f.html");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;

$query="SELECT * FROM poets WHERE id = ".$uid;
$result =  $conn->query($query);
while ($row = $result->fetch_assoc()) {
    if ($row["admin"] != 1) {
        header("Location: /account/home");exit();
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
