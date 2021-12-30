<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dl.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$partners = [];
$query = "SELECT id, name FROM partners";
if ($max = $conn->query($query)) {
  while ($row = $max->fetch_assoc()){
    $partners[$row["name"]] = $row["id"];
  }
}
foreach ($partners as $name => $id){
  $query = 'UPDATE products SET partner = '.$id.' WHERE partner = "'.$name.'"';
  $dlconn->query($query);
  echo $name." ".$dlconn->affected_rows."<br>";
}


?>
