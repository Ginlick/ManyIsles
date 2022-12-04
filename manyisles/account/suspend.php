<?php
$todoid = substr(preg_replace("/[^0-9]/", "", $_GET['id']), 0, 222);

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/modMailer.php");
$user = new adventurer($conn, $_COOKIE["loggedIn"]);
$mailer = new modMailer();
$id = $user->user;
if (!$user->check(false)){
  header("Location: /account/home");exit();
}
if ($id != 11 AND $id != 14) {
    header("Location: /account/home");exit();
}

$query = "SELECT user, status FROM partners WHERE id =".$todoid;
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $paccount = $row["user"];
    $status = $row["status"];
}
$query = 'SELECT email FROM accountsTable WHERE id ="'.$paccount.'"';
echo $query;
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
  $pemail = $row["email"];
}

$query = "UPDATE partners SET status = 'suspended' WHERE id =".$todoid;
if ($status == "suspended"){$query = str_replace("suspended", "active", $query);}

if ($conn->query($query)) {
    if ($status == "suspended"){
      $subject = "Partnership Suspended";
      $message = 'Your partnership has been suspended. All your products are temporarily taken down from the digital library.<br />
      This action usually results only when a trader severely breaks Many Isles <a href="https://www.manyisles.ch/docs/60/Publishing_Terms">publishing rules</a>, such as through vulgar or highly sexual content, or breaking publication guidelines.<br />
      The administrator will contact you personally via mail. If you receive no further information, feel free to contact <a href="mailto:godsofmanyisles@gmail.com">godsofmanyisles@gmail.com</a> for further information.';
    }
    else {
      $subject = "Partnership Reactivated";
      $message = 'An administrator of the Homeland Institute of Trade reactivated your suspended partnership, and all your products are now visible again in the digital library.';
    }
    $mailer->send($pemail, $subject, $message, "publishing");
}
header("Location:admin.php");


?>
