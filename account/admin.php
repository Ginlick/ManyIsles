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

if(!isset($_COOKIE["loggedIn"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}

$conn = new mysqli($servername, $username, $password, $dbname);
$id = $_COOKIE["loggedIn"];
if ($id != 11 AND $id != 14 AND $id != 26 AND $id != 36) {
    header("Location: /account/SignedIn.php");exit();
}


  $getNameRow = "SELECT uname, title FROM accountsTable WHERE id =". $_COOKIE["loggedIn"];
  $nameresult =  $conn->query($getNameRow);
  while ($row = $nameresult->fetch_assoc()) {
    $name = $row["uname"];
    $title = $row["title"];
}
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

$dsAdmin = false;
$query = 'SELECT id FROM partners WHERE account = "'.$name.'"';
if ($result = $conn->query($query)){
    if (mysqli_num_rows($result) != 0) { 
        while ($row = $result->fetch_assoc()) {
            $pId = $row["id"];
        }
        $query = "SELECT power FROM partners_ds WHERE id = $pId";
        if ($result = $conn->query($query)){
            while ($row = $result->fetch_assoc()) {
                if ($row["power"]>1){$dsAdmin = true;}
            }
        }
    }
}


?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Trade Admin</title>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="g/GGMdl.css">
<style>
    table {
        width:80%;
        margin:auto;
        text-align:left;
    }
    .longRow {
        max-width:22vw;
        overflow:hidden;
    }
</style>
</head>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>

<body>
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

<h1>Trade Administration</h1>
<div><img src='../Imgs/Ranks/HighMerchant.png' alt:'Oopsie!' style='width:96%;display:block;margin:auto'></div>
<p>As a member of the Homeland Institute of Trade's administration, you are entrusted with power. Use it responsibly.
</p>
</div>

<div class="contentBlock">
<h2>Active Partnerships</h2>
<table>
<?php

$emailFooter = "___
".$title." ".$name."
member of the Homeland Institute of Trade<br>
<br>
Homeland Institute of Trade<br>
Aspect of Homeland<br>
Many Isles Hub<br>
Many Isles";
echo "<textarea value='".$emailFooter."' style='display:none' id='myInput'></textarea>";
    $query = "SELECT id, name, account, status FROM partners WHERE status = 'active' OR status = 'suspended'";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["name"]."</td>";
    echo "<td>".$row["id"]."</td>";
    echo "<td>".$row["account"]."</td>";
    echo "<td><a class='popupButton' href='/dl/Partner.php?id=".$row["id"]."'>View</a></td>";
        $query2 = 'SELECT email FROM accountsTable WHERE uname = "'.$row["account"].'"';
        $result2 = $conn->query($query2);
        while($row2 = $result2->fetch_assoc()){
            $email = $row2["email"];
            break;
        }
    echo '<td><a class="popupButton" href="mailto:'.$email.'" target="_blank">Contact</a></td>';
    if ($row["status"]=="active"){$susp = "Suspend";}else{$susp = "Reactivate";}
    echo "<td><a class='popupButton' href='suspend.php?id=".$row["id"]."'>".$susp."</a></td>";
    echo "</tr>";
  }
}

?>
</table>
</div>

<div class="contentBlock">
<h2>Pending Partnerships</h2>
<p>Check the partnership's profile image by clicking the image, then pass their submission.</p>
<table>
<?php
    $query = "SELECT id, name, image, account FROM partners WHERE status = 'pending'";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["name"]."</td>";
    echo "<td>".$row["account"]."</td>";
    echo '<td><a href="downStuff.php?img=part&imgLink='.$row["image"].'">'.$row["image"]."</a></td>";
    echo "<td><a class='popupButton' href='pass.php?what=part&id=".$row["id"]."'>Pass</a></td>";
    echo "</tr>";
  }
}
?>
</table>
</div>

<div class="contentBlock">
<h2>Pending Products</h2>
<p>Check the product's image and file (if any).</p>
<table>
<?php
    $query = "SELECT id, name, partner, image, type, link FROM prodSub";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["name"]."</td>";
    echo "<td>".$row["partner"]."</td>";
    echo '<td><a href="downStuff.php?img=prod&imgLink='.$row["image"].'">'.$row["image"]."</a></td>";
    if ($row["type"]!="d"){echo '<td><a href="downStuff.php?img=prod&imgLink='.$row["link"].'">'.$row["link"]."</a></td>";}
    else {echo '<td class="longRow"><a href="'.$row["link"].'" target="_blank">'.$row["link"]."</a></td>";}
    echo "<td><a class='popupButton' href='pass.php?what=prod&id=".$row["id"]."'>Pass</a></td>";
    echo "</tr>";
  }
}
?>
</table>
</div>

<div class="contentBlock">
<h2>Pending File Updates</h2>
<p>Check the new thumbnail or file.</p>
<table>
<?php
    $query = "SELECT what, type, ud, file, partner FROM newSub";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["what"]."</td>";
    echo "<td>".$row["partner"]."</td>";
    echo '<td><a href="downStuff.php?file='.$row["what"].'&imgLink='.$row["file"].'">'.$row["file"]."</a></td>";
    echo "<td><a class='popupButton' href='pass.php?what=".$row["what"]."&id=".$row["ud"]."'>Pass</a></td>";
    echo "</tr>";
  }
}
?>
</table>
</div>

<?php

if ($dsAdmin){
echo '
<div class="contentBlock">
<h2>Digital Store Admin</h2>
<p>Get an overview (no edit power).</p>
<a class="popupButton" href="/ds/p/killAdmin.php?dir=1"  style="width:22%;">Get In</a>
</div>
';
}
?>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');

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
