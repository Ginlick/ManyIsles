<?php
require_once "g/eventEngine.php";
$eventE = new eventEngine;

$eventsList = file_get_contents("https://media.manyisles.ch/events/eventsList.json");
$downloadsList = file_get_contents("https://media.manyisles.ch/events/downloadsList.json");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
    <link rel="icon" href="/Imgs/Favicon.png">
    <?php
    echo $eventE->giveHead();
    ?>
    <style>
        .cont-cont {
            min-height: 100vh;
        }
        .fileDownloaderCont {
            margin: 5em 0;
        }
        .fileDownloader {
            border: 1px solid var(--col-dgrey);
            border-radius: 10px;
            margin: 2em auto;
            max-width: 600px;
            position: relative;
            display: flex;
            align-items: row;
        }
        .fileDownloader:hover {
            background-color: var(--all-color-albord);
        }
        .fileDownloader a {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
        }
        .fileDownloader img {
            max-height: 80px;
            padding: 1em;

        }
        .fileDownloader p {
            display: flex;
            font-weight: bold;
            align-items: center;
        }
        :root {
            --wiki-color-quote: #d0952f;
        --wiki-color-quotebody: #f0dab4;
        }
        .quote {
            margin: 10px 0;
            border-left: 5px solid var(--wiki-color-quote);
            padding: 10px 10px 10px 15px;
            background-color: var(--wiki-color-quotebody);
            overflow: hidden;
        }
    </style>
</head>
<body>
<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>
<section class="cont-cont">
    <section class="contcol-wrapper eContCol">
        <section class="column-main">
            <h1>Many Isles Events</h1>
                <p>The <a href="https://manyisles.org" target="_blank">Zurich student fantasy association</a> hosts events throughout the semester! Find an up-to-date list on the association website, <a href="https://manyisles.org/events" target="_blank">manyisles.org</a>.</p>
            </div>
        </section>
    </section>
</section>
<div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script type="module">



</script>
