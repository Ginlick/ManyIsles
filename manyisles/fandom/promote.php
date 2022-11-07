<?php
if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_GET['who'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_GET['d'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-1]/", $_GET['dir'])==1){header("Location:/fandom/home");exit();}

$id = $_GET['id'];
$parentWiki = $id;
require($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$page = new gen("act", 0, $id);
if ($page->power < 3){header("Location:/fandom/home");exit();}

$dir = $_GET['dir'];
$d = $_GET['d'];
$who = $_GET['who'];
$conn = $page->conn;

$redirect = "/fandom/wsettings.php?w=".$id;


$mods = "";
$auths = "";
$banned = "";
$query = "SELECT auths, mods, banned FROM wiki_settings WHERE id = $id";
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
    $myquery = "UPDATE wiki_settings SET mods = '$mods' WHERE id = $id";
    $title = "a moderator";
}
else if ($d == 2) {
    if ($dir == 1){if (!in_array($who, $auths)) {$auths[] = $who;}}
    else {$auths = array_diff($auths, $array = [$who]);}
    $auths = implode(",", $auths);
    $myquery = "UPDATE wiki_settings SET auths = '$auths' WHERE id = $id";
    $title = "a curated author";
}
else if ($d == 0) {
    if ($dir == 1){if (!in_array($who, $banned)) {$banned[] = $who;}}
    else {$banned = array_diff($banned, $array = [$who]);}
    $banned = implode(",", $banned);
    $myquery = "UPDATE wiki_settings SET banned = '$banned' WHERE id = $id";
    $title = "banned";
}

if (include($_SERVER['DOCUMENT_ROOT']."/Server-Side/email.php")) {
    $email = "";
    $query = "SELECT email FROM accountsTable WHERE id = $id";
    if ($result = $conn->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $email = $row["email"];
        }
    }
    $mailer = new mailer;
    $mailer->send($email, "You were Promoted", "A wiki made you $title. Congratulations! <br> <a href='https://manyisles.ch/fandom/wiki/$parentWiki/home'>view wiki</a>", "poet", "You're now $title");
}

if ($conn->query($myquery)){
    header("Location:$redirect&i=userup");exit();
}
else {
    header("Location:$redirect&i=failed");exit();
}

?>
