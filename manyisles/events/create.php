<?php
require_once("g/eventEngine.php");
$eventE = new eventEngine;


?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <?php
    echo $eventE->giveHead();
    ?>
    <style>
        .Box {
            border: var(--e-defaultborder);
            border-radius: 6px;
            margin: 0;
            padding: 8px 10px;
        }

        .Box.default {
            margin: 15px 0;
        }

        .Box h2, .Box h3 {
            margin: 20px 0 10px;
        }

        input, textarea, select {
            border: var(--e-defaultborder);
            background-color: var(--e-inset);
            font-size: var(--all-fonts-base);
            font-family: var(--all-fonts-text);
            padding: 5px 12px;
            border-radius: 6px;
        }

        label {
            font-size: var(--all-fonts-med);
            font-family: var(--all-fonts-text);
        }

        .inBoxForm input, .inBoxForm textarea, .inBoxForm select {
            margin: 4px 0 8px;
            resize: none;
        }
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
            <h1>Create Event</h1>
            <div class="Box default">
                <p>later: Series here (button to select parent series where helper/owner, none, or "create new")</p>
            </div>
            <div class="Box default">
                <form>
                    <div class="inBoxForm">
                        <label for="name">Event Name</label>
                        <input type="text" name="name" id="name" placeholder=""/>
                        <label for="date-happens">Date</label>
                        <input type="date" name="date-happens" id="date-happens" value="" min="" max=""
                               onload="fixDate(this, min)">
                        <label for="location">Location</label>
                        <input name="location" id="location" type="text" placeholder=""/>
                        <label for="desc">Description</label>
                        <textarea name="desc" id="desc" placeholder="" rows="5"></textarea>
                        <h3>Extra Information</h3>
                        <label for="eType">Event Type</label>
                        <input name="type" id="eType" type="text" placeholder="RPG"/>
                        <label for="date-opens">Opening Date</label>
                        <input type="date" name="date-opens" id="date-opens" value="" min="" max=""
                               onload="fixDate(this, min)">
                        <h3>Settings</h3>
                    </div>
                </form>
            </div>
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
