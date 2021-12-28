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

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconCreate.png">
    <title>Create!</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <style>

        p {
            font-size: calc(12px + 0.9vw);
        }
        * {
            box-sizing: border-box;
        }
        .topnav {
            overflow: hidden;
            margin:0;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        .row {
            display: flex;
        }
        #myMenu {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

            #myMenu li p, #myMenu li a {
                padding: 0.9vw;
                font-size: 1.4vw;
                text-decoration: none;
                color: black;
                display: block;
                text-align:left;
                margin: 1em 0 1em 0;
            }

                #myMenu li p:hover, #myMenu li a:hover {
                    background-color: #9b0000;
                }
        .topnav a {
            float: left;
            color: black;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

            .topnav a:hover {
                color: red;
            }
        .column {
            float: left;
        }
        .topnav p {
            float: left;
            color: black;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            color: #d10e04;
        }

            .topnav p:hover {
                color: white;
            }
        .flex-container {
            display: flex;
            align-items: stretch;
            min-height:80%;
        }
        @media only screen and (max-width:800px) {
            .flex-container {
                min-height: 90%;
            }
        }
        @media only screen and (max-width:200px) {
            .flex-container {
                min-height: 97%;
            }
        }
        h1, h2, h3, p {
            text-align: center;
        }

        p {
            font-size:calc(12px + 0.9vw);
            text-align:center;
            padding:0 4% 2% 4%;
}

</style>
</head>
<body>
    <div style="flex: 1 0 auto;">
        <div>
            <img src="../Imgs/createTop.png" alt="Create!" class='separator' style='display:block;'>
        </div>
        <div class="banner">
            <picture>
                <source srcset="../Imgs/Banner.png" media="(max-width: 1400px)">
                <source srcset="../BigBanner.png">
                <img src="../BigBanner.png" alt="Banner" style='width:100%;display:block'>
            </picture>

            <div style='width:100%; background-color:#ff0000;'>
                <ul class="topnav">
                    <li> <a href="../Code/CodeMain.html" style='float:left;color:yellow;width:auto;'>Home</a></li>
                </ul>
            </div>
        </div>

        <div class="flex-container">
            <div class='column' style='width:20%;flex-grow: 1; background-color:#ce0202;display:inline-block;float:left'>
                <ul id="myMenu">
                    <li onclick='clinnation("AboutBar")'><a id='AboutBar' href="CreateMain.html?show=about" style="color:#cfb900" >About Create!</a></li>
                    <li onclick='clinnation("ContactBar")'> <a id='ContactBar' href="CreateMain.html?show=contact" style="color:#cfb900">Contact Us</a></li>
                    <li onclick='clinnation("AccBar")'> <p id="AccBar" style="color:white" >Goals</p></li>
                    <li onclick='clinnation("SpellBar")'> <a id='SpellBar'href="CreateMain.html?show=spells" style="color:#cfb900">Submit Spell</a></li>
                    <li onclick='clinnation("ProdBar")'> <a id='ProdBar' href="CreateMain.html?show=prod" style="color:#cfb900">Submit Project</a></li>
                    <li onclick='clinnation("PartBar")'> <a id='PartBar' href="CreateMain.html?show=part" style="color:#cfb900" >Become a Partner</a></li>
                </ul>
            </div>

            <div class='column' style='width:80%;flex-grow: 1 1;float:right; padding:16px;'>
                <h1>Our Goals</h1>
                <p>In the Many Isles, we use goals to fuel our own creativity and encourage everyone to continue onwards. Help us achieve this goal, and we'll do the prize!</p>
                <h2>Getting There</h2>
                <p>Let's get 30 account holders!</p>

                <div style="width:80%;background-color:white;margin:auto;height:11%;padding:1%;border-radius:5px">
                <?php
                $members = mysqli_num_rows($conn->query("SELECT * FROM accountsTable"));
                if ($members >= 30) { $members = 30; mail ("godsofmanyisles@gmail.com", "Poll is Complete!", "Get to work in the Goals page cause it's done");}
                ?>
                <div style="width:<?php echo $members * 3.33333; ?>%;background-color:#61b3dd;height:98%;border-radius:5px 0px 0px 5px;">
                <p style="text-align:center;color:#ff0000;position:relative;z-index:2;padding:0;margin:0;height:100%;transform: translate(0%, 30%);font-family:Arial;"><span style="padding:30px;"><?php echo $members; ?>/30</span></p></div>
                </div>

                <h2>Prize</h2>
                <p style="margin-bottom:50px;">Once we reach this, we'll make a discord so that our community can grow all the more!</p>
            </div>

        </div>
    </div>
    <footer style="flex-shrink:0;">
        <img src="/Imgs/Footer.png" alt="Footer" class='separator' style='display:block;width:100%'>
    </footer>

</body>
</html>


