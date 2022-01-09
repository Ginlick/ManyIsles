<?php

if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:hub");exit();}
$id = $_GET['id'];
if (preg_match("/[^0-9]/", $_GET['dir'])==1){header("Location:hub");exit();}
$dir = $_GET['dir'];

require_once(dirname($_SERVER['DOCUMENT_ROOT'])."/media/keys/ds-actcode.php");$mycode = $ds_actcode;
require_once($_SERVER['DOCUMENT_ROOT'].'/ds/subs/subHandler.php');
$plan = new subHandler($mycode, "credit");
if ($plan->checkUser($id)) {
    $plan->statSub($id, $dir);
    header("Location:sub?id=$id&why=updated");exit();
}

echo "error 505.";

?>
