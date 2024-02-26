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
    </style>
</head>
<body>
<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>
<section class="cont-cont">
    <section class="contcol-wrapper eContCol">
        <section class="column-main">
            <h1>Many Isles Events</h1>
                <p>We host many fantastic events throughout the semester! We're currently working on an events platform, which will be available at <a href="https://manyisles.org" target="_blank">manyisles.org</a>.</p>
                <p>In the meantime, you can download the <b>semester programme</b> below, and sign up to events via our <a href="https://discord.gg/XTQnR7mS3D" target="_blank">discord</a>.
                <div class="fileDownloaderCont">
                <div class="fileDownloader">
                    <a href="files/24_1_Semester_Programme.pdf" target="_blank"></a>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/PDF_file_icon.svg/1667px-PDF_file_icon.svg.png" alt="pdf" />
                    <p>Many Isles Semester Programme 24_1</p>
                </div>

                <div class="fileDownloader">
                    <a href="files/2024_Programme.pdf" target="_blank"></a>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/PDF_file_icon.svg/1667px-PDF_file_icon.svg.png" alt="pdf" />
                    <p>Many Isles Programme 2024</p>
                </div>
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
