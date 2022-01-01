<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$query = "SELECT id, account FROM partners";
if ($max = $conn->query($query)) {
  while ($row = $max->fetch_assoc()){
    $query = 'SELECT id FROM accountsTable WHERE uname = "'.$row["account"].'"';
    if ($max2 = $conn->query($query)) {
      while ($row2 = $max2->fetch_assoc()){
        $accId = $row2["id"];
        $query = "UPDATE partners SET user = ".$row2["id"]." WHERE id = ".$row["id"];
        $conn->query($query);
      }
    }
  }
}


?>
