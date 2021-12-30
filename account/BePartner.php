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
if(!isset($_COOKIE["loggedIn"])) {header("Location: Account.html?error=wannaPublish");exit();}
$id = $_COOKIE["loggedIn"];


  $getNameRow = "SELECT uname FROM accountsTable WHERE id = ".$id;
  $nameresult =  $conn->query($getNameRow);
  $name ="";
  while ($row = $nameresult->fetch_row()) {$name = $row[0];}
  if ($name == "") {setcookie("loggedIn", "", time() -3600, "/");header("Location: Account.html");exit();}

  $getMailRow = "SELECT email FROM accountsTable WHERE id =".$id;
  $mailresult =  $conn->query($getMailRow);
  $mail ="";
  while ($row = $mailresult->fetch_row()) {$mail = sprintf ("%s", $row[0]);}

 $getMailRow = "SELECT emailConfirmed FROM accountsTable WHERE id = ".$id;
  $mailresult =  $conn->query($getMailRow);
  $conf ="";
  while ($row = $mailresult->fetch_row()) {$conf = $row[0];}


$query = 'SELECT * FROM partners WHERE account = "'.$name.'"';
$status = "";

if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $status = $row["status"];
    }
}

if ($status != ""){
header("Location: PubProd.php");exit();
}

?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Become Partner</title>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="g/GGMdl.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
</head>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>

<body onload="checkCookie()">
    <div style="flex: 1 0 auto;">

    <div w3-include-html="/Code/CSS/GTopnav.html"></div>

    <div class="contentBlock" style="margin-top:5vw;">
        <div class="banner" style="position:static">
            <picture>
                <source srcset="/Imgs/BannerDL.png" media="(max-width: 1400px)">
                <source srcset="/Imgs/BigBannerDL.png">
                <img src="/Imgs/BigBannerDL.png" alt="Banner" style='width:100%;display:block'>
            </picture>
        </div>

<div class="mednav">
        <ul>
            <li> <a href="SignedIn.php">&lt Back</a></li>
        </ul>
    </div>
    <?php
          if ($conf == ""){
            echo "<h1>Confirm Email</h1>
            <div><img src='/Imgs/Recruit.png' alt:'Oopsie!' style='width:96%;display:block;margin:auto'></div>
            <p>Quickly confirm your email to become a trader!<br></p>
            <button class='popupButton'><a href=''resendConfirm.php?id=41'<fas ></a></button>
            </div></div>
            ";


            exit();
          }

    ?>

<h1>Let's make you a Trader!</h1>
<div><img src='../Imgs/Ranks/HighMerchant.png' alt:'Oopsie!' style='width:96%;display:block;margin:auto'></div>
<p>With a basic companionship, you'll be able to publish content in our digital library - for free. You'll also glean the Trader title. For more information, see the <a href="/wiki/h/partnership.html" target="_blank">wiki</a>.
</p>
</div>

<div class="contentBlock">

<form action="SubPar.php" method="POST" class="stanForm" enctype="multipart/form-data">

        <div class="container">
            <img src="/dl/PartIm/Traveler.png" alt="Create!" class="linkim file-upload-image">
            <input type="file" onchange="readURL(this);" id="image" value="null" name = "image" accept=".png, .jpg"/>
            <label for="image">
                <div class="overlay">
                <span><i class="fas fa-arrow-up"></i></span>
                <div class="text">.png or .jpg<br>max 250kb</div>
                 </div>
            </label>
        </div>

<input type ="text"  pattern="[A-Za-z0-9\'\- ]{2,15}" name="pname" placeholder="Hansfried's Guildshop (only letters, numbers, ' and - )" class="sideText" required />
<textarea name="jacob" rows = "22" class="textBlock"  required> I make great lore for the ravenous orcs of northern Balebu and Intralu.</textarea>
<input type="password" placeholder="uniquePassword22" name="psw" required><br>

<p id="notifier">Don't be overly sexual or mean, and find a nice fitting picture.</p>
<button onclick="next()" class="popupButton">Next</button>

        <div id="modal" class="modal"  onclick="doPops()">
        </div>
        <div id="mod" class="modCol">
            <div class="modContent">
                <img src="../Imgs/PopTrade.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
                <h1>Publishing Terms</h1>
                <p>By publishing to the Many Isles, a trader acknowledges and understands the rules set by the Homeland Institute of Trade and the Pantheon.</p>
<p style="text-align:left">
&sect;0 Definitions<br />
&sect;0.1 Trader and Partner<br />
A trader is any person that publishes within the Many Isles. Partner, as a quasi-synonym, is a denomination for any owner of a partnership with the Many Isles.<br />
&sect;0.2 Products and Publishing<br />
In the Many Isles, a product is any thing - pdf, link, or image - posted by a partnership in the goal of sharing it to the community. The act of publishing is performed whenever a trader performs any action within the Many Isles publishing system, such as submitting a product.<br />
&sect;0.3 Partnership<br />
A partnership is the publishing entity that owns products published in the Many Isles digital library, which in turn is owned by an adventurer holding an account in the Many Isles and administered by Many Isles administrators.<br />
<br />&sect;1 Recognition<br />
&sect;1.1 Recognition of the Trader&rsquo;s Agreement<br />
The trader recognizes and accepts that all publishing actions taken within the Many Isles library are performed under the rules set by the Trader&rsquo;s Agreement. All actions concerning partnerships and their products are written in that document, which forms the base for all publishing. They further recognize that the Pantheon and Homeland Institute of Trade may at any time alter the Trader&rsquo;s Agreement for the benefice and efficacy of all.<br />
&sect;1.2 Recognition of the Trade Code<br />
The trader recognizes that as soon as they close a partnership with the Many Isles, they join the Homeland Institute of Trade and therefore underlie the institution&rsquo;s rules, represented in the Trade Code.<br />
&sect;1.3 Recognition of the Homeland Institute of Trade<br />
The trader recognizes that the entity directly administering publishing in the Many isles is the Homeland Institute of Trade, and that cooperation with this institution form the basis of any publishing.<br />
&sect;1.4 Recognition of the Adventurer&rsquo;s Agreement<br />
The trader, as an adventurer, recognizes and accepts the Adventurer&rsquo;s Agreement as core rules of the entire Many Isles community, and that it is the base for all, including the publishing system. They further understand that they must follow the Agreement&rsquo;s rules in addition to specific trade rules.<br />
&sect;1.5 Recognition of the Pantheon<br />
The trader, as an adventurer, recognizes that the Pantheon serves as final ruling authority over the Many Isles, and that it holds ultimate power over publishing and the trade institution.<br />
<br />&sect;2 Obligations of the Partner<br />
&sect;2.1 Obedience to Rules and Administrators<br />
The trader accepts that they must follow all instructions posed by applicable rules such as the Trader&rsquo;s Agreement. They also accept that administrators of the Homeland Institute of Trade as well as the Pantheon have, within the bounds of the Adventurer&rsquo;s Agreement and Trader&rsquo;s Agreement, total control on a partnership and its products.<br />
&sect;2.2 Partnership<br />
The trader&rsquo;s partnership must follow the Adventurer&rsquo;s Agreement, such as (1) an absence of vulgar or highly sexual language; (2) a sufficient quality throughout it.<br />
&sect;2.3 Product Requirements<br />
A product must follow the restrictions imposed by the Trader&rsquo;s Agreement, including but not restricted to: (1) absence of vulgar or highly sexual language; (2) ownership of the product; (3) following of the Wizards of the Coast&rsquo;s OGL; (4) correct title page layout; (5) no self-promotion except at the end of a document.<br />
<br />&sect;3 Rights of the Partner<br />
&sect;3.1 Right of Recursion<br />
The Trader&rsquo;s Agreement serves as the final decision in any situation concerning trade. Although administration may change the Agreement at any time, a trader may not be condemned for breaks against the Agreement that hadn&rsquo;t been outlined at the time of the break. <br />
&sect;3.2 Right of Fairness<br />
The Pantheon grants leeway to traders, and treats them fairly. If a problem were hidden or manipulated, the Pantheon will act in the best way possible for the trader and itself; it will not attempt to exploit the trader.<br />
&sect;3.3 Right of Preservation<br />
It is impossible for the Pantheon or trade administration to delete any product or partnership, or cause any lasting changes to a partnership or its products. Only a partner may edit and delete products and partnerships. This right has two exceptions: (1) The Pantheon or trade administration can at any time suspend a partnership according to &sect;3.6 of the Adventurer&rsquo;s Agreement, causing no lasting changes but temporarily removing all products from the digital library; (2) The Pantheon may start a salvation period and dissolve a partnership, transferring ownership of products and deleting a partnership, in certain very specific cases, as per &sect;3.7 of the Adventurer&rsquo;s Agreement.<br />
&sect;3.4 Right of Ownership<br />
Any products published by a partnership are fully owned by it. Any profits made through that product are compounded in the partner&rsquo;s interest, and the Many Isles have no ownership claims over the product. The Many Isles can take action upon products in certain cases:<br />
a. A partnership is suspended, in which case all products are temporarily invisible in the digital library, the partner can take no actions in publishing, and the partnership&rsquo;s value is reduced to 0.<br />
b. A partnership is dissolved, in which case the Pantheon may claim ownership over some or all of a partnership&rsquo;s products before deleting it in the dissolving process.<br />
&sect;3.5 Right of Payment<br />
A true partnership (instead of a companionship) has unlimited access to its funds and revenue according to its value as per &sect;2 and &sect;3 of the Adventurer&rsquo;s Agreement, which the Many Isles can in no way keep away from the partner. This right may be impeded by the suspension of a partnership, as per &sect;3.6 of the Adventurer&rsquo;s Agreement.<br /></p>
<p>The trader recognizes that by following the terms laid in the Publishing Terms, as well as all the rules and administrators it refers to, a positive communal relationship can be established and that all rights portrayed in &sect;3 apply without limitations.</p>
<br><br>
These terms can be viewed at all times <a href="https://docs.google.com/document/d/1mMFbIavXfvzZhso1hIECrFjRSWlqA8RbQHreBNha9zw/edit?usp=sharing" target="_blank">here</a>.</p>
                <input type="submit" class="popupButton">
            </div>
        </div>
</form>

</div>






<div class="contentBlock">
<p>Once you submit your application, you'll need to wait a day or two, and we'll send you a mail to tell you it was approved. You'll be able to start posting then!<br>
Partnerships invloving paid content are a bit more complicated. Please take it up with the Pantheon, eg. at <a href="mailto:godsofmanyisles@gmail.com">godsofmanyisles@gmail.com</a>.

</div>

</body>
</html>
<script src="AccountFunctions.js">
</script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>
function next() {
            document.getElementById("modal").style.display = "block";
            document.getElementById("mod").style.display = "block";
}
function doPops() {
            document.getElementById("modal").style.display = "none";
            document.getElementById("mod").style.display = "none";
}

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why == "noPSW"){
    document.getElementById("notifier").style.color="red";
    document.getElementById("notifier").innerHTML = "Incorrect Password.";
}
else if (why == "present"){
    document.getElementById("notifier").style.color="red";
    document.getElementById("notifier").innerHTML = "Please choose another partnership name; this one is taken.";
}
else if (why == "wrongTitle"){
    document.getElementById("notifier").style.color="red";
    document.getElementById("notifier").innerHTML = "Incorrect character format in title.";
}
else if (why == "wrongBody"){
    document.getElementById("notifier").style.color="red";
    document.getElementById("notifier").innerHTML = "Incorrect character format in description.";
}
else if (why == "badImage"){
    document.getElementById("notifier").style.color="red";
    document.getElementById("notifier").innerHTML = "Image too large, or not an image.";
}
function readURL(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {

      $('.file-upload-image').attr('src', e.target.result);

    };

    reader.readAsDataURL(input.files[0]);

  }
}
    function includeHTML() {
        var z, i, elmnt, file, xhttp;
        z = document.getElementsByTagName("*");
        for (i = 0; i < z.length; i++) {
            elmnt = z[i];
            file = elmnt.getAttribute("w3-include-html");
            if (file) {
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4) {
                        if (this.status == 200) { elmnt.innerHTML = this.responseText; }
                        if (this.status == 404) { elmnt.innerHTML = "Page not found."; }
                        elmnt.removeAttribute("w3-include-html");
                        includeHTML();
                    }
                }
                xhttp.open("GET", file, true);
                xhttp.send();
                return;
            }
        }
    }
    includeHTML();
</script>
