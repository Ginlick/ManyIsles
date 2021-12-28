<?php
if (isset($_GET["dir"])) {if (preg_match("/^[a-z]*$/", $_GET["dir"])!=1){header("Location:settings.php");exit();} else $dir =  $_GET["dir"];} else { header("Location:settings.php");exit(); }

$redirect = "settings.php";
$checkDSpresence = true;
require_once("security.php");

if ($dir == "false"){
    $dir = 0;
}
else {
    $dir = 1;
}

//doing 
$query = "UPDATE partners_ds SET acceptCodes = $dir WHERE id = $pId";
if ($result = $conn->query($query)){
    echo "success";
}
else {
    echo $conn->error;
}

?>
