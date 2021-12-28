<?php

$servername = "localhost:3306";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "manyisle_accounts";

if ($_SERVER['REMOTE_ADDR']=="::1"){
$servername = "localhost";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "accounts";
}

$conn = new mysqli($servername, $username, $password, $dbname);
$id = $_COOKIE["loggedIn"];
$todoid = $_GET["id"];

if ($id != 14 AND $id != 26 AND $id != 36) {
    header("Location: /account/SignedIn.php");exit();
}

if(!isset($_COOKIE["loggedIn"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}

  $getNameRow = "SELECT uname FROM accountsTable WHERE id =". $_COOKIE["loggedIn"];
  $nameresult =  $conn->query($getNameRow);
  $name ="";
  while ($row = $nameresult->fetch_row()) {$name = sprintf ("%s", $row[0]);}
 if ($name == "") {setcookie("loggedIn", "", time() -3600, "/");header("Location: Account.html");exit();}

  $getTitleRow = "SELECT title FROM accountsTable WHERE id =". $_COOKIE["loggedIn"];
  $titleresult =  $conn->query($getTitleRow);
  $title ="";
  while ($row = $titleresult->fetch_row()) {$title = sprintf ("%s", $row[0]);}

  $getMailRow = "SELECT * FROM accountsTable WHERE id =". $_COOKIE["loggedIn"];
  $mailresult =  $conn->query($getMailRow);
  $mail ="";
  while ($row = $mailresult->fetch_assoc()) {$mail = sprintf ("%s", $row["email"]); $curpsw = $row["password"];}
  $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}


$query = "SELECT account, status FROM partners WHERE id =".$todoid;
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $paccount = $row["account"];
    $status = $row["status"];
}
$query = 'SELECT email, id FROM accountsTable WHERE uname ="'.$paccount.'"';
echo $query;
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $pemail = $row["email"];
    if ($row["id"]==$id){header("Location: admin.php?y=1");exit();}
}

$query = "UPDATE partners SET status = 'suspended' WHERE id =".$todoid;
if ($status == "suspended"){$query = str_replace("suspended", "active", $query);}

if ($conn->query($query)) {
    if ($status == "active"){
    $subject = "Partnership Suspended";
    $headers = "From: pantheon@manyisles.ch" . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $message = <<<MYGREATMAIL

        <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <img src="http://manyisles.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
        <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Your Partnership was Suspended</h1>
        <p style="
            text-align: center;
            font-size: calc(8px + 0.9vw);
            color: black;
            padding:10px;
    ">
            An administrator of the Homeland Institute of Trade suspended your partnership. All your products are temporarily taken down from the digital library.<br />
            This action usually results only when a trader severely breaks Many Isles <a href="https://www.manyisles.ch/wiki/h/publishing/pubguide.html">publishing rules</a>, such as through vulgar or highly sexual content, or breaking publication guidelines.<br />
            The administrator will contact you personally via mail. If you receive no further information, feel free to contact <a href="mailto:godsofmanyisles@gmail.com">godsofmanyisles@gmail.com</a> for further information.
        </p>
    </body>
    </html>
MYGREATMAIL;
    mail ($pemail, $subject, $message, $headers);
    header("Location: mailto:".$pemail."?subject=About your Suspended Partnership");
    exit();
    }
    else if ($status == "suspended") {
            $subject = "Partnership Reactivated";
    $headers = "From: pantheon@manyisles.ch" . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $message = <<<MYGREATMAIL

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://manyisles.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Your Partnership was Reactivated</h1>
    <p style="
            text-align: center;
            font-size: calc(8px + 0.9vw);
            color: black;
            padding:10px;
    ">
        An administrator of the Homeland Institute of Trade reactivated your suspended partnership, and all your products are now visible again in the digital library.
    </p>
</body>
</html>
MYGREATMAIL;
    mail ($pemail, $subject, $message, $headers);
    header("Location: admin.php");
    exit();
    }
    else {
    header("Location: admin.php");
    exit();
    }
}



?>
