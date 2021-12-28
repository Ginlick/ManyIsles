
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <link rel="icon" href="../Imgs/Favicon.png">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="global/dl2.css">
<title>Products</title>
</head>
<style>


</style>
<body onresize="hideSome();">
<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$producttab = <<<YEAH
           <div class="container MEGANUM">
            <a  href="View.php?id=SENDMETOTHEPRODUCT&t=m">
            <div class="imgCont" load-image="/IndexImgs/GREATINDEXIMAGE" id="recMEGANUM">
            </div>
            <div class='titling'>GRANDTITLE</div></a>    </div>
YEAH;
?>

<div class="all-container">
   <div include-html="global/gprods.html">
   </div>


<div class="maincontain">

    <div  include-html="global/gsearch.html" class="search">
    </div>

    <div>
    <div include-html="global/gmenu.html" class="sideMenu">
    </div>
    </div>

    <div class="content">
        <div class="digCont">

<div id="nodeContainer"></div>
                <h1>New</h1>
        <div>
        <?php

        $latestid = "whelp";
        $checker = 0;

          if ($firstrow = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 1")) {
            while ($row = $firstrow->fetch_row()) {
              $latestid = $row[0];
            }
                for ($x = 0; $x>-1; $x++) {
                 $forid = $latestid - $x;
                 if (checkStat($forid, "products") == false){continue;};
                 $query = sprintf("SELECT * FROM products WHERE id = %s", $forid);
                 if ($toprow = $conn->query($query)) {
                   while ($row = $toprow->fetch_assoc()) {
                     $titling = $row["name"];
                     if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
                     $image = $row["image"];
                     $link = $row["id"];
                     $premium = false;
                     if ($row["tiers"]!="g"){$premium = true;}
                     makeFirstProdTab($producttab, $titling, $image, $link, $premium, "e");
                   }
               } else {continue;}
                   global $checker;
                   if ($checker==8){ break;}
                   if ($x==22){break;}
               }

          }

        function checkStat($prodid, $t) {
            global $conn;
            $query = 'SELECT partner FROM '.$t.' WHERE id ='.$prodid;
            $result = $conn->query($query);
            $partner = "x";
            while ($row = $result->fetch_assoc()) {
                $partner = $row["partner"];
            }
            $query = 'SELECT status FROM partners WHERE name ="'.$partner.'"';
            $status = "active";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                $status = $row["status"];
            }
            if ($status == "suspended"){return false;}
            else {return true;}
        }

         function makeFirstProdTab($producttab, $titling, $image, $link, $premium, $who) {
                global $checker, $nume3, $nume4, $nume2;
                if ($who=="e"){$checker++;$producttab = str_replace("MEGANUM", $checker, $producttab);}
                else if ($who=="h"){$nume2++;$producttab = str_replace("MEGANUM", $nume2, $producttab);}
                else if ($who=="a"){$nume4++;$producttab = str_replace("MEGANUM", $nume4, $producttab);$producttab = str_replace("t=m", "t=a", $producttab);}
                else{$nume3++;$producttab = str_replace("MEGANUM", $nume3, $producttab);$producttab = str_replace("t=m", "t=d", $producttab);}
                $producttab = str_replace("GREATINDEXIMAGE", $image, $producttab);
                $producttab = str_replace("GRANDTITLE", $titling, $producttab);
                $producttab = str_replace("SENDMETOTHEPRODUCT", $link, $producttab);
        
                if ($premium == true){$producttab = str_replace("class='titling'", "class='titling premium'", $producttab);}
                echo $producttab;      
         }

        ?>
        </div>
        </div>

        <div class="digCont">
                <h1>Popular</h1>
        <?php

        $sql = "SELECT * FROM products ORDER BY popularity DESC";
        $result = $conn->query($sql);

        $nume2 = 0;
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
             if (checkStat($row["id"], "products") == false){continue;};
             if ($nume2 == 8 ){break;}
             $premium = false;
             if ($row["tiers"]!="g"){$premium = true;}
             $titling = $row["name"];
             if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
             makeFirstProdTab($producttab, $titling, $row["image"], $row["id"], $premium, "h");
          }
        }




        ?>

        </div>


        <div class="digCont">
                <h1>Recommended</h1>
                   <div class="container">
                    <a  href="View.php?id=26">
                        <div class="imgCont" load-image="/IndexImgs/Coldmts.jpg" id="recX">
                        </div>
                    <div class='titling'>On Cold Mountains</div></a>    </div>

                   <div class="container">
                    <a  href="View.php?id=3">
                        <div class="imgCont" load-image="/IndexImgs/wilds.png" id="recY">
                        </div>
                    <div class='titling'>Adventurer's Guide to the Wilderness</div></a>    </div>

                   <div class="container">
                    <a  href="View.php?id=8">
                        <div class="imgCont" load-image="/IndexImgs/Dark.png" id="recZ">
                        </div>
                    <div class='titling premium'>Handbook of Dark Secrets</div></a>    </div>

                   <div class="container" id="a">
                   <a  href="View.php?id=33">
                        <div class="imgCont" load-image="/IndexImgs/Illithid.png" id="recA">
                        </div>
                    <div class='titling'>Psion</div> </a>   </div>

                   <div class="container" id="b">
                   <a  href="View.php?id=41">
                        <div class="imgCont" load-image="/IndexImgs/EpcChar.png" id="recB">
                        </div>
                    <div class='titling'>Epic Characters</div> </a>   </div>

                   <div class="container" id="c">
                   <a  href="View.php?id=40">
                        <div class="imgCont" load-image="/IndexImgs/ShortHist.png" id="recC">
                        </div>
                    <div class='titling'>Short History of the Many Isles</div> </a>   </div>

                   <div class="container" id="d">
                   <a  href="View.php?id=27">
                        <div class="imgCont" load-image="/IndexImgs/Spectre.png" id="recD">
                        </div>
                    <div class='titling premium'>Spectre of Revolution</div> </a>   </div>


                   <div class="container" id="e">
                   <a  href="View.php?id=36">
                        <div class="imgCont" load-image="/IndexImgs/arc.png" id="recE">
                        </div>
                    <div class='titling'>Arcane Traditions</div> </a>   </div>
        </div>


        <div class="digCont">

                <h1>Tools</h1>
        <?php
        // Block for Diggie Search

        $nume3 = 0;
        $searchstring = "SELECT max(id) FROM diggies";
        $indexid = "whoppee";

        if ($max = $conn->query($searchstring)) {
        while ($gay = $max->fetch_row()){
        $indexid = $gay[0];
        if ($indexid != null){
            for ($x = 0; $x>=0; $x++) {
            $currentsearch = $indexid - $x;
            if (checkStat($currentsearch, "diggies") == false){continue;};
            $currsearch = sprintf("SELECT * FROM diggies WHERE id = %s ", $currentsearch);
            $toprow = $conn->query($currsearch);
            while ($row = $toprow->fetch_assoc()) {
                     $titling = $row["name"];
                     if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
                     $image = $row["image"];
                     $link = $row["id"];
                     $premium = false;
                     if ($row["tiers"]!="g"){$premium = true;}
                     makeFirstProdTab($producttab, $titling, $image, $link, $premium, "d");
            }
            if ($currentsearch==1){ break;}
            if ($nume3==8){ break;}
         }
        }
        }}

        ?>

        </div>

        <div class="digCont">

                <h1>Art</h1>
        <?php
        // Block for art Search

        $nume4 = 0;
        $searchstring = "SELECT max(id) FROM art";
        $indexid = "whoppee";

        if ($max = $conn->query($searchstring)) {
        while ($gay = $max->fetch_row()){
        $indexid = $gay[0];
        if ($indexid != null){
            for ($x = 0; $x>=0; $x++) {
            $currentsearch = $indexid - $x;
            if (checkStat($currentsearch, "art") == false){continue;};
            $currsearch = sprintf("SELECT * FROM art WHERE id = %s ", $currentsearch);
            $toprow = $conn->query($currsearch);
            while ($row = $toprow->fetch_assoc()) {
                     $titling = $row["name"];
                     if($row["displaytitle"] != null){$titling = $row["displaytitle"];}
                     $image = $row["image"];
                     $link = $row["id"];
                     $premium = false;
                     if ($row["tiers"]!="g"){$premium = true;}
                     makeFirstProdTab($producttab, $titling, $image, $link, $premium, "a");
            }
            if ($currentsearch==1){ break;}
            if ($nume4==8){ break;}
         }
        }
        }}

        ?>



        </div>
    </div>
</div>

    <div class="bottomad-container">
        <div class="bottomad">
            <img src="global/plus.png" alt="hi" />
            <a href="/account/BePartner.php">
                Publish your own!
            </a>
        </div>
    </div>


        <div class="footer" include-html="global/Gfooter.html">
</div>
</div>



</body>
</html>
<script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script src="global/dl2v2.js"></script>

