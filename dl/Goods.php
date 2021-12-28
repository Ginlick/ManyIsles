<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once("global/engine.php");

$dl = new dlengine($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <link rel="icon" href="../Imgs/Favicon.png">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="global/dl3.css">
    <title>Digital Library</title>
</head>
<style>


</style>
<body>

<div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
    <div class="flex-container">
        <div class='left-col'>
            <ul class="myMenu">
                <li><a class="Bar" href="/home"><i class="fas fa-arrow-left"></i> Home</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>

            <?php
                echo $dl->giveMenu();
            ?>

            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/docs/11/Digital_Library" target="_blank">Digital Library FAQ</a></li>
            </ul>
        </div>

        <div id='content' class='column'>
                <?php
                    echo $dl->giveSearch();
                ?>

                <h2>Discover</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodRow("random", 8);
                    ?>
                </div>
                <h2>New</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodRow("new", 8);
                    ?>
                </div>
                <h2>Popular</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodRow("popular", 8);
                    ?>
                </div>
                <h2>Recommended</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodItem(26);
                        echo $dl->prodItem(8);
                        echo $dl->prodItem(3);
                        echo $dl->prodItem(33);
                        echo $dl->prodItem(41);
                        echo $dl->prodItem(40);
                        echo $dl->prodItem(27);
                        echo $dl->prodItem(36);
                    ?>
                </div>


        </div>
    </div>

    <div w3-include-html="/ds/g/GFooter.html" w3-create-newEl="true"></div>

    <div class="bottomad-container">
        <div class="bottomad">
            <img src="global/plus.png" alt="hi" />
            <a href="/account/BePartner.php">
                Publish your own!
            </a>
        </div>
    </div>
</div>



</body>
</html>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script src="global/dl2v2.js"></script>
<script>
function seekMaker() {
  document.cookie='seeker=/dl/Goods';
}
</script>
