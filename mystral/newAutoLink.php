<?php
$name = ""; $href = ""; $wiki = 0;
if (isset($_GET["name"])) {
    if (preg_match("/['\"]/", $_GET['name'])==1){exit();}
    $name = strtolower($_GET["name"]);
}
if (isset($_GET["reference"])) {
    if (preg_match("/['\"]/", $_GET['reference'])==1){exit();}
    $href = $_GET["reference"];
}
if (isset($_GET["wiki"])) {
  $wiki = substr(preg_replace("/[^0-9]/","",  $_GET['wiki']), 0, 22);
}

if (!preg_match("/[^ ]+ [^ ]+/", $name) AND !preg_match("/^[^ ]+$/", $name)){exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "mystral");
if (!$gen->canedit){header("Location:$gen->homelink");exit();}

$oldArr = [];
$query = "SELECT data FROM auto_links WHERE id = $gen->user";
if ($result = $gen->dbconn->query($query)){
    if (mysqli_num_rows($result) == 0) {
        $query = "INSERT INTO auto_links (id) VALUES ($gen->user)";
        $gen->dbconn->query($query);
    }
    else {
        while ($row = $result->fetch_assoc()){
            $oldArr = json_decode($row["data"], true);
        }
    }
}

if (in_array($name, $oldArr[$wiki])){exit();}
$insArr= ["name" => $name, "href" => $href];
$oldArr[$wiki][] = $insArr;
$newArr = json_encode($oldArr);

$query = "UPDATE auto_links SET data = '$newArr' WHERE id = $gen->user";
if ($gen->dbconn->query($query)){
    echo "success";
}
else {
    echo "error";
}

?>
