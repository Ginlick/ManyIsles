<?php

require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();

if (!isset($_POST['uname']) || !isset($_POST['email']) || !isset($_POST['psw']) || !isset($_POST['region'])){
  $user->go("/account/Account");
}

$madeReturn = $user->createAccount($_POST['uname'], $_POST['email'], $_POST['psw'], $_POST['region']);
if ($madeReturn !== true){
  $autofill = "uname=".$_POST['uname']."&email=".$_POST['email'];
  header("Location:Account?".$autofill."&error=".$madeReturn);
  exit();
}


//where to go after
$return = "/account/SignedIn";
$buttcon = "To Account";
if (isset($_COOKIE["seeker"])){
  $return = $_COOKIE["seeker"];
  $buttcon = "continue";
  setcookie("seeker", "", time() - 2200);
}
if (isset($_POST['wanttoPublish']) AND $_POST['wanttoPublish'] =="1"){
    $return = "SignedIn?show=notConfirmed";
    $buttcon = "Start Publishing";
}


if (isset($_POST["goTo"])){
    header("Location:".$_POST["goTo"]);
    exit();
}

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


<h1>Welcome to the Harbor, <?php echo $user->fullName; ?></h1>
<img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:0 auto 0;padding: 2vw 0;' class='separator'>

<p>Your email address is: <?php echo $user->email; ?></p>
<p><?php
  echo "We're happy you're here. Please confirm your email to finish setting up your account.";
  if ($_POST['wanttoPublish']=="1"){
      echo "<br><br>Once you've confirmed your email, go to \"Become Partner\" on your account page.";
  }
  echo "<br><br><span style='color:#c2c2c2;'>Note that the confirmation email might be in your spam folder. You can also always resend the link from your account page.</span>";
?></p>

 <div ><a class="popupButton" style="width:30%;" href="<?php echo $return; ?> "><?php echo $buttcon; ?></a></div>
</div>

</body>
</html>
<script src="/Code/CSS/global.js"></script>
