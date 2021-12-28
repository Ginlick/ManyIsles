<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/docs/1/");exit();} else {$id = $_GET['id'];}
if (isset( $_GET['db'])) { if (preg_match("/[^0-9a-zA-Z]/", $_GET['db'])==1){header("Location:/docs/1/");exit();} else {$database = $_GET['db'];} } else {$database = "docs";}
if ($database == "rules"){
    $domain = "5eS";
}
else {
    $domain = "docs";
    $database = "docs";
}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
$super = false;
require_once($_SERVER['DOCUMENT_ROOT']."/docs/security.php");
if (!$super){
    header("Location:/$domain/1/home"); exit();
}

$query = "DELETE FROM $database WHERE id = ".$_GET['id']." AND status = 'reverted'";
if  ($conn->query($query)){
    header("Location:/$domain/".$_GET['id']."/article?show=aged");exit();
}
else {
    header("Location:/$domain/".$_GET['id']."/article?show=nreverted");exit();
}
?>