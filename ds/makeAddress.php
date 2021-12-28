<?php
if (preg_match('/["]/', $_POST['fullname'])==1){$redirect = "true";}
else if (preg_match('/["]/', $_POST['address'])==1){$redirect = "true";}
else if (preg_match('/["]/', $_POST['city'])==1){$redirect = "true";}
else if (preg_match('/["]/', $_POST['zip'])==1){$redirect = "true";}
else if (preg_match('/["]/', $_POST['state'])==1){$redirect = "true";}
else {$redirect = "false";}

if ($redirect != "false"){
    header("Location:checkout1.php?error=illegalInput");
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
include("g/countries.php");
$countries_glob = $countries["GLO"];

if(!isset($_COOKIE["loggedIn"])){header("Location: checkout.html");exit();}
if (!isset($countries_glob[$_POST['state']])){header("Location: checkout1.php");exit();}

$id = $_COOKIE["loggedIn"];


$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $checkpsw = $row["password"];
    }
}

$redirect = "checkout.html";
include("../Server-Side/checkPsw.php");

if ( session_status() !== PHP_SESSION_ACTIVE ) {session_start();}
if (!isset($_SESSION["basket"])) {$_SESSION["basket"]="";}
$inbasket = explode(",", $_SESSION["basket"]);

$pureDigit = true;
if ($_SESSION["basket"] != ""){
    foreach ($inbasket as $x => $value) {
        if (stripos($value, ":")) {
            $shortitem = substr($value, 0, strpos($value, ":"));       
        }
        else {$shortitem = $value;}

        $query = "SELECT * FROM dsprods WHERE id = ".$shortitem;
        if ($result = $conn->query($query)) {
            if ($shortitem != 1){$pureDigit = false;}
        }
    }
}

if ($pureDigit == false) {
    $query ="DELETE FROM address WHERE id = ".$id;
}
else if ($_POST['fullname'] == "" AND $_POST['address'] == "" AND $_POST['city'] == "" AND $_POST['zip'] == "" AND $_POST['state'] == "") {
    $query = "SELECT id FROM accountsTable WHERE id = 1";
}
else {
    $query ="DELETE FROM address WHERE id = ".$id;
}

if ($conn->query($query) ){
    $query = sprintf('INSERT INTO address (id, fullname, address, city, Zip, Country) VALUES (%s, "%s", "%s", "%s", "%s", "%s")',
        $id,
        $_POST['fullname'],
        $_POST['address'],
        $_POST['city'],
        $_POST['zip'],
        $_POST['state']
    );
    echo $query;
    if ($conn->query($query)) {
        echo "<br>Yay";
        header("Location:checkout2.php");exit();
    }
}

if ($pureDigit == true){
    header("Location:checkout2.php");
}
else {
    header("Location:checkout1.php");exit();
}

?>

