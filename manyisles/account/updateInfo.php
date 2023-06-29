<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){$user->go("Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;

$fName = $user->purify($_POST["firstName"], "account");
$lName = $user->purify($_POST["lastName"], "account");
$discName = $user->purify($_POST["discordName"], "discName");
$region = $user->purify($_POST["region"], "number");

$persInfo = $user->persInfo;
$persInfo["fName"] = $fName;
$persInfo["lName"] = $lName;
$persInfo["references"]["discName"] = $discName;
$jPersInfo = json_encode($persInfo);

$query = "UPDATE accountsTable SET region = '$region', persInfo = '$jPersInfo'";

if ($conn->query($query)) {
    $user->go("home?show=persInfo");
}


$conn->close();

?>
