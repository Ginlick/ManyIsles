<html>
<body>

<?php

$subject = "Companionship Closed";
$message = <<<MYGREATMAIL

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://manyisles.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Welcome, Bishop!</h1>s
    <p style="text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;margin-right:5%;margin-left:5%;">
    By creating a companionship with the Many Isles, you've joined a thriving community. Present us your greatest achievements!    </p>
    <img src="http://manyisles.ch/Imgs/Bar.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <div style="width:20%;float:left;display:block;position:relative">
        <img src="http://manyisles.ch/dl/PartIm/Bishop.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
    </div>
    <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;">Share your Stuff</h2>
    <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
        We have a great digital library that has collected thousands of views on many products, and hundreds of downloads. This customer base is now open to you!
    </p>
    <img src="http://manyisles.ch/Imgs/Bar.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <div style="width:20%;float:left;display:block;position:relative">
        <img src="http://manyisles.ch/IndexImgs/Exotic.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
    </div>
    <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;">Great Tools</h2>
    <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
        Thanks to your contributions, the Many Isles exist. As a return, we throw open all tools for you, such as the <a href="https://drive.google.com/drive/folders/1ngOo5Gfe7k-gxWBZourBSUWQunAxiGm6?usp=sharing">Merchant's Wagon</a>.
    </p>
    <img src="http://manyisles.ch/Imgs/Bar.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <div style="width:20%;float:left;display:block;position:relative">
        <img src="http://manyisles.ch/IndexImgs/Spellcraft.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;" />
    </div>
    <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;">Be Responsible</h2>
    <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
    Although we grant you as a creator a special place among us, please follow the rules. Make sure you read through our <a href="https://manyisles.ch/dl/pubguide.html">publication guide</a>, and that you agree with the <a href="https://docs.google.com/document/d/1Q1CqPuaHVOM2Bz9GsZQ9S9QvrRZmyMFVo6_Iu7fq2K8/edit?usp=sharing">Trader's Agreement</a>.
    </p>

</body>
</html>

MYGREATMAIL;
$headers = "From: pantheon@manyisles.ch" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//$target = "aufregendetage@gmail.com";


if (mail ($target, $subject, $message, $headers))
  {echo "Sorry very sorry oops i succeeded master ".$target;}
else
  {echo "I didn't send the mess, 4 probably.";}
?>
</body>
</html>