<?php
// if $checkArtId: requires $artId
//does whole ds partnership check, supplies $conn

if(!isset($_COOKIE["loggedIn"])){header("Location:/account/Account.html?error=notSignedIn");exit();}
if(!isset($_COOKIE["loggedP"])){header("Location: /account/Account.html?error=notSignedIn");exit();}
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$id = $_COOKIE["loggedIn"];
if (preg_match("/^[0-9]+$/", $id)!=1) {setcookie("loggedIn", "", time() -3600, "/");header("Location: /account/Account.html?error=notSignedIn");exit();}

if (!isset($checkArtId)){
    $checkArtId = false;
}
if (!isset($checkDSpresence)){$checkDSpresence = false;}
if (!isset($admin)){$admin = false;}

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $checkpsw = $row["password"];
    }
}

require($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw.php");

$query = 'SELECT * FROM partners WHERE account = "'.$uname.'"';
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        $pId = $row["id"];
        $status = $row["status"];
        $artSeller = $row["name"];
        $pType = $row["type"];
    }
}
if (!isset($pId)){header("Location: /account/BePartner.php");exit();}
if ($status != "active"){header("Location: /account/Publish.php");exit();}

$query = "SELECT power FROM partners_ds WHERE id = $pId";
if ($result = $conn->query($query)){
    if (mysqli_num_rows($result) == 0) { header("Location: /ds/home.php");exit(); }
    while ($row = $result->fetch_assoc()) {
        $pPower = $row["power"];
    }
}

if ($admin) {
    if ($pPower < 2) {
        header("Location: killAdmin.php");exit(); 
    }
}
else {
    if ($checkArtId AND $artId != 0) {
        $query = "SELECT sellerId FROM dsprods WHERE id = $artId";
        if ($result = $conn->query($query)){
            if (mysqli_num_rows($result) == 0) { header("Location: ".$redirect);exit(); }
            else {
                while ($row = $result->fetch_assoc()) {
                    if ($row["sellerId"] != $pId) { header("Location: ".$redirect);exit(); }
                }
            }
        }
    }

    if ($checkDSpresence) {
        $query = "SELECT power, acceptCodes FROM partners_ds WHERE id = $pId";
        if ($result = $conn->query($query)){
            if (mysqli_num_rows($result) == 0) { header("Location: /ds/home.php");exit(); }
            while ($row = $result->fetch_assoc()) {
                $pPower = $row["power"];
                $pAcceptCodes = $row["acceptCodes"];
            }
        }
    }

}



if (!function_exists ("inputChecker")) {
    function inputChecker($input, $preg, $how) {
        global $redirect;
        if (isset($input)) {
            if ((preg_match($preg, $input)==1) == $how){
                header("Location: ".$redirect);echo $redirect;exit();
            } else {
                return($input);
            }
        } else {
            header($redirect);exit();
        }
    }
}

?>


