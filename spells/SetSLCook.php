<?php
if (isset($_GET["where"])){if (preg_match("/[^A-Za-z.]{2,}/", $_GET['where'])==1){header("Location: SpellIndex.html");exit();}}


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


if(!isset($_COOKIE["loggedIn"])){header("Location: /account/Account.html?error=signingIn");exit();}
if(!isset($_COOKIE["loggedP"])){header("Location: /account/Account.html?error=signingIn");exit();}


$id = $_COOKIE["loggedIn"];

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $curpsw = $row["password"];
    }
    }

 $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: /account/Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$slot="";
$a = "false";
$b = "false";
$c = "false";
$d = "false";
$e = "false";
$query = "SELECT * FROM spelllists WHERE id = ".$id;
 if ($result = $conn->query($query)) {
    while ($row = $result->fetch_row()) {
        if ($row[1]!="") {$slot="ye"; $a = $row[1];}
        if ($row[2]!="") {$slot="ye"; $b = $row[2];}
        if ($row[3]!="") {$slot="ye"; $c = $row[3];}
        if ($row[4]!="") {$slot="ye"; $d = $row[4];}
        if ($row[5]!="") {$slot="ye"; $e = $row[5];}
    }
}

if ($slot==""){
$go = "Location: /account/SignedIn.php?sl=no";
echo $go;
if (isset($_GET["show"])){$go = "Location: /account/SignedIn.php?sl=no&show=".$_GET["show"];}
echo $go;
if (isset($_GET["where"])){header($go);exit();}
else {header("Location: SpellList.html?sl=none");exit();}
}

echo "<script>";

if ($a != "false"){echo 'document.cookie = "spellLists='.$a.';path=/";';}else{echo 'document.cookie = "spellLists=set; path=/";';}
if ($b != "false"){echo 'document.cookie = "spellb='.$b.';path=/";';}else{echo 'document.cookie = "spellb=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";';}
if ($c != "false"){echo 'document.cookie = "spellc='.$c.';path=/";';}else{echo 'document.cookie = "spellc=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";';}
if ($d != "false"){echo 'document.cookie = "spelld='.$d.';path=/";';}else{echo 'document.cookie = "spelld=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";';}
if ($e != "false"){echo 'document.cookie = "spelle='.$e.';path=/";';}else{echo 'document.cookie = "spelle=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";';}



?>
var urlParams = new URLSearchParams(window.location.search);
var sl = urlParams.get('sl');
var show = urlParams.get('show');
var signGo =  "/account/SignedIn.php?sl=ye";
if (show!= null){
    signGo = signGo.concat("&show=".concat(show));
}

var where = urlParams.get('where');
    if (where == "SignedIn") {
            window.location.href = signGo;
            
        }
     else if (where == "noSpace") {
            window.location.href = "SpellList.html?sl=noSpace";
        }
     else if (sl == "noSpace") {
            window.location.href = "SpellList.html?sl=noSpace";
        }
else {window.location.href = "SpellList.html";}

</script>
