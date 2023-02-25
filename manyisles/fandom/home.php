<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/parseIWDate.php");
require($_SERVER['DOCUMENT_ROOT']."/wiki/parse.php");
$wparse = new parse($conn, 0);

$fullChildLine = ""; $bigChildLine = "";
$seenArray = array();
$query = "SELECT a.*
FROM pages a
LEFT OUTER JOIN pages b
    ON a.id = b.id AND a.v < b.v
WHERE a.root = 0 AND b.id IS NULL ORDER BY pop DESC LIMIT 0, 22";
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        //create imgCont
        $id = $row["id"];
        $article = new article(["id"=>$id], $conn);
        if ($article->status != "suspended"){
          //check visibility
          $visible = true;
          $query = "SELECT visibility FROM wiki_settings WHERE id = $id";
          if ($firstrow2 = $conn->query($query)) {
            if (mysqli_num_rows($firstrow2) > 0){
              while ($row2 = $firstrow2->fetch_assoc()) {
                if ($row2["visibility"]=="hidden"){
                  $visible = false;
                }
              }
            }
          }
          if ($visible){
            $wikiThumbnail = $wparse->bodyParser("[wiki:art$id]", 2);
            if (isset($article->bodyInfo["meta"]["description"]) AND $article->bodyInfo["meta"]["description"] != ""){
                $toAdd = "<div class='wikiThumbCont'><div class='wikiThumbContLeft'>".$wikiThumbnail."</div><div class='wikiThumbContRight'>";
                $toAdd .= "<h3>".$article->name." Wiki</h3>";
                $toAdd .= $wparse->bodyParser($article->bodyInfo["meta"]["description"]);
                $toAdd .= "</div></div>";
                $bigChildLine .= $toAdd;
            }
            else {
                $fullChildLine .= $wikiThumbnail;
            }

          }
        }

        // $pageName = $article->shortName;
        // $pageImg = $row["banner"];
        // $thumbImg = $row["sidetabImg"];
        // $pageStatus = $row["status"];
        // $root = $row["root"];
        // if (!in_array($id, $seenArray)){
        //     array_push($seenArray, $id);
        //     if ($root == 0){
        //         if ($pageStatus != "suspended"){
        //           //check visibility
        //           $visible = true;
        //           $query = "SELECT visibility FROM wiki_settings WHERE id = $id";
        //           if ($firstrow2 = $conn->query($query)) {
        //             if (mysqli_num_rows($firstrow2) > 0){
        //               while ($row2 = $firstrow2->fetch_assoc()) {
        //                 if ($row2["visibility"]=="hidden"){
        //                   $visible = false;
        //                 }
        //               }
        //             }
        //           }

        //           if ($visible){
        //             //add entry
        //             if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg);}
        //             $fullChildLine .= " <div class='domCont'><a href='/fandom/wiki/".$id."/article'><img src='".$pageImg."' /> <h3>".$pageName."</h3><div class='overlay'></div></a></div>";
        //           }
        //         }
        //     }
        // }
    }
}

$canedit = false;
$uid = 0;
if (isset($_COOKIE["loggedIn"])) {
    $uid = $_COOKIE["loggedIn"];
    $query = "SELECT * FROM slots WHERE id = ".$_COOKIE["loggedIn"];
    $result = $conn->query($query);
        while ($row = $result->fetch_assoc()){
            if ($row["a"]==null){$setto="a";}
            else if ($row["b"]==null){$setto="b";}
            else if ($row["c"]==null){$setto="c";}
            else if ($row["d"]==null){$setto="d";}
            else if ($row["e"]==null){$setto="e";}
            else if ($row["f"]==null){$setto="f";}
            else if ($row["g"]==null){$setto="g";}
            else if ($row["h"]==null){$setto="h";}
            else if ($row["i"]==null){$setto="i";}
            else if ($row["j"]==null){$setto="j";}
            else $setto = "";
        }
    $canedit = true;
    if (isset($setto) AND $setto == "") {$canedit = false;}
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconWiki.png">
    <title> Home | Fandom</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/wiki/wik.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <style>
        .wikiThumbCont {
            padding: 15px;
            display: flex;
        }
        .wikiThumbCont .wikiThumbContLeft, .wikiThumbContRight {
            padding: 10px;
            min-width: 222px;
        }
        .wikiThumbContLeft .domCont {
            width: 100%;
        }
        .nop p {
            display: none;
        }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>
    <div class="content">
        <div class="fandomcoll">
            <div class="col-l">
                <h2>Many Isles</h2>
               <div class="bottButtCon"><a href="/home"><button class="wikiButton" >Home</button></a></div>
            </div>
            <div class="col-l">
                <h2>Many Isles Fandom</h2>
                <p>Get your lore public!</p>

                    <?php if ($canedit) {
                        echo ' <div class="bottButtCon"><a href="edit.php?new=1"><button class="wikiButton" >Create Wiki</button></a></div>';
                    }
                    else if (!isset($_COOKIE["loggedIn"])) {
                        echo '<div class="bottButtCon"><a href="/account/home?error=SignIn"><button class="wikiButton" >Sign In</button></a></div>"';
                    }
                    else {
                        echo "<p>Currently, your slots are full.</p>";
                    }


                    ?>

            </div>
        </div>

        <div class="fandomrcoll">
        <div class="col-r" style="margin-bottom:50px;">
            <img src="/wikimgs/banners/fandom.png" alt="oops" class="topBanner" />
            <p class="topinfo"><a href="/fandom/home">Fandom</a></p>

            <h1>Fandom</h1>
            <p>
                The Many Isles Fandom Wiki is an awesome platform where you can create extensive wikis about your setting, for free.  <?php if (!isset($_COOKIE["loggedIn"])) {echo ' <a href="/account/home">Create an account</a> to get started! '; } ?><br />
                You can find the documentation <a href="/docs/20/Fandom">here</a>.
            </p>


            <h2>Explore Wikis</h2>
            <p>


                <?php echo $bigChildLine; echo "<div class='nop'>".$fullChildLine."</div>"; ?>


        </div>
        </div>
    </div>


    <div w3-include-html="/fandom/fandomFooter.html" w3-create-newEl="1"></div>


</body>
</html>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script src="/wiki/wik.js"></script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "susp"){
    createPopup("d:poet;txt:The article you tried to visit is suspended and cannot be viewed.");
}
else if (why == "nexist"){
    createPopup("d:poet;txt:This article could not be found.");
}
else if (why == "cantedit"){
    createPopup("d:poet;txt:Sorry, you cannot edit this wiki.");
}
</script>
