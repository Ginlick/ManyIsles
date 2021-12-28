<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

if(!isset($_COOKIE["loggedIn"])){header("Location: Account.html?error=signingIn");exit();}

$id = $_COOKIE["loggedIn"];
$uname = "";

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $title = $row["title"];
      $checkpsw = $row["password"];
    }
}
$redirect = "Account.html";
include("../Server-Side/checkPsw.php");


$query = "SELECT * FROM partners WHERE account = '".$uname."'";
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $pid = $row["id"];
      $pname = $row["name"];
      $pimage = $row["image"];
      $pjacob = $row["jacob"];
      $status = $row["status"];
      $ptype = $row["type"];
    }
}

if ($status != "active" and $status != "suspended"){header("Location: BePartner.php");exit();}

$dsextension = false;
$query = 'SELECT acceptCodes FROM partners_ds WHERE id = '.$pid;
if ($result = $conn->query($query)){
    if (mysqli_num_rows($result) != 0) {
        $dsextension = true;
    }
}


?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="../Imgs/Favicon.png">
    <title>Partnership</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/GGMdl.css">
<style>
    .procol {
    text-align:center;
    display:block;
    padding:0;
    }
        .inMod {
            background-image: url('https://www.worldanvil.com/uploads/images/652919222e67172cd20efdffbd6d9bd9.jpg');
            background-attachment: scroll;
            position: fixed; /* Stay in place */
            z-index: 3; /* Sit on top */
            left: 0;
            top: 0;
            width: 60%;
            max-height: 80%;
            height: auto;
            margin-left: 20%;
            margin-top: 10%;
            z-index: 7;
            display: none;
        }
.contentBlock {
    height:auto;
}
        .dsButton {
    background-color: #d1a720;
    border-radius: 10px;
    padding: 9px;
    font-size: calc(14px + 0.5vw);
    font-weight: normal;
    display: inline;
    margin: 0 10px 40px;
}

    .dsButton:hover {
        background-color: #f0c026;
        transition: .2s ease;
        cursor: pointer;
    }
</style>
</head>
<body>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
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
            <li> <a href="SignedIn.php?display=Part">&lt Back</a></li>
        </ul>
    </div>

<h1><?php echo $pname?>, by <?php echo $uname?>!</h1>
<?php

if ($ptype=="prem") {echo "<div><img src='../Imgs/Ranks/HighMerchant.png' alt:'Oopsie!' style='width:96%;display:block;margin:auto;'></div>";}
else {echo "<div><img src='../Imgs/Ranks/Trader.png' alt:'Oopsie!' style='width:96%;display:block;margin:auto'></div>";}
?>

<p>Thanks for your support, your products are in good hands in this community! You're making so many gaming nights better!</p>

</div>

<?php
if ($status == "suspended"){
    echo '
 <div class="contentBlock">
    <h1>Partnership Suspended</h1>
    <p>The Homeland Institute of Trade temporarily suspended your partnership. If you have any questions, please contact <a href="mailto:godsofmanyisles@gmail.com">godsofmanyisles@gmail.com</a>.<br>
    A number of publishing features will not work until your account is reactivated.    </p>
</div>
';
}


?>

    <div class="contentBlock">
    <h1>Publish a new Product</h1>
    <p>New products need to follow certain <a href="/wiki/h/publishing/pubguide.html" target="_blank">guidelines</a>. Please make sure you understand them before attempting to publish.</p>
<button class="popupButton"><a href="PubProd.php" style="color:white">Publish</a></button>
</div>

    <div class="contentBlock" style="padding-bottom:3vw;">
    <h1>Published Products</h1>
<p>Edit your previously published products here.</p>
<?php
$query = 'SELECT * FROM products WHERE partner = "'.$pname.'"';
    if ($firstrow = $conn->query($query)) {
    if ($firstrow!=null){echo "<h3>Modules</h3>";}
    while ($row = $firstrow->fetch_assoc()) {
    $column = "<a class='procol' href='Product.php?id=22&t=m'>MEHA</a>";
    $column = str_replace("MEHA", $row["name"], $column);
    $column = str_replace("22", $row["id"], $column);
    echo $column;
    }
}
$query = 'SELECT * FROM diggies WHERE partner = "'.$pname.'"';
    if ($firstrow = $conn->query($query)) {
    if ($firstrow!=null){echo "<h3>Tools</h3>";}
    while ($row = $firstrow->fetch_assoc()) {
    $column = "<a class='procol' href='Product.php?id=22&t=d'>MEHA</a>";
    $column = str_replace("MEHA", $row["name"], $column);
    $column = str_replace("22", $row["id"], $column);
    echo $column;
    }
}
$query = 'SELECT * FROM art WHERE partner = "'.$pname.'"';
    if ($firstrow = $conn->query($query)) {
    if ($firstrow!=null){echo "<h3>Art</h3>";}
    while ($row = $firstrow->fetch_assoc()) {
    $column = "<a class='procol' href='Product.php?id=22&t=a'>MEHA</a>";
    $column = str_replace("MEHA", $row["name"], $column);
    $column = str_replace("22", $row["id"], $column);
    echo $column;
    }
}
?>

    </div>

    <div class="contentBlock">
    <h1>Your <?php if ($ptype=="prem" OR $dsextension){echo "Partnership";} else {echo "Companionship";} ?></h1>
    <p>This is the information currently visible by visitors to the Many Isles. <a href="/dl/Partner.php?id=<?php echo $pid; ?>" target="_blank">View Page</a>

<div><img src="../Imgs/Bar2.png" alt:"Bar" style='display:block;width:100%'></div>
<form  action = "RenameP.php" method="POST" enctype="multipart/form-data" class="stanForm hide">
        <div class="container">
            <img src="/dl/PartIm/<?php echo $pimage; ?>" alt="Create!" class="linkim file-upload-image">

            <input type="file" onchange="readURL(this);" id="image" name = "image" accept=".png, .jpg"/>
            <label for="image">
                <div class="overlay">
                <span><i class="fas fa-arrow-up"></i></span>
                <div class="text">.png or .jpg<br>max 250kb</div>
                 </div>
            </label>
        </div>
<h2 style="text-align:left" id="rTitle"><?php echo $pname; ?></h2>
<input id="rtInput" type ="text"  pattern="[A-Za-z0-9\'\- ]{2,15}" name="nname" placeholder="<?php echo $pname; ?>" class="sideText" />
<p style="text-align:left" id="rJacob"><?php echo $pjacob; ?></p>
<input id="rjInput" type ="text" name="njacob" placeholder="<?php echo $pjacob; ?>"  style="width:60%"/>
<input id="rpInput" type ="password" name="psw" pattern="[A-Za-z0-9]{2,}" placeholder="UniquePassword22"  style="width:60%" />

<p style="color:green;display:none" id="npInfo">Done!</p>
 <input type="submit"  class="popupButton"></input>
</form>
<div style="width:100%; height:1px;display:inline-block"></div>
<p >Please be aware that if you wish to update your profile image, it will take a little while to get confirmed and accepted.</p>
    </div>

<?php
if ($dsextension){
    echo '
 <div class="contentBlock">
    <h1>Digital Store Hub</h1>
    <p>Manage your Digital Store items and orders.</p>
<button class="dsButton"><a href="/ds/p/hub.php" style="color:white;"><i class="fas fa-arrow-right"></i> View Hub</a></button>
</div>
';
}
else {
    echo '
 <div class="contentBlock">
    <h1>Digital Store Extension</h1>
    <p>Activate a Digital Store extension to start publishing in the <a href="/ds/home.php">digital store</a>.<br>Note that this cannot be undone.</p>
<button class="dsButton"><a href="/ds/p/activate.php" style="color:white;"><i class="fas fa-arrow-right"></i> Activate</a></button>
</div>
';
}

?>



    <div class="contentBlock">
    <p>As a creator, we trust you're a good follower of the rules. For your information, all publishing undertaken in the Many Isles follows the guidelines of the <a href="https://docs.google.com/document/d/1Q1CqPuaHVOM2Bz9GsZQ9S9QvrRZmyMFVo6_Iu7fq2K8/edit?usp=sharing" target="_blank">Trader's Agreement</a>.<br>
For more information about Many Isles publishing, feel free to check out the <a href="/wiki/h/publishing/pubguide.html" target="_blank">publication guidelines</a>.</p>

        <div id="backConf" class="modal" onclick="removePops('backConf', 'conConf')">
        </div>
        <div id="conConf" class="modCol" onclick="removePops('backConf', 'conConf')">
            <div class="modContent">
                <img src="/Imgs/PopTrade.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
                <h1>Product Submitted</h1>
                <p>We've sent your product, and will publish it in a few moments. Check back here to find it under your published products!</p>
            </div>
        </div>

</div>
</div>
</body>
</html>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>
function removePops(back, con) {
    document.getElementById(back).style.display = "none";
    document.getElementById(con).style.display = "none";
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
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('.file-upload-image').attr('src', e.target.result);
        document.getElementById("rpInput").style.display="block";
    };
    reader.readAsDataURL(input.files[0]);
  }
}
    includeHTML();
    document.getElementById("rTitle").onclick = function() { document.getElementById("rTitle").style.display="none";document.getElementById("rtInput").style.display="block";document.getElementById("rpInput").style.display="block"};
    document.getElementById("rJacob").onclick = function() { document.getElementById("rJacob").style.display="none";document.getElementById("rjInput").style.display="block";document.getElementById("rpInput").style.display="block"};

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why == "noPSW"){
document.getElementById("npInfo").innerHTML = "Incorrect Password";
document.getElementById("npInfo").style.color = "Red";
document.getElementById("npInfo").style.display = "block";
}
else if (why == "wrongTitle"){
document.getElementById("npInfo").innerHTML = "Incorrect Title Characters";
document.getElementById("npInfo").style.color = "Red";
document.getElementById("npInfo").style.display = "block";
}
else if (why == "wrongBody"){
document.getElementById("npInfo").innerHTML = "Incorrect Description Characters";
document.getElementById("npInfo").style.color = "Red";
document.getElementById("npInfo").style.display = "block";
}
else if (why == "npSuccess"){
document.getElementById("npInfo").style.display = "block";
}
else if (why == "badImage"){
document.getElementById("npInfo").style.display = "block";
document.getElementById("npInfo").innerHTML = "Image too large, or not an image.";
document.getElementById("npInfo").style.color = "Red";
}
else if (why == "present"){
document.getElementById("npInfo").innerHTML = "This Partnership name already exists.";
document.getElementById("npInfo").style.color = "Red";
document.getElementById("npInfo").style.display = "block";}
else if (why == "duplicate"){
document.getElementById("npInfo").innerHTML = "This image file name already exists.";
document.getElementById("npInfo").style.color = "Red";
document.getElementById("npInfo").style.display = "block";}
else if (why == "prodPubbed"){
document.getElementById("backConf").style.display = "block";
document.getElementById("conConf").style.display = "block";
}
</script>