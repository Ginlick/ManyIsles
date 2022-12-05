<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){$user->go("/account/home?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;
if (!$user->checkInputPsw($_POST['psw'])){$user->go("/account/home?show=emailWrongPsw");}
if (preg_match("/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $_POST['newmail'])!=1){$user->go("/account/home?show=emailWrongPsw");}
$newmail = $_POST['newmail'];
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/modMailer.php");
$mailer = new modMailer;

$conCode = $user->newConfirmCode();

$subject = "Confirm New Email";
$message = <<<MYGREATMAIL
      Please confirm your new email to set it as your account email. Ignore this message if you do not wish to do so.
    </p>
    <button style="margin:2vw auto 2vw;padding:10px;display:block;background-color:#61b3dd;border:0px;border-radius:4px;font-weight:bold;color:white;font-size:20px;"><a href="https://manyisles.ch/account/checkMail.php?code=XOXOXOXO" style="text-decoration:none;color:white;">Confirm</a></button>
MYGREATMAIL;

$query = "DELETE FROM newmails WHERE id = ".$id;
$conn->query($query);

$query = 'SELECT * FROM accountsTable WHERE email = "'.$newmail.'"';
$result = $conn->query($query);
if ($result != false) {
    if ($result->num_rows != 0) {
        $user->go("/account/home?show=emailDoubleMail");
    }
}

$code = $user->generateRandomString(22);
$query = 'INSERT INTO newmails (id, email, code) VALUES ('.$id.', "'.$newmail.'", "'.$code.'")';
if ($conn->query($query)) {
  $message = str_replace("XOXOXOXO", $code, $message);
  $mailer->send($newmail, $subject, $message, "community");
  $user->go("/account/home?show=emailAccomplished");
}
$conn->close();

?>
