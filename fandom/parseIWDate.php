<?php
//

function getDateArray($conn, $wiki, $domain = "fandom") {
    $query="SELECT dateName FROM wiki_settings WHERE id = $wiki ";
    if ($domain == "mystral"){
        $query="SELECT dateName FROM wiki_settings WHERE id = '$wiki'";
    }
    if ($result =  $conn->query($query)) {
        if ($result->num_rows != 0){
            while ($row = $result->fetch_assoc()){
                return json_decode($row["dateName"], true);
            }
        }
    }
}


function parseIWDate($otimeStart, $otimeEnd, $dates = [], $complete = false, $indiv = 0) {
    if (str_contains($otimeStart, ",")) {
        $dArr = explode(",", $otimeStart);
        $startDays = $dArr[0];
        $timeStart = $dArr[1];
    } else {$timeStart = $otimeStart; $startDays = "";}
    if (str_contains($otimeEnd, ",")) {
        $dArr = explode(",", $otimeEnd);
        $endDays = $dArr[0];
        $timeEnd = $dArr[1];
    } else {$timeEnd = $otimeEnd; $endDays = "";}
    $sidetab = "";
    if ($indiv == 0){
        if ($timeStart != ""){
            if ($complete){$sidetab .= $startDays;}
            $sidetab .= yearMod($timeStart, $dates);
        }
        if ($complete){$condition = returnSame($otimeStart, $otimeEnd);}
        else {$condition = returnSame($timeStart, $timeEnd);}
        if (!$condition){
            if ($timeStart != "" OR $timeEnd!= ""){$sidetab.=" - ";}
            if ($timeEnd != "" AND ($timeStart == "" OR intval($timeEnd) >= intval($timeStart))){
                if ($complete){$sidetab .= $endDays;}
                $sidetab .= yearMod($timeEnd, $dates);
            }
        }
    }
    else {
        if ($complete){
            if ($indiv == 1){$sidetab .= $startDays;}
            else if ($indiv == 2){$sidetab .= $endDays;}
        }
        if ($indiv == 1){$sidetab .= yearMod($timeStart, $dates);}
        else if ($indiv == 2){$sidetab .= yearMod($timeEnd, $dates);}
    }
    return $sidetab;
}
function yearMod($year, $dates) {
    if ($dates != []){
        $year = intval($year);
        $year = number_format($year, 0, ".", "'");
        if (str_contains($year, "-")) {
            if (isset($dates["B"])) {
                return str_replace("-", "", $year)." ".$dates["B"];
            }
        }
        else {
            if (isset($dates["A"])) {
                return $year." ".$dates["A"];
            }
        }
    }
    return $year;
}
function giveYear($toparse) {
    if (str_contains($toparse, ",")) {
        $dArr = explode(",", $toparse);
        $year = $dArr[1];
    } else {$year = $toparse;}
    if ($year == ""){$year = null;}
    return $year;
}

function returnSame($a, $b){
    if ($a == $b) { return true; }
    else { return false; }
}

function getArtImage($sideImg, $banner, $NSFW = 0, $info = null) {
    if ($NSFW > 1 AND !isset($_COOKIE["clearNSFW"])){
        $thumbnail = "/wikimgs/icons/warning.png";
    }
    else {
        if ($sideImg!=""){
            $thumbnail = $sideImg;
        }
        else {
            $thumbnail = banner($banner, $info);
        }
    }
    return $thumbnail;
}

function banner($banner, $info = null){
    if ($banner == "default" OR $banner == "/wikimgs/banners/default" OR $banner == "current"){
        $banner = "fandom.png";
        if ($info != null) {
            $banner = $info->defaultBanner;
        }
    }

    if (!str_contains($banner, "/")){
        return "/wikimgs/banners/".$banner;
    }
    else {
        return $banner;
    }
}
?>
