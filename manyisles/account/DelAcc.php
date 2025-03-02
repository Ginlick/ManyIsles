<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/modMailer.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:/account/home?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;
$uname = $user->uname;
$mailer = new modMailer();

if (!$user->checkInputPsw($_POST['psw'])){header("Location: /account/home?show=wrongPassword");exit();}


require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");


$subject1 = "Partnership Dissolution"; $textSubj1 = false;
$message1 = "
        Your account was just deleted. According to §3.7.1, block (2), of the Trader's Agreement, this allows the Many Isles Pantheon to begin a salvation period on your partnership. A week after initialization of this period, the Many Isles may dissolve your partnership, taking partial or complete ownership of your partnership's product assortment and deleting the partnership. Meanwhile, your partnership is suspended, as per §3.7.2 and §3.6 of the Trader's Agreement.<br />
        Do you wish to stop this procedure? Please contact the Pantheon immediately and create a new account. This will halt the salvation period and allow you to restore ownership over your partnership and its product assortment. Feel free to contact <a href='mailto:pantheonmanyisles.ch'>pantheonmanyisles.ch</a> if you have any questions.";

$subject2 = "Goodbye, ".$user->fullName; $textSubj2 = "Account Deleted";
$message2 = <<<MYGREATMAIL
        We're sorry to see you go.<br><br>You've just deleted your account, losing all data, including any Many Isles credit and saved spell lists.
MYGREATMAIL;

$query = "DELETE FROM accountsTable WHERE id = ".$id;
if ($result=$conn->query($query)) {
    $query = '"SELECT * FROM partners WHERE account = "'.$uname.'"';
    if ($conn->query($query)){
        $query = 'UPDATE partners SET status = "suspended", account = "none" WHERE account = "'.$uname.'"';
        $conn->query($query);
        $mailer->send($user->email, $subject1, $message1, "publishing", $textSubj1);
    }
    $query = "DELETE FROM spelllists WHERE id = ".$id;
    $conn->query($query);
    $query = "DELETE FROM poets WHERE id = ".$id;
    $conn->query($query);
    $query = "DELETE FROM slots WHERE id = ".$id;
    $conn->query($query);

    //blogs
    require($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
    $blog = new blogEngine();
    $blog->deleteBuser();

    //money part
    require($_SERVER['DOCUMENT_ROOT']."/Server-Side/transactions.php");
    $userCredit = new transaction($moneyconn, $id);
    $panthCredit = new transaction($moneyconn, 14);
    if ($userCredit->new(-$userCredit->total_credit, $uname, "Account Deletion")){
      $panthCredit->new($userCredit->total_credit, $uname, "Account $id Deleted");
    }

    $mailer->send("pantheonmanyisles.ch", "Account Deleted", "yep, it's sad to say, ".$uname);
    $mailer->send($user->email, $subject2, $message2, "community", $textSubj2);

    setcookie("loggedIn", "", time() -3600, "/");
    setcookie("loggedCode", "", time() -3600, "/");
    echo "Done";
    header("Location: /account/home?error=deleted");
}

$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
<p>If you see this, something's wrong.</p>
</body>
</html>
