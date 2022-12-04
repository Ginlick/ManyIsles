<?php
$todoid = substr(preg_replace("/[^0-9]/", "", $_GET['id']), 0, 222);

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer($conn, $_COOKIE["loggedIn"]);
$id = $user->user;
if (!$user->check(false)){
  header("Location: /account/home");exit();
}
if ($id != 11 AND $id != 14) {
    header("Location: /account/home");exit();
}

$query = "UPDATE partners SET type = 1 WHERE id = $todoid";
if ($conn->query($query)){
  $userId = 0;
  $query = "SELECT user FROM partners WHERE id = $todoid";
  if ($result = $conn->query($query)){
    while ($row = $result->fetch_assoc()) {
      $userId = $row["user"];
    }
  }
  $partner = new adventurer($conn, $userId);
  if ($partner->promote("High Merchant")){
    $query = "DELETE FROM requests WHERE domain = 'pub' AND request = 'prem' AND requestee = $todoid";
    $conn->query($query);
    header("Location:/account/admin?i=activtd");
  }
}




?>
