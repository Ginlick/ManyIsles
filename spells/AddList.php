<?php

//if (preg_match("/[^A-Za-z0-9'-, ]/", $_GET['body'])==1){header("Location: SpellIndex.html");exit();}
//if (isset($_GET["sl"])){if (preg_match("/[^a-e]{1}/", $_GET['sl'])==1){header("Location: SpellIndex.html");exit();}}


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
if(!isset($_COOKIE["spellLists"])){header("Location: SpellList.html");exit();}


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

$setto="";
$query = "SELECT * FROM spelllists WHERE id = ".$id;

 if ($result = $conn->query($query)) {
    while ($row = $result->fetch_row()) {
     if ($row[0]== null){header("Location: SpellList.html");
            setcookie("spellLists", "", time() -3600, "/");
            setcookie("spellb", "", time() -3600, "/");
            setcookie("spellc", "", time() -3600, "/");
            setcookie("spelld", "", time() -3600, "/");
            setcookie("spelle", "", time() -3600, "/");
        exit();}
        if (isset($_GET["sl"])){$setto=$_GET["sl"];break;}
        if ($row[1]==null){$setto="a";break;}
        if ($row[2]==null){$setto="b";break;}
        if ($row[3]==null){$setto="c";break;}
        if ($row[4]==null){$setto="d";break;}
        if ($row[5]==null){$setto="e";break;}
}
}

if ($setto==""){header("Location: SetSLCook.php?where=noSpace");exit();}

$query = sprintf('UPDATE spelllists SET %s = "%s" WHERE id = %s', $setto, $_GET['body'], $id);
if ($conn->query($query)) {
    $goto = $setto;
    if ($setto == "a"){$goto = "Lists";}
    echo '<script>document.cookie = "spell'.$goto.'='.$_GET['body'].';path=/";window.location.href = "SavedList.html?sl='.$setto.'";</script>';
}

else {echo $query;}



?>
