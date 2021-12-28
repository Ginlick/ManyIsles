<?php

if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:partner.php?id=1");}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once("../g/artTabs.php");
$pId = $_GET['id'];

$name = "";
$image="";
$jacob="";
$query = "SELECT * FROM partners WHERE id = $pId";
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $name = $row["name"];
      $image = $row["image"];
      $jacob = $row["jacob"];
      $status = $row["status"];
      $account = $row["account"];
      $pRegDate = $row["reg_date"];
    }
}
if ($name == null){header("Location: partner.php?id=1");}
if ($status == "suspended"){$jacob = "<span style='color:red'>This partnership is currently suspended, and all its products are temporarily unavailable in the digital library.</a>";}

$pUsId = 1;
$query = 'SELECT id FROM accountsTable WHERE uname = "'.$account.'"';
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $pUsId = $row["id"];
    }
}

$date_array = date_parse($pRegDate);
$pRegDate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Partner | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <style>
        .flexer {
            display: flex;
            flex-direction: row;
        }
        section.imageShower {
            width: 37%;
            padding: 20px;
        }

        .squareCont {
            width: 100%;
            position: relative;
            overflow: hidden;
        }

            .squareCont:after {
                content: "";
                display: block;
                padding-bottom: 100%;
            }

            .squareCont .square {
                position: absolute;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

        .square img {
            height: 100%;
            object-fit: contain;
        }

        section.rightOvertails {
            width: 62%;
            padding: 10px;
        }

        .overtail {
            text-align: left;
            border-bottom: 1px solid #ddd;
            margin: 10px;
            font-family: 'Montserrat', sans-serif;
            padding-bottom: 10px;
        }

            .overtail h1 {
                text-align: left;
                margin: 0;
            }

            .overtail p {
                text-align: left;
                font-family: 'Montserrat', sans-serif;
            }

            .overtail em {
                font-style: normal;
            }


            .overtail.iOvertails h5 {
                font-family: 'Montserrat', sans-serif;
                padding-top: 15px;
                margin: 0;
                font-size: min(calc(12px + .3vw), 16px);
                font-weight: normal;
            }

        .overtail.normal p {
            font-family: 'Open Sans', sans-serif;
        }


        section.details {
            width: 100%;
            color: #505050;
            font-family: 'Open Sans', sans-serif;
        }

        .details .specList {
            list-style-type: none;
            text-align: left;
            column-count: 2;
            padding: 0;
        }

        .details p, .details h2 {
            text-align: left;
        }
        .fa {
            padding: 5px 10px;
            font-size:min(calc(17px + .3vw), 22px);
        }
    .details {
            border-top: 1px solid #ddd;
            margin-top: 60px;
    }
.checkoutBox {
    margin: 10px auto;
}
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="../home.php"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="../home.php">Browse</a></li>
                </ul>
                <?php
                    $specRoot = "../";
                    include('../g/sideBasket.php');
                ?>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/33/Become_Publisher" target="_blank">Become a Publisher</a></li>

                    <li><a class="Bar" href="/wiki/h/publishing/ds.html" target="_blank">How to publish in the store?</a></li>
                    <li><a class="Bar" href="/wiki/h/partnership.html" target="_blank">Partnerships</a></li>
                    <li><a class="Bar" href="/wiki/h/publishing.html" target="_blank">Publishing to the Many Isles</a></li>
                    <li><a class="Bar" href="/wiki/h/digsto.html" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
            <div class="crumbs"><a href="../home.php">Store</a> - View Partner</div>
            <div class="flexer">
                <section class="imageShower">
                    <div class="squareCont">
                        <div class="square">
                                <img src="/dl/PartIm/<?php echo $image; ?>">
                        </div>
                    </div>
                </section>

                <section class="rightOvertails">
                    <div class="overtail">
                        <h1><?php echo $name; ?></h1>
                        <p>
                            Joined <?php echo $pRegDate; ?><br>
                            View on the <a href="/dl/Partner.php?id=<?php echo $pId; ?>" target="_blank">digital library</a><br>
                            Id: p#<?php echo $pId; ?>
                        </p>
                    </div>
                    <div class="overtail normal">
                        <p>
                            <?php echo $jacob;  ?>
                        </p>
                    </div>
                <?php if ($pUsId == $_COOKIE["loggedIn"]) {
                    echo '                    <div class="overtail" style="display:flex;justify-content:center;">
                        <div class="checkoutBox">
                            <a href="hub.php"><button class="checkout">
                                <i class="fas fa-arrow-right"></i>
                                <span>Partnership Hub</span>
                            </button>
                        </div>
                    </div>';
                }
                ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://manyisles.ch/ds/p/partner.php?id=<?php echo $pId; ?>" target="_blank" class="fa fa-facebook"></a>
                    <a href="http://www.reddit.com/submit?title=Check out <?php echo $name; ?>'s Awesome Content!&url=https://manyisles.ch/ds/p/partner.php%3Fid%3D<?php echo $pId; ?>" target="_blank" class="fa fa-reddit"></a>
                    <a href="https://twitter.com/intent/tweet?text=Check out <?php echo $name; ?>'s Awesome Content!%0A&url=https://manyisles.ch/ds/p/partner.php%3Fid%3D<?php echo $pId; ?>&hashtags=manyisles,dnd" target="_blank" class="fa fa-twitter"></a>
                    <a href="http://pinterest.com/pin/create/button/?url=https://manyisles.ch/ds/p/partner.php%3Fid%3D<?php echo $pId; ?>&media=https://manyisles.ch/dl/PartIm/<?php echo $image; ?>&description=Check out <?php echo $name; ?>'s Awesome Content!" target="_blank" class="fa fa-pinterest"></a>
                </section>


            </div>
                <section class="details">
                    <h1>Publications</h1>
<?php
    $query = 'SELECT * FROM dsprods WHERE sellerId = "'.$pId.'"';
        if ($toprow = $conn->query($query)) {
        $counter = 0;
        $showNoStock = true;
            while ($row = $toprow->fetch_assoc()) {
                if ($counter == 22){break;}
                $counter++;

                makeArtTab($row, [], true);
            }
        }
?>
                </section>
        </div>
    </div>


    <div w3-include-html="../g/GFooter.html" w3-create-newEl="true"></div>


<form id="basket" action="../basket.php" method="POST"  enctype="multipart/form-data" style="display:none;visibility:hidden;">
    <input style="display:none" name="basketing" id="basketing" value="1" />
    <input style="display:none" name="quickBuy" value="1" />
    <input style="display:none" name="goTo" id="goTo" value="p/partner.php?id=<?php echo $pId; ?>" />
</form>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>

function showView(element, newimg) {
    element.src = newimg;
}

function purchase(item){
    document.getElementById("basketing").value = item;
    document.getElementById("basket").submit();    
}
</script>


