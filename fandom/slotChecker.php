<?php
//needs: $uid (user id), $conn

if (!isset($power)){
    $power = 1;
    if (isset($admin)){$power = 3;}
    if (isset($auth)){$power = 2;}
    if (isset($super)){$power = 5;}
}

if (!isset($override)) {$override = false;}
if (!isset($id)) {$id = 0;}

$setto = "";
if (($power < 2) OR ($override)){
    $query = "SELECT * FROM slots WHERE id = ".$uid;
    $result = $conn->query($query);
    if ($result->num_rows == 0){
        $query = "INSERT INTO slots (id, a) VALUES ('$uid', '$id')";
        $conn->query($query);
    }
    else {
        while ($row = $result->fetch_assoc()){
            if ($row["a"]==null){$setto="a";break;}
            if ($row["b"]==null){$setto="b";break;}
            if ($row["c"]==null){$setto="c";break;}
            if ($row["d"]==null){$setto="d";break;}
            if ($row["e"]==null){$setto="e";break;}
            if ($row["f"]==null){$setto="f";break;}
            if ($row["g"]==null){$setto="g";break;}
            if ($row["h"]==null){$setto="h";break;}
            if ($row["i"]==null){$setto="i";break;}
            if ($row["j"]==null){$setto="j";break;}
        }
        if ($setto ==""){header("Location: /fandom/wiki/".$id."/?i=noslot");exit();}
        $query = sprintf('UPDATE slots SET %s=%s WHERE id = %s', $setto, $id, $uid);
        $conn->query($query);

        if ($setto == "f") {
            mail("pantheon@manyilses.ch", "Slots Getting Fuller", "Is at six slots: $uid");
        }
    }
}

$query = 'UPDATE poets SET edits= edits + 1 WHERE id = '.$uid;
if ($result = $conn->query($query)) {
}
else {
    $query = sprintf('INSERT INTO poets (id, uname, edits) VALUES (%s, "%s", 1)', $uid, $uname);
    $conn->query($query);
}




?>
