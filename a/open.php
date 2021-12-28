<?php

$servername = "localhost:3306";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "manyisle_accounts";

if ($_SERVER['REMOTE_ADDR']=="::1"){
$servername = "localhost";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "accounts";
}

$conn = new mysqli($servername, $username, $password, $dbname);

$query = "SELECT * FROM adventures";
$opens_mysqli = $conn->query($query);




?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="../Imgs/Favicon.png">
    <title>Account</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="/account/g/GGMdl.css">
    <style>

        .gamesTable {
            font-family: "Arial";
            font-size: 16px;
            float: left;
            display: inline-block;
            border-collapse: collapse;
            width: 100%;
            padding: 2vw;
        }

        .gamesTable {
            text-align: left;
        }

            .gamesTable thead, .gamesTable tbody, .gamesTable tr {
                width: 100%;
            }

                .gamesTable thead tr {
                    background-color: #f3f3f3;
                }

                .gamesTable tr:nth-child(even) {
                    background-color: #f3f3f3;
                }

                .gamesTable tbody > tr > :nth-child(1) {
                    column-width: 25vw;
                }

                .gamesTable tbody > tr > :nth-child(2) {
                    column-width: 25vw;
                }

                .gamesTable tbody > tr > :nth-child(3) {
                    column-width: 8vw;
                }

                .gamesTable tbody > tr > :nth-child(4) {
                    column-width: 8vw;
                }

                .gamesTable tbody > tr > :nth-child(5) {
                    column-width: 25vw;
                }

                .gamesTable tbody > tr > :nth-child(6) {
                    column-width: 30vw;
                }

                .gamesTable tbody > tr > :nth-child(7) {
                    column-width: 12vw;
                }

            .gamesTable button {
                background-color: #53b2e3;
                transition: .1s ease;
                color: #686b6c;
            }

                .gamesTable button:hover {
                    background-color: #3aa7e0;
                }


            .gamesTable th, .gamesTable td {
                padding: 3px;
            }

        .provoker {
            position: relative;
            display: inline-block;
        }

            .provoker .playersNode {
                visibility: hidden;
                width: 12vw;
                background-color: #ffffff;
                text-align: left;
                padding: 5px;
                border-radius: 6px;
                position: absolute;
                z-index: 1;
                top: 125%;
                left: 50%;
                margin-left: -60px;
                opacity: 0;
                transition: opacity 0.3s;
                box-shadow: 0 0 5px grey;
            }


            /* Show the tooltip text when you mouse over the tooltip container */
            .provoker:hover .playersNode {
                visibility: visible;
                opacity: 1;
            }
    </style>
</head>
<body>
    <div style="flex: 1 0 auto;">
        <div w3-include-html="/Code/CSS/GTopnav.html"></div>

        <div class="contentBlock" style="margin-top: 5vw;">

            <div class="banner" style="position:static">
                <picture>
                    <source srcset="/Imgs/BannerCM.png" media="(max-width: 1400px)">
                    <source srcset="/Imgs/BigBannerCM.png">
                    <img src="/Imgs/BigBannerCM.png" alt="Banner" style='width:100%;display:block'>
                </picture>
            </div>

            <h1>Open Games</h1>
            <p>You can join any of the open games below.</p>
            <table class="gamesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Host</th>
                        <th>System</th>
                        <th>Region</th>
                        <th>Players</th>
                        <th>Description</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $opens_mysqli->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row["name"]."</td>";
                        echo "<td>".$row["host"]."</td>";
                        echo "<td>".$row["syst"]."</td>";
                        echo "<td>".$row["region"]."</td>";
                        
                        echo "<td class='provoker'>".$row["players"]."<span class='playersNode'>".$row["players"]."</span></td>";
                        echo "<td>".$row["descr"]."</td>";
                        echo "<td><button>Join</button></td>";
                    }
                    
                    ?>


                    <tr>
                        <td>KMiO</td>
                        <td>Me</td>
                        <td>5e</td>
                        <td>3</td>
                        <td class="provoker">Players<span class="playersNode">Hansfired, Ginlic</span></td>
                        <td>Awersome text reaööy coolAwersome text reaööy coolAwersome text reaööy coolAwersome text reaööy coolAwersome text reaööy cool</td>
                        <td><button>Join</button></td>
                    </tr>
                    <tr>
                        <td>KMiO</td>
                        <td>Me</td>
                        <td>5e</td>
                        <td>3</td>
                        <td>Players</td>
                        <td>Awersome text reaööy coolAwersome text reaööy coolAwersome text reaööy coolAwersome text reaööy coolAwersome text reaööy cool</td>
                        <td><button>Join</button></td>
                    </tr>
                </tbody>

            </table>



        </div>





    </div>
</body>
</html>

<script>function includeHTML() {
        var z, i, elmnt, file, xhttp;
        z = document.getElementsByTagName("*");
        for (i = 0; i < z.length; i++) {
            elmnt = z[i];
            file = elmnt.getAttribute("w3-include-html");
            if (file) {
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4) {
                        if (this.status == 200) { elmnt.innerHTML = this.responseText; }
                        if (this.status == 404) { elmnt.innerHTML = "Page not found."; }
                        elmnt.removeAttribute("w3-include-html");
                        includeHTML();
                    }
                }
                xhttp.open("GET", file, true);
                xhttp.send();
                return;
            }
        }
    }
    includeHTML();</script>
