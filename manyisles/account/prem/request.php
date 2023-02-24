<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();
$dl->partner();
$mailer = $dl->addMailer();


$query = "SELECT * FROM requests WHERE domain = 'pub' AND request = 'prem' AND requestee = ".$dl->partId;
if ($result = $dl->conn->query($query)) {
  if (mysqli_num_rows($result) != 0){
    $dl->go("Publish?i=requ", "p");
  }
}

$query = "INSERT INTO requests (requestee, domain, request) VALUES ($dl->partId, 'pub', 'prem')";
if ($dl->conn->query($query)) {
  $mailer->easyMail("New Premium Partnership Request", "New premium partnership request by ".$dl->user->fullName."'s ".$dl->partName.", check it out https://".$mailer->giveServerInfo("servername")."/account/admin.php", "", "publishing");
  $dl->go("Publish?i=requ", "p");
}

$dl->go("Publish?i=notrequ", "p");


?>
