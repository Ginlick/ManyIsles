<?php

include("../g/countries.php");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Country Codes | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
    <style>

    .countryUL {
        list-style-type: none;
        padding: 0;
        width: 32%;
        display:block;
        float: left;
    }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
    <div class="flex-container">
        <div class='left-col'>
            <a href="hub.php"><h1 class="menutitle">Partnership</h1></a>
            <ul class="myMenu">
                <li><a class="Bar" href="hub.php"><i class="fas fa-arrow-left"></i> Hub</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/docs/18/Digital_Store_Extension" target="_blank">DS Publishing</a></li>
            </ul>
        </div>

        <div id='content' class='column'>
            <h1>Country Codes </h1>
            <div class='dsBanner'><img src='/Imgs/Ranks/HighMerchant.png' alt:'Oopsie!'></div>
            <p>For shipping, you can set prices for single countries (two-letter codes) or for arrays of countries (three-letter codes) so you don't have to do it manually.<br>
            All countries the Many Isles ship to are listed in the GLO array.<br>
            You guarantee that you are able to ship products to all countries you set a shipping price for. If you do not supply some countries in the GLO array, don't list it.</p>

            <h2>Country Arrays</h2>

            <?php
            foreach ($countries as $key => $countryA) {
                echo "<ul class='countryUL'>";
                echo "<li><b><u>$key</u></b></li>";
                foreach ($countries[$key] as $code => $country) {
                    echo "<li> $code ($country)</li>";
                }
                echo "</ul>";
            }

            ?>
        </div>
    </div>


    <div w3-include-html="../g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>

</script>


