<?php
// $conn, $parentWiki, $id

if (isset($domain) AND $domain == 1){
    $super = false;
    require_once($_SERVER['DOCUMENT_ROOT']."/docs/security.php");
    if (!$super){
        header("Location:/docs/1/home"); exit();
    }
    require($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
    include($_SERVER['DOCUMENT_ROOT']."/fandom/getWiki.php");
    $parentWiki = getWiki($id, "docs");
}
else {
    $doSecurity = true;
    require($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
    include($_SERVER['DOCUMENT_ROOT']."/fandom/getWiki.php");
     echo getWiki($id);
   $parentWiki = getWiki($id);
    $banned = false;
    $admin = 0;
    include($_SERVER['DOCUMENT_ROOT']."/fandom/power.php");
}


?>