<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer();
if (!$user->check(true)){header("Location:Account?error=notSignedIn");}
$id = $user->user;
$conn = $user->conn;

$query="SELECT * FROM poets WHERE id = ".$id;
$result =  $conn->query($query);
while ($row = $result->fetch_assoc()) {
    if ($row["admin"] != 1) {
        header("Location: /account/home");exit();
    }
}
?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Fandom Admin</title>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/account/g/GGMdl.css">
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
a, .fakelink {
    color: #5c4f95;
}

    a:hover, .fakelink:hover {
        color: #497d8a;
    }
.mednav {
    background-color: #f5d000;
}
.wikiButton {
    display: inline-block;
    color: black;
    background-color: #9edae3;
    transition: .3s ease;
    cursor: pointer;
    font-size: calc(12px + 0.5vw);
    border-radius: 4px;
    font-weight: bold;
    padding:10px;
    margin:auto;
}

.wikiButton:hover {
    color: #2a2a2a;
    background-color: #8ec5cd;
}
.contentBlock table {
    text-align:center;
}
</style>
</head>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>

<body>
    <div style="flex: 1 0 auto;">

    <div w3-include-html="/Code/CSS/GTopnav.html"></div>

    <div class="contentBlock" style="margin-top:5vw;">

        <div class="banner" style="position:static">
                <img src="/wikimgs/banners/fandom.png" alt="Banner" style='width:100%;display:block'>
        </div>

<div class="mednav">
        <ul>
            <li> <a href="/account/home">&lt Back</a></li>
        </ul>
    </div>

<a href="/wiki/f/f.php" target="_blank"><h1>Fandom Administration</h1></a>
<div><img src='/Imgs/Ranks/Poet.png' alt:'Oopsie!' style='width:96%;display:block;margin:auto'></div>
<p>As a member of the Homeland Institute of Trade's administration, you are entrusted with power. Use it responsibly.
</p>
</div>

<div class="contentBlock">
<h2>Account Slots</h2>
<table>
<!-- <thead>
<tr>
<td>Account</td>
<td>1</td>
<td>2</td>
<td>3</td>
<td>4</td>
<td>5</td>
<td>6</td>
<td>7</td>
<td>8</td>
<td>9</td>
<td>10</td>
<td></td>
</tr>
</thead> -->
<tbody>
<?php

    $query = "SELECT * FROM slots";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
    function addLink($x){
       echo "<td><a href='f.php?id=".$x."' target='_blank'>Check</a></td>";
    }
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["id"]."</td>";
    if (isset($row["a"])){addLink($row["a"]);}
    if (isset($row["b"])){addLink($row["b"]);}
    if (isset($row["c"])){addLink($row["c"]);}
    if (isset($row["d"])){addLink($row["d"]);}
    if (isset($row["e"])){addLink($row["e"]);}
    if (isset($row["f"])){addLink($row["f"]);}
    if (isset($row["g"])){addLink($row["g"]);}
    if (isset($row["h"])){addLink($row["h"]);}
    if (isset($row["i"])){addLink($row["i"]);}
    if (isset($row["j"])){addLink($row["j"]);}
    echo "<td><a class='wikiButton' href='clear.php?id=".$row["id"]."&cach=".$row["b"]."'>Clear</a></td>";
    echo "</tr>";
  }
}

?>
</tbody>
</table>
</div>

<div class="contentBlock">
<h2>Suspended Pages</h2>
<table>
<?php
    $query = "SELECT * FROM pages WHERE status = 'suspended'";
    $result = $conn->query($query);
$showed_pages = array();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    if (in_array($row["id"], $showed_pages)) {continue;}
    else {array_push($showed_pages, $row["id"]);}
    echo "<tr>";
    echo "<td>".$row["id"]."</td>";
    echo "<td><a href='f.php?clear=true&id=".$row["id"]."' target='_blank'>View</a></td>";
    echo "<td><a class='wikiButton' href='suspend.php?w=1&id=".$row["id"]."'>Restore</a></td>";
    echo "</tr>";
  }
}

?>
</table>
</div>

<div class="contentBlock">
<h2>Reverted Pages</h2>
<table>
<tbody>
<?php
for ($x = 1; $x<100; $x++){
    $query = "SELECT * FROM pages WHERE id = ".$x. " AND v =  ( SELECT MIN(v) FROM pages WHERE id = ".$x." AND status = 'reverted')";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["id"]."</td>";
        echo "<td><a class='wikiButton' href='f.php?id=".$row["id"]."' target='_blank'>View</a></td>";
        echo "<td><a class='wikiButton' href='age.php?id=".$row["id"]."'>Slaughter Younglings</a></td>";
        echo "</tr>";
      }
    }
    else {continue;}
}
?>
</tbody>
</table>
</div>

<div class="contentBlock">
<h2>Reported Pages</h2>
<table>
<thead>
<tr>
<td>Reporter</td>
<td>Page</td>
<td></td>
</tr>
</thead>
<tbody>
<?php
    $query = "SELECT * FROM reported";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["uid"]."</td>";
    echo "<td><a href='f.php?id=".$row["id"]."' target='_blank'>Check</a></td>";
    echo "<td><a class='wikiButton' href='report.php?w=undo&id=".$row["id"]."'>Clear</a></td>";
    echo "</tr>";
  }
}

?>
</tbody>
</table>
</div>

<div class="contentBlock">
<h2>Users</h2>
<table>
<thead>
<tr>
<td>id</td>
<td>uname</td>
<td>admin</td>
<td>edits</td>
<td></td>
</tr>
</thead>
<tbody>
<?php
    $query = "SELECT * FROM poets";
    $result = $conn->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row["id"]."</td>";
    echo "<td>".$row["uname"]."</td>";
    echo "<td>".$row["admin"]."</td>";
    echo "<td>".$row["edits"]."</td>";
    if ($row["banned"] == 0){$text = "Ban";}else{$text = "Clear";}
    echo "<td><a class='wikiButton' href='ban.php?id=".$row["id"]."'>".$text."</a></td>";
    echo "</tr>";
  }
}

?>
</tbody>
</table>
</div>


<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "1"){
    alert("Success!");
}
else if (why == "0") {
    alert("Failure!");
}


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
