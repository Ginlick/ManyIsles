<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

if (isset($_COOKIE["ds_codes"])){
    if (preg_match("/[^-A-Za-z0-9,]/", $_COOKIE["ds_codes"])==1){
        setcookie("ds_codes", "", time() -3600, "/");
    }
    else {
        $dCodeArray = explode(",", $_COOKIE["ds_codes"]);
        foreach ($dCodeArray as $code){
            $query = "UPDATE dscodes SET uses = uses + 1 WHERE code = '$code'";
            $conn->query($query);
        }
        setcookie("ds_codes", "", time() -3600, "/");
    }
}




?>

