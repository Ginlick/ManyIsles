<?php
require_once("g/eventEngine.php");
$eventE = new eventEngine;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
    <?php
    echo $eventE->giveHead();
    ?>
    <style>
    </style>
</head>
<body>
<div w3-include-html="/global/css/GTopnav.html" w3-create-newEl="true"></div>
<section class="cont-cont">
    <section class="contcol-wrapper eContCol">
        <?php
        echo $eventE->giveLeft();
        ?>
        <section class="column-main">
            <h1>Hi</h1>
            <p>This place requires a feed w/ the coming events (JS!!! WOOHOOO!!!!!)</p>
        </section>
    </section>
</section>
<div w3-include-html="/global/css/genericFooter.html" w3-create-newEl="true"></div>
</body>
</html>
<script src="/global/css/global.js"></script>
<script>
    var urlParams = new URLSearchParams(window.location.search);
    var why = urlParams.get('why');
    if (why == "itemDeleted") {
    }


</script>
