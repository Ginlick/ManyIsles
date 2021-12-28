<?php
$name = ""; $href = "";
if (isset($_GET["id"])) {
    if (preg_match("/['\"]/", $_GET['id'])==1){exit();}
    $name = $_GET["id"];
}
else {exit();}


require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, 0, false, "mystral");
if (!$gen->canedit){header("Location:$gen->homelink");exit();}

$oldArr = [];
$query = "SELECT data FROM auto_links WHERE id = $gen->user";
if ($result = $gen->dbconn->query($query)){
    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()){
            $oldArr = json_decode($row["data"], true);
        }
        unset($oldArr[$name]);
        $newArr = json_encode($oldArr);
        $query = "UPDATE auto_links SET data = '$newArr' WHERE id = $gen->user";
        echo $query;
        if ($gen->dbconn->query($query)){
            echo "<script>window.location.replace('hub?i=killd&cash=".rand()."');</script>";
        }
        else {
            echo "error";
        }
    }
}
?>


