<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/parse.php");

if (isset($_GET["g"])) {
    if (preg_match("/[^0A-Za-z\- ]/", $_GET['g'])==1){exit();}
    $genre = $_GET["g"];
}
else {
    $genre = "";
}

if (isset($_GET["c"])) {
    if (preg_match('/["< ]/', $_GET['c'])==1){exit();}
    $categories = $_GET["c"];
    if ($categories == 0){$categories = null;}
}
else {
    $categories = null;
}

if (isset($_GET["w"])) {
    if (preg_match("/[^0-9]/", $_GET['w'])==1){exit();}
    $wiki = intval($_GET["w"]);
}
else {
    $wiki = 1;
}
if (isset($_GET["m"])) {
    if (preg_match("/[^0-1]/", $_GET['m'])==1){exit();}
    $mode = $_GET["m"];
}
else {
    $mode = 0;
}
$domain = 0;
if (isset($_GET["dom"])){
    if (preg_match("/^[0-9]$/", $_GET["dom"])!=1){exit();} else {$domain = $_GET["dom"];}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, $wiki, false, $domain);


function requirements($pageRoot, $currentGenre, $currentCates) {
    global $wiki, $genre, $categories, $gen;
    if (getWiki($pageRoot, $gen->database, $gen->dbconn) == $wiki){
        if ($categories != null){
            $categoryArray = explode(",", $currentCates);
            if (in_array($categories, $categoryArray)){
                return true;
            }
        }
        else {
            return true;
        }
    }
    return false;
}



function createTimeline($result, $parse) {
    global $gen, $wiki;
    $dateArray = getDateArray($gen->dbconn, $wiki);
    $fullResult = array();
    $counter = 0;
    echo '        <section class="cd-timeline js-cd-timeline" id="resultTimeline">
            <div class="container max-width-lg cd-timeline__container">';
    while ($row = $result->fetch_assoc()){
        if ($counter >= 22){break;}
        if (requirements($row["id"], $row["cate"], $row["categories"])){
            $stYear = null;
            if ($row["timeStart"] != ""){ $stYear = giveYear($row["timeStart"]);}
            else if ($row["timeEnd"] != ""){ $stYear = giveYear($row["timeEnd"]);}
            if ($stYear == null){continue;}
            $sideResult = [];
            $sideResult["year"] = $stYear;
            if ($row["sidetabImg"] != null){$pageImg = $row["sidetabImg"];}else{$pageImg = banner($row["banner"]);}
            $sideResult["html"] = ' <div class="cd-timeline__block">
                <div class="cd-timeline__img cd-timeline__img--picture" load-image="'.$pageImg.'">
                </div>

                <div class="cd-timeline__content text-component">
                    <a href="'.$gen->baseLink.$row["id"].'/'.$row["shortName"].'">
                    <h2>'.$row["name"].' <span class="typeTab">'.$row["cate"].'</span></h2>
                    <p>
                        <i>'.parseIWDate($row["timeStart"], $row["timeEnd"], $dateArray, true).'</i><br>
                    </p>
                    <p class="color-contrast-medium">'.$parse->bodyParser(substr($row["body"], 0, 222)."...").'</p>
                    </a>
                    <div class="flex justify-between items-center">
                        <span class="cd-timeline__date">'.parseIWDate($row["timeStart"], $row["timeEnd"], $dateArray, false, 1).'</span>
                    </div>
                </div>
            </div> ';
            array_push($fullResult, $sideResult);
            $counter++;
        }
    }
    usort($fullResult, function($a, $b) {
        return $a['year'] - $b['year'];
    });
    foreach ($fullResult as $sideResult){
        echo $sideResult["html"];
    }

    echo ' </div>
        <p class="topinfo" style="text-align: center;">Note that articles with no date information are not listed in timelines.</p>
        </section>';
}


$fullChildLine = "";
$parse = new parse($gen->dbconn, 0, 1, $gen->domain);

$insert =  " AND a.cate LIKE '%".$genre."%'";
if ($genre == ""){$insert = "";}

$query = "SELECT a.*
FROM $gen->database a
LEFT OUTER JOIN $gen->database b
    ON a.id = b.id AND a.v < b.v
WHERE b.id IS NULL $insert ORDER BY importance DESC LIMIT 0, 999";
$counter = 0;
if ($firstrow = $gen->dbconn->query($query)) {
    if ($mode == 1){
        createTimeline($firstrow, $parse);
    }
    else {
        while ($row = $firstrow->fetch_assoc()) {
            $i = $row["id"];
            $pageName = $row["name"];
            $pageShortName = $row["shortName"];
            $pageImg = $row["banner"];
            $thumbImg = $row["sidetabImg"];
            $pageRoot = $row["root"];
            $currentGenre = $row["cate"];
            $currentCates = $row["categories"];
            if (requirements($i, $currentGenre, $currentCates)){
                if ($pageShortName != ""){
                    $pageName = $pageShortName;
                }
                $pageName = substr($pageName, 0, 22);
                if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg);}
                if ($counter < 12){
                    $fullChildLine = $fullChildLine." [wiki:art$i]";
                }
                else {
                    $fullChildLine = $fullChildLine." <p><a href='f.php?id=".$i."'>".$pageName."</a></p>";
                }
                $counter++;
            }
        }
        if ($fullChildLine == ""){echo "<p class='topinfo' style='text-align:center;padding: 60px 0 30px;'>No results found.</p>";}
        else {
            echo $parse->bodyParser($fullChildLine, 2, $gen->database);
        }
    }
}

?>
