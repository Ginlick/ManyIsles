<?php

if(!isset($_COOKIE["loggedIn"])){header("Location: Account.html?error=notSignedIn");exit();}
if(!isset($_COOKIE["loggedP"])){header("Location: Account.html?error=notSignedIn");exit();}

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
$uname = "";

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $title = $row["title"];
      $curpsw = $row["password"];
    }
    }
 $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query = "SELECT * FROM partners WHERE account = '".$uname."'";
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $pid = $row["id"];
      $pname = $row["name"];
      $pimage = $row["image"];
      $pjacob = $row["jacob"];
      $status = $row["status"];
    }
    }

if ($status != "active"){header("Location: BePartner.php");exit();}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="../Imgs/Favicon.png">
    <title>Publish Product | Partnership</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="g/GGMdl.css">
<style>
.field input {
    width:4vw;
}
.input {
    display:block;
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
            <li> <a href="Publish.php">&lt Back</a></li>
        </ul>
    </div>
<h1>Submit Product</h1>
<p>Present your great work here. Make sure that you understand rules for publishing in the Many Isles, as explained below. We'll check your submission asap, and hopefully publish it within a few days!</p>

<div><img src="/Imgs/Bar2.png" alt:"Bar" style='display:block;width:100%'></div>
<h3>Main Info</h3>
<form  action = "SubProd.php" method="POST" enctype="multipart/form-data" class="stanForm">
<div class="container">
            <img src="/IndexImgs/GMTips.png" alt="Oh no onii-chan your image is too big UwU" class="linkim file-upload-image" >
            <input type="file" onchange="readURL2(this);" id="image" value="null" name = "image" accept=".png, .jpg" required/>
            <label for="image">
                <div class="overlay">
                <span><i class="fas fa-arrow-up"></i></span>
                <div class="text">.png or .jpg<br>max 250kb</div>
                 </div>
            </label>
        </div>
<input id="rtInput" type ="text" name="nname" placeholder="A Great Creation" pattern = "[A-Za-z0-9\&\'\- ]{2,}" class="sideText" required/>
<input id="rjInput" type ="text" name="njacob" placeholder="Make epic things with this legendary craft, adapted for all players of all kinds!"  class="sideText" required/>

<div><img src="/Imgs/Bar2.png" alt:"Bar" style='display:block;width:100%'></div>
<h3>Meta</h3>
<div style="width:40%;display:inline-block;padding:9px;float:left;"
    <label for="type">Choose a type:</label>
    <select id="type" name="type" onchange="typValue()">
      <option value="m">Module</option>
      <option value="d">Tool</option>
      <option value="a">Art</option>
    </select>
</div>
<div style="width:60%;display:inline-block;text-align:left;padding:9px;">
    <div id="modField" class="field">
      <span class="input"><input type="checkbox" onclick = "catValue('c');">classes</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('r');">races</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('u');">rules</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('a');">adventures</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('l');">lore</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('d');">DM stuff</input></span>
    </div>
    <div id="digField" style="display:none;" class="field">
      <span class="input"><input type="checkbox" onclick = "catValue('h');">homebrewing</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('r');">generator</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('i');">index/list</input></span>
    </div>
    <div id="artField" style="display:none;" class="field">
      <span class="input"><input type="checkbox" onclick = "catValue('v');">visual</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('m');">cartography</input></span>
      <span class="input"><input type="checkbox" onclick = "catValue('n');">dungeons</input></span>
    </div>
</div>
<input type="text" style="display:none" id="Categories" name="categories" value=""/>

<div style="padding:4vw 0;" id="mSpecificMeta">
    <div>
        <label for="gamesys">Choose a game system:</label>
        <select id="gamesys" name="gamesys">
            <option value="2">5e</option>
            <option value="0">Any / Other</option>
            <option value="1">5eS</option>
        </select>
    </div>
    <div style="padding-top:1vw;">
        <label for="supportProd">Suggest and receive support payments:</label>
        <select id="supportProd" name="supportProd">
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
    <input type="text" id="smartCategories" name="smartCategories" placeholder="adventurous, inspirational, flexible, arcane"/>
</div>

<div><img src="/Imgs/Bar2.png" alt:"Bar" style='display:block;width:100%'></div>
<h3>File</h3>

<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<div class="file-upload">
  <div class="image-upload-wrap">
    <input class="file-upload-input" type='file' onchange="readURL(this);" name="file" id="file-upload-input" accept="application/pdf" />
    <div class="drag-text">
      <p>Drag and drop a file, or click to upload (max 30 MB)</p>
    </div>
  </div>
  <div class="file-upload-content">
    <p class="image-title">Uploaded File</p>
    <div class="image-title-wrap">
      <button type="button" onclick="removeUpload()" class="remove-image">Remove </button>
    </div>
  </div>
</div>
<input id="linkInput" type ="text" name="link" placeholder="url to the tool"  class="sideText" style="display:none;margin:0.3vw auto 1.4vw auto" />


        <p id="fInfo" style="text-align:center; font-size:18px;color:red;display:none">Submission Failed.</p>
 <input type="submit"  class="popupButton" style="width:8vw;margin:0 auto 2vw;" onclick="imagePrompt();"></input>
</form>
   </div>



    <div class="contentBlock" style="padding-bottom:3vw;">
<h1>Rules </h1>
<p>Please understand that restrictions are placed on your product, both by the Wizards and the OGL, and the Many Isles. To clarify these rules, we have created the <a href="/wiki/h/publishing/pubguide.html" target="_blank">publication guide</a>. Make sure you've read through it, and that your product is eligible, before submitting it here.<br>
Also make sure your document fulfills the rules below, clarified in §1.3.2b. Find necessary images in the <a href="https://drive.google.com/drive/folders/1ngOo5Gfe7k-gxWBZourBSUWQunAxiGm6?usp=sharing" target="_blank">Merchant's Wagon</a>.</p>
<ul style="text-align:left">
<li>Add the Many Isles banner, your partnership's symbol, and a fitting top bar (if necessary) on the title page.</li>
<li>Include a link to the Many Isles at the end of your document.</li>
<li>Don't perform any self-promotion throughout the product, except in the final paragraph.</li>
</ul>
</div>


</body>
</html>
<script>
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

function removePops(back, con) {
    document.getElementById(back).style.display = "none";
    document.getElementById(con).style.display = "none";
}
function readURL2(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {

      $('.file-upload-image').attr('src', e.target.result);

    };

    reader.readAsDataURL(input.files[0]);

  }
}
function readURL(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {
      $('.image-upload-wrap').hide();

      $('.file-upload-content').show();

      $('.image-title').html(input.files[0].name);
    };

    reader.readAsDataURL(input.files[0]);

  } else {
    removeUpload();
  }
}

function removeUpload() {
  $('.file-upload-input').replaceWith($('.file-upload-input').clone());
  $('.file-upload-content').hide();
  $('.image-upload-wrap').show();
}
$('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
});
function imagePrompt() {
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").style.color = "green";
    document.getElementById("fInfo").innerHTML = "Uploading... <br> do not close this tab!";
}
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why == "delFail"){
document.getElementById("delWrongPsw").style.display = "block";
document.getElementById('backDel').style.display='block';
document.getElementById('conDel').style.display='block';
}
else if (why=="present"){
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").style.color = "red";
    document.getElementById("fInfo").innerHTML = "Title already exists";
}
else if (why=="duplicate"){
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").style.color = "red";
    document.getElementById("fInfo").innerHTML = "Image Name already Present";
}
else if (why=="npSuccess"){
    document.getElementById("fInfo").style.display = "block";
}
else if (why=="ffail"){
    document.getElementById("fInfo").style.display = "block";
}
else if (why=="fbadtitle"){
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").innerHTML = "Please include only basic characters in the file name.";
}
else if (why=="badFile"){
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").innerHTML = "File could not be uploaded. This may be because it is too large, not present, or not accessible.";
}
else if (why=="badImage"){
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").innerHTML = "Image could not be uploaded. This may be because it is too large, not present, or not accessible.";
}
function typValue() {
    var type = document.getElementById("type").value;
    document.getElementById('Categories').value="";
    if (type == "m"){
        document.getElementById("modField").style.display="inline-block";
        document.getElementById("digField").style.display="none";
        document.getElementById("artField").style.display="none";
        $('.file-upload').show();
        document.getElementById("file-upload-input").setAttribute("accept", "application/pdf");
        document.getElementById("linkInput").style.display="none";
        removeUpload();   
    }
    else if (type == "d"){
        document.getElementById("modField").style.display="none";
        document.getElementById("digField").style.display="inline-block";
        document.getElementById("artField").style.display="none";
        $('.file-upload').hide();
        document.getElementById("linkInput").style.display="inline-block";
        removeUpload();
    }
    else if (type == "a"){
        document.getElementById("modField").style.display="none";
        document.getElementById("digField").style.display="none";
        document.getElementById("artField").style.display="inline-block";
        $('.file-upload').show();
        document.getElementById("file-upload-input").setAttribute("accept", ".png, .jpg");
        document.getElementById("linkInput").style.display="none";
        removeUpload();  
    }
    if (type=="m"){
        document.getElementById("mSpecificMeta").style.display="block";
    }
    else {
        document.getElementById("mSpecificMeta").style.display="none";
    }
}
function catValue(clicked) {
    var catValue = document.getElementById('Categories').value;
    if (catValue.includes(clicked)){
        newValue = catValue.replace(clicked, "");
        document.getElementById('Categories').value = newValue;
    }
    else {
        newValue = catValue.concat(clicked);
        document.getElementById('Categories').value = newValue;
    }
}
</script>