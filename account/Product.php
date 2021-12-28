<?php

if(!isset( $_GET['id'])){header("Location: Publish.php");exit();}
if (preg_match("/^[0-9]{1,}$/", $_GET['id'])!=1){header("Location: Publish.php");exit();}
if (preg_match("/^[a-z]{1}$/", $_GET['t'])!=1){header("Location: Publish.php");exit();}
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
$type = $_GET["t"];
if ($type == "m"){$longtype = "module";}
else if ($type == "d"){$longtype = "tool";}
else if ($type == "a"){$longtype = "art";}
$uname = "";

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $title = $row["email"];
      $curpsw = $row["password"];
    }
    }
 $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query = "SELECT * FROM partners WHERE account = '".$uname."'";
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_row()) {
      $pid = $row[0];
      $pname = $row[1];
      $pimage = $row[2];
      $pjacob = $row[5];
      $status = $row[6];
    }
    }

if ($status != "active"){header("Location: BePartner.php");exit();}


$query = 'SELECT * FROM products WHERE id = '.$_GET['id'];
if ($type == "d"){$query = str_replace("products", "diggies", $query);}
if ($type == "a"){$query = str_replace("products", "art", $query);}
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_row()) {
      $rname = $row[1];
      $rimage = $row[2];
      $rpartner = $row[3];
      $rtype = $row[4];
      $rjacob = $row[6];
      $rlink = $row[7];
    }
    }

if ($rpartner != $pname){header("Location: Publish.php");}


?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Product | Partnership</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/GGMdl.css">
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
<h1><?php echo $rname?>, by <?php echo $pname?></h1>

    <p>Edit everything about this <?php echo $longtype; ?> here. <a href="/dl/View.php?id=<?php echo $_GET['id']; ?>&t=<?php echo $type; ?>" target="_blank">View Page</a>

<div><img src="../Imgs/Bar2.png" alt:"Bar" style='display:block;width:100%'></div>

<form  action = "RenameR.php" method="POST" enctype="multipart/form-data" class="stanForm hide">
        <div class="container">
            <img src="/IndexImgs/<?php echo $rimage; ?>" alt="Create!" class="linkim file-upload-image">

            <input type="file" onchange="readURL2(this);" id="image" name = "image" accept=".png, .jpg"/>
            <label for="image">
                <div class="overlay">
                <span><i class="fas fa-arrow-up"></i></span>
                <div class="text">.png or .jpg<br>max 250kb</div>
                 </div>
            </label>
        </div>
<h2 style="text-align:left" id="rTitle"><?php echo $rname; ?></h2>
<input id="rtInput" type ="text" name="nname" value="<?php echo $rname; ?>"  class="sideText" />
<p style="text-align:left" id="rJacob"><?php echo $rjacob; ?></p>
<input id="rjInput" type ="text" name="njacob" value="<?php echo $rjacob; ?>"   class="sideText" />
<input type ="text" display="none" name="rId" style="display:none" value="<?php echo $_GET['id']; ?>">
<input type ="text" display="none" name="rType" style="display:none" value="<?php echo $type; ?>">
<p style="color:green;display:none" id="npInfo">Done!</p>
 <input type="submit"  class="popupButton" ></input>
</form>
<div style="width:100%; height:1px;display:inline-block"></div>
<p >Please be aware that if you wish to update the product's thumbnail, it will take a short while to complete the change.</p>
</div>

    <div class="contentBlock" style="padding-bottom:3vw;">

<h2>Update File</h1>

<form  action = "NewFileR.php" method="POST" enctype="multipart/form-data" class="stanForm">
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <div class="file-upload">
      <div class="image-upload-wrap">
        <input class="file-upload-input" type='file' onchange="readURL(this);" name="file" id="file-upload-input" accept="application/pdf" />
        <div class="drag-text">
          <p>Drag and drop a file, or click to upload (max 30mb)</p>
        </div>
      </div>
      <div class="file-upload-content">
        <p class="image-title">Uploaded File</p>
        <div class="image-title-wrap">
          <button type="button" onclick="removeUpload()" class="remove-image">Remove </button>
        </div>
      </div>
    </div>
    <input id="linkInput" type ="text" name="link" placeholder="<?php echo $rlink; ?>"  class="sideText" style="display:none;margin:0.3vw auto 1.4vw auto" />

            <input type="text" name="rId" value="<?php echo $_GET["id"]; ?>" style="display:none" />
            <input type="text" name="rType" value="<?php echo $type; ?>" style="display:none" />
        <p style="color:red;display:none" id="fInfo">Upload Failed</p>
     <input type="submit"  class="popupButton" style="width:8vw;margin:0 auto 2vw;" id="apui"></input>
</form>
   </div>



    <div class="contentBlock" style="padding-bottom:3vw;">
<h2>Statistics</h2>
<p>Sorry, we're still working on this. Check back in a later version of the Many Isles to find out how your product is doing!</p>
</div>

<div class="contentBlock">
<h2>Delete Product</h2>
<p>You can delete your product here. All information about it will be lost, forever.</p>
 <button class="popupButton" style="background:#363636;" onclick="document.getElementById('backDel').style.display='block';document.getElementById('conDel').style.display='block'">Delete</button></div>
</div>


        <div id="backDel" class="modal" onclick="removePops('backDel', 'conDel')">
        </div>
        <div id="conDel" class="modCol">
            <div class="modContent"  style="background:black;">
                <img src="/Imgs/PopTrade.png" alt="Hello There!" style="width: 100%; display: inline-block " />
                    <h1 style="color:grey"> Delete Product </h1>
                    <p style="color:grey">Please confirm the deletion of your great work. This deletion is permanent and irreversible, and all files and information is immediately deleted.</p>
                    <form action="DelProd.php" method="get">
                        <input type="password" name="psw" placeholder="uniquePassword22" style="width:80%;margin: 10px auto 10px auto"/>
                        <input type="text" name="id" value="<?php echo $_GET["id"]; ?>" style="display:none" />
                        <input type="text" name="type" value="<?php echo $type; ?>" style="display:none" />
                        <p id="delWrongPsw" style="color:red;display:none">Incorrect Password.</p>
                        <button class="popupButton" type="submit">OK</button>
                    </form>
            </div>
        </div>
</body>
</html>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
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
    document.getElementById("rTitle").onclick = function() { document.getElementById("rTitle").style.display="none";document.getElementById("rtInput").style.display="block";};
    document.getElementById("rJacob").onclick = function() { document.getElementById("rJacob").style.display="none";document.getElementById("rjInput").style.display="block";};
function readURL2(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('.file-upload-image').attr('src', e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}
function removePops(back, con) {
    document.getElementById(back).style.display = "none";
    document.getElementById(con).style.display = "none";
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

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why == "delFail"){
document.getElementById("delWrongPsw").style.display = "block";
document.getElementById('backDel').style.display='block';
document.getElementById('conDel').style.display='block';
}
else if (why=="present"){
    document.getElementById("npInfo").style.display = "block";
    document.getElementById("npInfo").style.color = "red";
    document.getElementById("npInfo").innerHTML = "Title already exists";
}
else if (why=="duplicate"){
    document.getElementById("npInfo").style.display = "block";
    document.getElementById("npInfo").style.color = "red";
    document.getElementById("npInfo").innerHTML = "Image Name already Present";
}
else if (why=="badImage"){
    document.getElementById("npInfo").style.display = "block";
    document.getElementById("npInfo").style.color = "red";
    document.getElementById("npInfo").innerHTML = "Image too large, or not an image.";
}
else if (why=="npSuccess"){
    document.getElementById("npInfo").style.display = "block";
}
else if (why=="ffail"){
    document.getElementById("fInfo").style.display = "block";
}
else if (why=="fbadtitle"){
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").innerHTML = "Transfer of uploaded file failed.";
}
else if (why=="fsuccess"){
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").style.color = "green";
    document.getElementById("fInfo").innerHTML = "Done!";
}
    var type = "<?php echo $type; ?>";
    if (type == "m"){
        $('.file-upload').show();
        document.getElementById("file-upload-input").setAttribute("accept", "application/pdf");
        document.getElementById("linkInput").style.display="none";
    }
    else if (type == "d"){
        $('.file-upload').hide();
        document.getElementById("linkInput").style.display="inline-block";
    }
    else if (type == "a"){
        $('.file-upload').show();
        document.getElementById("file-upload-input").setAttribute("accept", ".png, .jpg");
        document.getElementById("linkInput").style.display="none";
    }
</script>