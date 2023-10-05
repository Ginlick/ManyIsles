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
        .eventEntry {
            margin: 20px 10px;
            border: 2px solid var(--e-lightshade);
            border-radius: 10px;
            padding: 10px;
        }
        .eventEntry h3 {
            margin: 10px 0;
        }
        .eventEntry .date {
            font-style: italic;
        }
        .eventEntry .place {
            padding-bottom: 10px;
        }
        b {
            font-weight: bolder;
        }
        .downloadsLink {
            display: block;
            font-weight: bold;
            padding: 5px 0;
        }

    </style>
</head>
<body>
<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>
<section class="cont-cont">
    <section class="contcol-wrapper eContCol">
        <section class="column-left">
            <div class="inColumnLeft unaccented">

            </div>
            <div class="inColumnLeft">
                <h2>Discover Us</h2>
                <p>
                    Come and hang out! Most of our events are free, open to everyone, and don't require registration.
                </p>
                <h2>Contact</h2>
                <p>
                    Meet the community on discord. Don't worry, we don't bite!
                </p>

                <a href="https://discord.gg/XTQnR7mS3D" target="_blank"><button class="eventButton">Discord</button></a>
                <p>Alternatively, <a href="mailto:events@manyisles.org" target="_blank">write us an email</a>.</p>
            </div>
            <!--
            <div class="inColumnLeft">
                <img class="inColumnFiller" src="imgs/ToH_inverse.jpg" alt="Tournament of Heroes" />
                <p>Find the leaderboard of our Tournament of Heroes one-shots!</p>
                <a href="tournamentofheroes"><button class="eventButton">Check it Out</button></a>
            </div> 
             -->
            <div class="inColumnLeft">
                <h2>Downloads</h2>
                <div id="downloadsContainer"></div>
            </div>
       </section>
        <section class="column-main">
            <h1>Our Events</h1>
            <section id="eventsContainer"></section>
        </section>
    </section>
</section>
<div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script type="module">
    var urlParams = new URLSearchParams(window.location.search);
    var why = urlParams.get('why');
    if (why == "itemDeleted") {
    }
    const eventsList = <?php echo $eventsList; ?>;
    const downloadsList = <?php echo $downloadsList; ?>;

    for (let eventData of eventsList) {
        var eventBlock = document.createElement("DIV");
        eventBlock.classList.add("eventEntry");
        let eventTitle = document.createElement("H3");
        eventTitle.innerHTML = eventData["name"];
        eventBlock.appendChild(eventTitle);
        let eventDate = document.createElement("p");
        eventDate.classList.add("date");
        eventDate.innerHTML = eventData["time"];
        eventBlock.appendChild(eventDate);
        let eventPlace = document.createElement("p");
        eventPlace.classList.add("place");
        eventPlace.innerHTML = eventData["place"];
        eventBlock.appendChild(eventPlace);
        let description = document.createElement("p");
        description.innerHTML = eventData["description"];
        eventBlock.appendChild(description);
        let bring = document.createElement("p");
        bring.innerHTML = "<b>Bring along:</b> " + eventData["bring"];
        eventBlock.appendChild(bring);
        let fee = document.createElement("p");
        fee.innerHTML = "<b>Participation fee:</b> " + eventData["fee"];
        eventBlock.appendChild(fee);
        document.getElementById("eventsContainer").appendChild(eventBlock);
    }

    for (let dlData of downloadsList){
        var parent = document.createElement("A");
        parent.classList.add("downloadsLink");
        parent.setAttribute("href", dlData["url"]);
        parent.setAttribute("target", "_blank");
        parent.innerHTML = dlData["name"];
        document.getElementById("downloadsContainer").appendChild(parent);
    }


</script>
