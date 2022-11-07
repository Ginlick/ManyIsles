
<?php
// $conn, $uid, $parentWiki

$admin = 0;
$super = 0;
$auth = 0;
$banned = false;

require_once($_SERVER['DOCUMENT_ROOT']."/fandom/accStat.php");
$astat = getAccStat($conn, $uid, $parentWiki);

if ($astat == 0){$banned = true;}
else {
    if ($astat > 1){
        $auth = 1;
    }
    if ($astat > 2){
        $admin = 1;
    }
    if ($astat > 4){
        $super = 1;
    }
}



?>