<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$fullChildLine = "";
$seenArray = array();
$query = "SELECT * FROM pages ORDER BY v DESC";
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        $id = $row["id"];
        $pageName = $row["shortName"];
        $pageImg = $row["banner"];
        $thumbImg = $row["sidetabImg"];
        $pageStatus = $row["status"];
        $root = $row["root"];
        if (!in_array($id, $seenArray)){
            array_push($seenArray, $id);
            if ($root == 0){
                if ($pageStatus != "suspended"){
                    if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = "/wikimgs/banners/".$pageImg;}
                    $fullChildLine = $fullChildLine." <div class='domCont'><a href='f/f.php?id=".$id."'><img src='".$pageImg."' /> <h3>".$pageName."</h3><div class='overlay'></div></a></div>";
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconWiki.png">
    <title>Fandom | Many Isles Wiki</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/wiki/wik.css">
</head>
<body>
    <div class="content">
        <div class="col-l" wiki-include-html="/wiki/col-l.html">
        </div>
        <div class="col-r">
            <p class="topinfo"><a href="/wiki.html">Many Isles Wiki</a> - <a href="#">Fandom</a></p>
            <h1>Fandom Wiki</h1>
            <p>
                Written by the Many Isles community, you can find anything here: lore of Karte-Caedras, meta information, fan pages, and much more. Participate yourself after <a href="/account/Account">creating an account</a>!<br />
                The Fandom wiki is a hub in which other wikis are ordered. The Karte-Caedras wiki is for Many Isles lore, while the Many Isles wiki is for community lore.<br />
                If you want to learn more about participating in the Fandom Wiki, check out this <a href="h/fandom.html">article</a>.
            </p>

            <h2>Explore</h2>
            <p>
                Our great community-created wikis. Enjoy the ride!

                <?php echo $fullChildLine; ?>


        </div>
    </div>
</body>
</html>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script src="/wiki/wik.js"></script>
