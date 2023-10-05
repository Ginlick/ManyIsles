<?php
require_once "g/eventEngine.php";
$eventE = new eventEngine;

$chars = [];
$query = "SELECT * FROM ToHcharacters;";
$conn = $eventE->addConn("accounts");
if ($result = $conn->query($query)){
    while ($row = $result->fetch_assoc()) {
        $chars[] = $row;
    }
}

function sortChars ($a, $b) {
    if ($a["wins"] == $b["wins"]){return 0;}
    return ($a["wins"] < $b["wins"]) ? 1 : -1;
}

usort($chars, "sortChars");



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
            min-height: 100%;
        }

        .leaderboardTable {
            width: 100%;
            background-color: var(--all-color-albord);
        }
        thead td {
            font-weight: bold;
        }
        .leaderboardTable td {
            font-family: var(--gen-fontfams-base);
            font-size: var(--all-fonts-base);
            padding: 5px;
        }
        .leaderboardTable tbody tr:nth-child(odd) {
            background-color: var(--col-lightwite);
        }
    </style>
</head>
<body>
<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>
<section class="cont-cont">
    <section class="contcol-wrapper eContCol">
        <section class="column-left">
            <img class="inColumnFiller" src="imgs/ToH_inverse.jpg" alt="Tournament of Heroes" />
            <p>Find a leaderboard of the tournament here. To check out coming sessions, see the events page.</p>
            <a href="events"><button class="eventButton">Events List</button></a>
        </section>
        <section class="column-main">
            <h1>Tournament of Heroes Leaderboard</h1>
            <table class="leaderboardTable">
            <thead>
                <tr><td>Name</td><td>Player</td><td>Wins</td>
            </thead>
            <tbody>
                <?php
                foreach  ($chars as $char){
                    echo "<tr><td>".$char["name"]."</td><td>".$char["player"]."</td><td>".$char["wins"]."</td></tr>";
                }
                ?>
            </tbody>
            </table>        
        </section>
    </section>
</section>
<div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script type="module">



</script>
