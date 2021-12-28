<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
class gay {} $gen = new gay;
equipDom($gen, "mystral");

$query = "SELECT a.id
FROM $gen->database a
LEFT OUTER JOIN $gen->database b
    ON a.id = b.id AND a.v < b.v
WHERE b.id IS NULL AND a.root = 0";

if ($result = $gen->dbconn->query($query)){
    if (mysqli_num_rows($result) >= $gen->mystData["notebooks"]) {
        echo "<script>window.location.replace('$gen->homelink?view=sub');</script>";exit();
    }
    else {
        $max =$gen->dbconn->query("SELECT MAX( id ) AS max FROM $gen->database");
        list($max) = mysqli_fetch_row($max); $max += 1;
        $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, parseClear, cate) VALUES ('.$max.', 0, "New Notebook", "New Notebook",  "default", 0, "This is your notebook\'s homepage. [Edit](/mystral/edit?id='.$max.')", 1, "Homepage")';
        if ($gen->dbconn->query($query)){
            header("Location:".$gen->artRootLink.$max."/home?i=created");exit();
        }
    }
}
else {
    $query = "CREATE TABLE $gen->database LIKE notes";
    if ($gen->dbconn->query($query)){
        $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, parseClear, cate) VALUES (1, 0, "New Notebook", "New Notebook",  "default", 0, "This is your notebook\'s homepage. [Edit](/mystral/edit?id=1)", 1, "Homepage")';
        if ($gen->dbconn->query($query)){
            header("Location:".$gen->artRootLink."1/home?i=created");exit();
        }
    }
}



?>


