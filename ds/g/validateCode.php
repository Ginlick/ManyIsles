<?php

if (isset($_GET["code"])) {if (preg_match("/[^-A-Za-z0-9]/", $_GET["code"])==1){header("Location: ../basket.php");exit();} else {$code = $_GET["code"];} } else  {header("Location: ../checkout2.php?why=invalidCode");exit();}

if(!isset($_COOKIE["loggedIn"])) {header("Location: checkout.html");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: checkout.html");setcookie("loggedIn", "", time() -3600, "/");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$query = "SELECT status, maxUses, uses FROM dscodes WHERE code = '$code'";
if ($firstrow = $conn->query($query)) {
    if (mysqli_num_rows($firstrow) == 0) {header("Location: ../checkout2.php?why=invalidCode");exit();}
    while ($row = $firstrow->fetch_assoc()) {
        $status = $row["status"];
        $uses = $row["uses"];
        $maxUses = $row["maxUses"];
    }
}
$conn->close();

if ($status == 1 AND $maxUses - $uses > 0){
    if (isset($_COOKIE["ds_codes"])){
        if (preg_match("/[^-A-Za-z0-9,]/", $_COOKIE["ds_codes"])==1){
            setcookie("ds_codes", "", time() -3600, "/");
        }
        else {
            $dCodeArray = explode(",", $_COOKIE["ds_codes"]);
            if (count($dCodeArray)>5){
                setcookie("ds_codes", "", time() -3600, "/");
            }
        }

        if (!str_contains($_COOKIE["ds_codes"], $code)){
            $codesCookie = $_COOKIE["ds_codes"].",".$code;
        }
        else {
            $codesCookie = $_COOKIE["ds_codes"];
        }
    }
    else {$codesCookie = $code;}
    setcookie("ds_codes", $codesCookie, time()+604800, "/");
    header("Location: ../checkout2.php?why=codeValidated");
    exit();
}
else {
    header("Location: ../checkout2.php?why=invalidCode");exit();
}

?>

