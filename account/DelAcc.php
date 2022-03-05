<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;
$uname = $user->uname;

if (!$user->checkInputPsw($_POST['password'])){header("Location: SignedIn.php?show=wrongPassword");exit();}


require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");


$subject = "Partnership Dissolution";
$message = <<<MYGREATMAIL
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://manyisles.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Partnership Dissolution</h1>
    <p style="
            text-align: center;
            font-size: calc(8px + 0.9vw);
            color: black;
            padding:10px;
    ">
        Your account was just deleted. According to §3.7.1, block (2), of the Trader's Agreement, this allows the Many Isles Pantheon to begin a salvation period on your partnership. A week after initialization of this period, the Many Isles may dissolve your partnership, taking partial or complete ownership of your partnership's product assortment and deleting the partnership. Meanwhile, your partnership is suspended, as per §3.7.2 and §3.6 of the Trader's Agreement.<br />
        Do you wish to stop this procedure? Please contact the Pantheon or Homeland Institute of Trade immediately and create a new account. This will halt the salvation period and allow you to restore ownership over your partnership and its product assortment. Feel free to contact <a href="mailto:pantheon@manyisles.ch">pantheon@manyisles.ch</a> if you have any questions.

    </p>
</body>
</html>
MYGREATMAIL;

$subject2 = "Goodbye, ".$user->fullName;
$message2 = <<<MYGREATMAIL
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://manyisles.ch/Imgs/PopupBar.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Account Deleted</h1>
    <p style="text-align: center;font-size: calc(8px + 0.9vw);color: black;padding:10px;">
        We're sorry to see you go.<br><br>You've just deleted your account, losing all data, including any Many Isles credit and saved spell lists.
    </p>
</body>
</html>
MYGREATMAIL;

$headers = "From: pantheon@manyisles.ch" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

$query = "DELETE FROM accountsTable WHERE id = ".$id;
if ($result=$conn->query($query)) {
    $query = '"SELECT * FROM partners WHERE account = "'.$uname.'"';
    if ($conn->query($query)){
        $query = 'UPDATE partners SET status = "suspended", account = "none" WHERE account = "'.$uname.'"';
        $conn->query($query);
        mail($to, $subject, $message, $headers);
    }
    $query = "DELETE FROM spelllists WHERE id = ".$id;
    $conn->query($query);
    $query = "DELETE FROM poets WHERE id = ".$id;
    $conn->query($query);
    $query = "DELETE FROM slots WHERE id = ".$id;
    $conn->query($query);
    session_destroy();

    //money part
    $query = "SELECT * FROM global_credit WHERE id = $id";
    if ($result = $moneyconn->query($query)){
        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){
                $userCredit = $row["credit"];
                $userReference = $row["reference"];

                $query = "INSERT INTO transfers_$userReference (motive, source, amount) VALUES ('Account Deletion', '$uname', '-$userCredit')";
                $moneyconn->query($query);
                $query = "INSERT INTO transfers_1422222222 (motive, source, amount) VALUES ('Account $id Deleted', '$uname', '$userCredit')";
                $moneyconn->query($query);

                $query = "UPDATE global_credit SET credit = 0 WHERE reference = $userReference";
                $moneyconn->query($query);
                $query = "UPDATE global_credit SET credit = credit + $userCredit WHERE reference = 1422222222";
                $moneyconn->query($query);
            }
        }
    }

    mail("godsofmanyisles@gmail.com", "Account Deleted", "yep, it's sad to say, ".$uname, $headers);
    setcookie("loggedIn", "", time() -3600, "/");
    setcookie("loggedCode", "", time() -3600, "/");
    echo "Done";
    header("Location: Account.html");
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
