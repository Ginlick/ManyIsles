<?php


if (isset($_GET["g"])) {
    if (preg_match("/[^A-Za-z\- ]/", $_GET['g'])==1){header("Location:../f.html");exit();}
    $genre = $_GET["g"];
    if ($genre == "Homepage"){$genre = "Lore";}
}
else {
    $genre = null;
}

if (isset($_GET["c"])) {
    if (preg_match('/[^"< ]/', $_GET['c'])!=1){header("Location:../f.html");exit();}
    $categories = $_GET["c"];
}
else {
    $categories = null;
}

if (isset($_GET["w"])) {
    if (preg_match("/[^0-9]/", $_GET['w'])==1){header("Location:../f.html");exit();}
    $wiki = intval($_GET["w"]);
}
else {
    $wiki = 1;
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, $wiki, true);
$conn = $gen->conn;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title>Search <?php echo $gen->wikiName; ?> wiki | Fandom</title>
    <link rel="stylesheet" href="/wiki/res/timeline.css">
    <style>
#resultDIV p {
    margin-left:2.5%;
}

    </style>
</head>
<body  style="<?php echo $gen->giveWikStyle(); ?>">
  <?php echo $gen->giveTopBar(); ?>
    <div class="content">
        <div class="fandomcoll">
            <?php
                echo $gen->giveLHomer("You are searching the ");
            ?>
        </div>
        <div class="fandomrcoll">
            <div class="col-r">
                <input type="text" class="wikisearchbar" placeholder="Search <?php echo $gen->wikiName; ?> wiki..." id="viewRoot1"  oninput="offerSuggestions(this, 'suggestNow', 1);" onfocus="offerSuggestions(this, 'suggestNow', 1);" autocomplete="off"></input>
                <div class="suggestions"  style="transform: translate(0, 31px);"></div>

                <img src="<?php  echo banner($gen->article->banner); ?>" alt="oops" class="topBanner" />
                <div class="topinfo"><a href="/fandom/home">Fandom</a> - <a href="/fandom/wiki/<?php echo $gen->parentWiki; ?>/home"><?php echo $gen->wikiName; ?></a> - <a href="#">Search</a></div>
                <h1>Search Results<a href="f.php?id=<?php echo $wiki; ?>"><span class='typeTab'><?php echo $gen->wikiName; ?> wiki</span></a></h1>
                <div style="width:100%;">
                    <div style="display:inline-block;padding:9px;"
                        <label for="genre">genre:</label>
                        <select id="genre" name="genre" onchange="getResults();">
                        <?php
                    foreach ($gen->cateoptions as $option){
                        echo '<option value="'.$option["value"].'">'.$option["name"].'</option>';
                    } 
                    echo "<option value='Source'>Source</option>"
                    ?>
                        </select>
                    </div>
                    <div style="display:inline-block;padding:9px;"
                        <label for="cate">category:</label>
                        <select id="cate" name="cate" onchange="getResults();">
    <?php
    $query = "SELECT * FROM wikicategories WHERE wiki = ".$wiki;
    if ($max = $conn->query($query)){
        while ($row = $max->fetch_assoc()){
            $cateId = $row["id"];
            $cateName = $row["name"];
            echo '<option value="'.$cateId.'">'.$cateName.'</option>';
        }
    }


    ?>                       </select>

                    </div>
                    <div style="display:inline-block;padding:9px;"
                        <label for="mode">view:</label>
                        <select id="mode" name="mode" onchange="getResults();">
                            <option value="0">Thumbnails</option>
                            <option value="1">Timeline</option>
                        </select>
                    </div>
                </div>
             <div id="resultDIV" style="box-sizing: border-box;">
                </div>
            </div>
        </div>
        </div>

        <div id="modal" class="modal" onclick="removePops()">
        </div>
        <div id="mod" class="modCol">
            <div class="modContent">
                <img src="/Imgs/PopPoet.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
            </div>
        </div>

    </div>

    <?php echo $gen->giveFooter(); ?>
</body>
</html>
<?php echo $gen->giveScripts(); echo $gen->giveSScript($genre, $categories); ?>
