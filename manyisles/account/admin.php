<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer($conn, $_COOKIE["loggedIn"]);
$id = $user->user;
if (!$user->check(false)){
  header("Location: /account/home");exit();
}
if ($id != 11 AND $id != 14) {
    header("Location: /account/home");exit();
}

$pId = 0;
$query = "SELECT id FROM partners WHERE user = $user->user";
if ($result = $conn->query($query)){
    while ($row = $result->fetch_assoc()) {
        $pId = $row["id"];
    }
}
$dsAdmin = false;
$query = "SELECT power FROM partners_ds WHERE id = $pId";
if ($result = $conn->query($query)){
    while ($row = $result->fetch_assoc()) {
        if ($row["power"]>1){$dsAdmin = true;}
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
<div><img src='../Imgs/Ranks/HighMerchant.png' alt:'Oopsie!' class="bannerI"></div>
<p>As a member of the Homeland Institute of Trade's administration, you are entrusted with power. Use it responsibly.
</p>
</div>

<div class="contentBlock">
<h2>Active Partnerships</h2>
<table>
<?php

    $query = "SELECT * FROM partners WHERE status = 'active' OR status = 'suspended'";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["name"]."</td>";
    echo "<td>p#".$row["id"]."</td>";
    echo "<td>u#".$row["user"]."</td>";
    echo "<td><a href='/dl/partner?id=".$row["id"]."'>View</a></td>";
        $query2 = 'SELECT email FROM accountsTable WHERE id = "'.$row["user"].'"';
        $result2 = $conn->query($query2);
        while($row2 = $result2->fetch_assoc()){
            $email = $row2["email"];
            break;
        }
    echo '<td><a href="mailto:'.$email.'" target="_blank">Contact</a></td>';
    if ($row["status"]=="active"){$susp = "Suspend";}else{$susp = "Reactivate";}
    echo "<td><a class='button' href='suspend.php?id=".$row["id"]."'>".$susp."</a></td>";
    echo "</tr>";
  }
}

?>
</table>
</div>
<div class="contentBlock">
<h2>Premium Activation Requests</h2>
<table>
<?php

    $query = "SELECT * FROM requests WHERE domain = 'pub' AND request = 'prem'";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><a href='/dl/partner?id=".$row["requestee"]."' target='_blank'>p#".$row["requestee"]."</a></td>";
    echo "<td><a href='prem/activate?id=".$row["requestee"]."' target='_self'>activate</a></td>";
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
<a href="/ds/p/killAdmin.php?dir=1"  style="width:22%;">Get In</a>
</div>
';
}
?>
<script src = "/Code/CSS/global.js">
</script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "activtd") {
  createPopup("d:pub;txt:Partnership extension activated");
}
</script>
