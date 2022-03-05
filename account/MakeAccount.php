<?php

$return = "/account/SignedIn";$buttcon = "To Account";
if (isset($_COOKIE["seeker"])){
  $return = $_COOKIE["seeker"];
  $buttcon = "continue";
  setcookie("seeker", "", time() - 2200);
}


if (preg_match("/[A-Za-z0-9 ]{2,}/", $_POST['uname'])!=1){$redirect = "uname";}
else if (preg_match("/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $_POST['email'])!=1){$redirect = "email";}
else if (preg_match("/[A-Za-z0-9]{1,}/", $_POST['psw'])!=1){$redirect = "psw";}
else if (preg_match("/[1-3]/", $_POST['region'])!=1){$redirect = "reg";}
else {$redirect = "false";}
if (isset($_POST['wanttoPublish'])) {if (preg_match("/[0-1]/", $_POST['wanttoPublish'])!=1){$redirect = "uname";} else {$redirect = "false";}}

$autofill = "uname=".$_POST['uname']."&email=".$_POST['email'];


if ($redirect != "false"){
    header("Location:Account.html?".$autofill."&uninput=".$redirect);
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

//spamkill
$query = "SELECT * FROM accountsTable order by id DESC LIMIT 22,1";
if ($result = $conn->query($query)){
    while ($row = $result->fetch_assoc()) {
        $time = $row["regdate"];
        if (strtotime($time) >= strtotime("-1 day")){
            header("Location:Account.html?".$autofill."&error=spamblock");
            exit();
        }
    }
}

$email = $_POST['email'];
if ($result = $conn->query(sprintf("SELECT email FROM accountsTable WHERE email='%s';", $email))) {
   if ($result->num_rows > 0) {
     header("Location:Account.html?".$autofill."&error=EmailTaken");
     exit();
   }
}
if ($result = $conn->query(sprintf("SELECT uname FROM accountsTable WHERE uname='%s';", $_POST['uname']))) {
   if ($result->num_rows > 0) {
     header("Location: Account.html?".$autofill."&error=UnameTaken");
     exit();
   }
}

$hashedPsw = password_hash($_POST['psw'], PASSWORD_DEFAULT);
$sql = sprintf(
  "INSERT INTO accountsTable (uname, title, email, region, password) VALUES ('%s', 'Adventurer', '%s', %s, '%s');",
  $_POST['uname'],
  $_POST['email'],
  $_POST['region'],
  $hashedPsw);

$id = "";
if ($conn->query($sql)) {
  $user = new adventurer();
  if (!$user->signIn($_POST['uname'], $_POST['psw'])){
    $query = "DELETE FROM accountsTable WHERE uname = '".$_POST['uname']."'"; $conn->query($query);
    header("Location: Account.html?".$autofill."&error=dataPlacing");
  }
}
else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

//confirmer
$conCode = "";
require_once($_SERVER['DOCUMENT_ROOT']."/account/newConfCode.php");


$to = $_POST['email'];
$subject = "Welcome!";
$message = <<<MYGREATMAIL
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>

    <div style="width: 100%;background-color: #61b3dd;margin:0;min-height:100vh;padding:2vw;box-sizing: border-box;">
        <img src="https://manyisles.ch/Imgs/Favicon.png" style="width:10%;padding:1vw 1vw 0 1vw" />
        <h2 style="color:white;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;display:inline-block;font-size:8vw; margin:0;transform: translate(0, -22%);">Many Isles</h2>
        <div style="background-color:white;padding:1vw;border-radius:22px">
            <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;margin-bottom:0;">Welcome to the Many Isles!</h1>
            <p style="text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;margin-right:5%;margin-left:5%;">
                We're happy that you've decided to join us. You'll get free premium products, as well as access to our great community! We love worldbuilding and homebrewing, and you'll fit right in.<br />
            </p>

            <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
            <div style="width:20%;float:left;display:block;position:relative">
                <img src="http://manyisles.ch/IndexImgs/Pen.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
            </div>
            <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">An awesome Fandom</h2>
            <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
                The Many Isles has a cool <a href="https://www.manyisles.ch/fandom/home" target="_blank">fandom wiki</a>, accessible to all. Once you've confirmed your email, you can participate your own articles and even write a whole wiki about your own world!
            </p>

            <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
            <div style="width:20%;float:left;display:block;position:relative">
                <img src="http://manyisles.ch/Imgs/Prods.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
            </div>
            <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">An awesome Digital Library</h2>
            <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
                Our main goal is to promote small creators that want to get their awesome free stuff out there. Check out our <a href="https://manyisles.ch/dl/Goods">digital library</a> today, and start publishing from your <a href="https://manyisles.ch/account/SignedIn">account page</a>!
            </p>

            <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
            <div style="width:20%;float:left;display:block;position:relative">
                <img src="http://manyisles.ch/IndexImgs/Bless.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
            </div>
            <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">Our Recommendation</h2>
            <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
                Check out the great <a href="https://www.manyisles.ch/dl/View?id=17&t=m">Handbook of Blessings</a> published by the Pantheon, and gift your party with cool minor blessings to reward them supernaturally for their efforts! Feel free to explore the digital library beyond this afterwards ;)
            </p>

            <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
            <div style="width:20%;float:left;display:block;position:relative">
                <img src="http://manyisles.ch/Imgs/disct.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
            </div>
            <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">Join Us</h2>
            <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
                We share epic brews on our <a href="https://discord.gg/F7ZvRckpC9" target="_blank">discord server</a>. Join us to become part of the community!
            </p>

            <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;padding:16px;box-sizing:border-box;" />
            <h2 style="text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;">Confirm Email</h2>
            <p style="text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
                By clicking the button below, you'll confirm your email to permanently gain all these benefits and become a true member of the Many Isles!
            </p>
            <button class="popupButton" style="margin:2vw auto 2vw;padding:10px;display:block;background-color:red;border:0px;border-radius:4px;font-weight:bold;color:white;font-size:20px;"><a href="http://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife" style="text-decoration:none;color:white;">Confirm and Join</a></button>
            <p style='color: #7d7d7d; font-size: calc(10px + 0.4vw); text-align: center; '>If the button does not work, try moving the email out of your spam folder, or paste this link into your browser: <a href="http://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife">http://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife</a> </p>

        </div>
    </div>

</body>
</html>
MYGREATMAIL;
$headers = "From: pantheon@manyisles.ch" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$message =  str_replace("massiveTreeofLife", $conCode, $message);

if (isset($_POST['wanttoPublish']) AND $_POST['wanttoPublish'] =="1"){
    $return = "SignedIn.php?show=notConfirmed";
    $buttcon = "Start Publishing";
}

$conn->close();
?>


<html>
<head>
    <meta charset="UTF-8" />
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Account</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/acc.css">

</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html"></div>


<h1>Welcome to the Harbor, <?php echo $_POST["uname"]; ?></h1>
<img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:0 auto 0;padding: 2vw 0;' class='separator'>

<p>Your email address is: <?php echo $to; ?></p>
<p><?php if (mail ($to, $subject, $message, $headers))
  {echo "We're happy you're here. Your boat can safely dock in Denise, city of merchants, but we're still waiting for the messenger to confirm your identity.<br> <span style='color:red'> Please confirm your email to finish setting up your account.</span>";
    if ($_POST['wanttoPublish']=="1"){
        echo "<br><br>Once you've confirmed your email, you can become a partner from your account page on.";
    }
    echo "<br><br><span style='color:#c2c2c2;'>Note that the email might be in your spam folder. You can also always resend the link from your account page.</span>";
}
else
  {echo "Oops, your mail couldn't be sent. Try again from your account page.";
   }

if (isset($_POST["goTo"])){
    header("Location:".$_POST["goTo"]);
    exit();
}

?></p>

 <div ><a class="popupButton" style="width:30%;" href="<?php echo $return; ?> "><?php echo $buttcon; ?></a></div>
</div>

</body>
</html>
<script src="/Code/CSS/global.js"></script>
