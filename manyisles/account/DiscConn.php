<?php
if (preg_match("/#[0-9]{4}$/", $_POST['discSubmit'])==0){header("Location: SignedIn.php?show=discWrong");exit();}
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true, true)){header("Location:SignedIn?show=notConfirmed");}
$conn = $user->conn;

$query = 'SELECT * FROM accountsTable WHERE discname = "'.$_POST['discSubmit'].'"';
$result = $conn->query($query);
if ($result != false) {
    if ($result->num_rows != 0) {
        while ($row = $result -> fetch_assoc()) {
            if ($row["discname"] == $_POST['discSubmit'] && $row["id"] != $id) {
                header("Location: SignedIn?show=discDuplicate");exit();
            }
        }
    }
}

$query = 'UPDATE accountsTable SET discname = "'.$_POST['discSubmit'].'" WHERE id = '.$user->user;
echo $query;
if ($conn->query($query)) {
    header("Location: SignedIn?show=discSucc");exit();
}

 ?>
