<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();
$dl->partner();

$query = "SELECT * FROM requests WHERE domain = 'pub' AND request = 'prem' AND requestee = ".$dl->partId;
if ($result = $conn->query($query)) {
  if (mysqli_num_rows($result) != 0){
    $dl->go("Publish?i=requ", "p");
  }
}

$query = "INSERT INTO requests (requestee, domain, request) VALUES ($dl->partId, 'pub', 'prem')";
if ($conn->query($query)) {
  $dl->go("Publish?i=requ", "p");
}

$dl->go("Publish?i=notrequ", "p");


?>
