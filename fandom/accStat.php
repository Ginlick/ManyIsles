
<?php
//$conn

function getAccStat($conn, $id, $wiki = null, $explicit = false) {
    $astat = 1;
    $query = "SELECT banned, admin, super FROM poets WHERE id = $id";
    if ($max = $conn->query($query)) {
        while ($row = $max->fetch_assoc()){
            if ($row["banned"]!=0){$astat = 0;}
            else if ($row["super"]!=0){$astat = 5;}
            else if ($row["admin"]!=0){$astat = 4;}
        }
    }
    if ($wiki != null AND $astat > 0 AND $astat < 5){
        $query = "SELECT banned, auths, mods FROM wiki_settings WHERE id = $wiki";
        if ($max = $conn->query($query)) {
            while ($row = $max->fetch_assoc()){
                $banned = explode(",", $row["banned"]);
                $mods = explode(",", $row["mods"]);
                $auths = explode(",", $row["auths"]);
                if (in_array($id, $banned)){$astat = 0;}
                else if (in_array($id, $mods)){$astat = 3;}
                else if (in_array($id, $auths)){$astat = 2;}
            }
        }
    }
    if ($explicit){
        if ($astat == 0){return "banned";}
        else if ($astat == 1){return "poet";}
        else if ($astat == 2){return "curated";}
        else if ($astat == 3){return "moderator";}
        else if ($astat == 4){return "admin";}
        else if ($astat == 5){return "super";}
    }
    else {
        return $astat;
    }
}

?>