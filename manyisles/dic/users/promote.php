<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");

if (preg_match("/[^0-9]/", $_GET['dicd'])==1){header("Location:/dic/home");exit();}
if (preg_match("/[^0-9]/", $_GET['who'])==1){header("Location:/dic/home");exit();}
if (preg_match("/[^0-9]/", $_GET['d'])==1){header("Location:/dic/home");exit();}
if (preg_match("/[^0-1]/", $_GET['dir'])==1){header("Location:/dic/home");exit();}

$dic = new dicEngine();
$dic->checkCredentials(true);
$redirect = "users/langhub?dicd=".$dic->language;
$conn = $dic->dicconn;
$lang = $dic->language;

$dir = $_GET['dir'];
$d = $_GET['d'];
$who = $_GET['who'];
if ($dic->wiki->power < $d){$dic->go($redirect);}

$mods = [];
$auths = [];
$banned = [];
$query = "SELECT auths, mods, banned FROM languages WHERE id = $lang";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $mods = explode(",", $row["mods"]);
        $auths = explode(",", $row["auths"]);
        $banned = explode(",", $row["banned"]);
    }
}


$title = "poet";
if ($d == 3){
    if ($dir == 1){if (!in_array($who, $mods)) {$mods[] = $who;}}
    else { $mods = array_diff($mods, $array = [$who]);}
    $mods = implode(",", $mods);
    $myquery = "UPDATE languages SET mods = '$mods' WHERE id = $lang";
    $title = "a moderator";
}
else if ($d == 2) {
    if ($dir == 1){if (!in_array($who, $auths)) {$auths[] = $who;}}
    else {$auths = array_diff($auths, $array = [$who]);}
    $auths = implode(",", $auths);
    $myquery = "UPDATE languages SET auths = '$auths' WHERE id = $lang";
    $title = "a curated author";
}
else if ($d == 0) {
    if ($dir == 1){if (!in_array($who, $banned)) {$banned[] = $who;}}
    else {$banned = array_diff($banned, $array = [$who]);}
    $banned = implode(",", $banned);
    $myquery = "UPDATE languages SET banned = '$banned' WHERE id = $lang";
    $title = "banned";
}

if ($conn->query($myquery)){
    $dic->go($redirect."&i=userup");
}
else {
  $dic->go($redirect."&i=failed");
}

?>
