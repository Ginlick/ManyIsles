<?php
if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:/dl/partner?id=1");}
$pId = $_GET["id"];

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once("global/engine.php");
$dl = new dlengine($conn);

$query = "SELECT * FROM partners WHERE id = $pId";
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $name = $row["name"];
      $image = $row["image"];
      $jacob = $row["jacob"];
      $status = $row["status"];
      $regDate = $row["reg_date"];
      $type = $row["type"];
    }
}
$pType = "Companionship";
if ($type == "prem"){$pType = "Full Partnership";}
$date_array = date_parse($regDate);
$pRegDate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];

$totalPub = 0;
$query = "SELECT COUNT(IF (partner = $pId, 1, NULL)) FROM products";
if ($firstrow = $dl->dlconn->query($query)){
  while ($row = $firstrow->fetch_row()) {
    $totalPub = $row[0];
  }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <link rel="icon" href="../Imgs/Favicon.png">
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
    <title><?php echo $name ?> | Digital Library</title>
    <?php echo $dl->styles(); ?>
</head>
<style>
.flexer {text-align: center;}
.imageShower {
  width: 37%;
}

</style>
<body>

  <?php
      echo $dl->giveGlobs();
  ?>
      <div class="flex-container">
        <div class='left-col'>
            <ul class="myMenu">
                <li><a class="Bar" href="/home"><i class="fas fa-arrow-left"></i> Home</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>

            <?php
                echo $dl->giveMenu();
            ?>

            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/docs/11/Digital_Library" target="_blank">Digital Library FAQ</a></li>
            </ul>
        </div>

        <div class='column'>
          <section class="pInfoBlock">
            <?php echo $dl->giveAccTab(); ?>
            <div class="crumbs"><a href="/dl/home">Digital Library</a> - <a href="/docs/4/Partnership%20Program" target="_blank">Partners</a> - <?php echo $name; ?></div>
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
                            Id: p#<?php echo $pId; ?><br>
                            Library Publications: <?php echo $totalPub; ?>
                        </p>
                    </div>
                    <div class="overtail normal">
                        <p>
                            <?php echo $jacob;  ?>
                        </p>
                    </div>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://manyisles.ch/dl/partner?id=<?php echo $pId; ?>" target="_blank" class="fa fa-facebook"></a>
                    <a href="http://www.reddit.com/submit?title=Check out <?php echo $name; ?>'s stuff on the Many Isles!&url=https://manyisles.ch/dl/partner?id=<?php echo $pId; ?>" target="_blank" class="fa fa-reddit"></a>
                    <a href="https://twitter.com/intent/tweet?text=Check out <?php echo $name; ?>'s stuff on the Many Isles!%0A&url=https://manyisles.ch/dl/partner?id=<?php echo $pId; ?>&hashtags=manyisles,dnd" target="_blank" class="fa fa-twitter"></a>
                    <a href="http://pinterest.com/pin/create/button/?url=https://manyisles.ch/dl/partner?id=<?php echo $pId; ?>&media=<?php echo $image; ?>&description=Check out <?php echo $name; ?>'s stuff on the Many Isles!" target="_blank" class="fa fa-pinterest"></a>
                </section>


            </div>


                <h2>Discover</h2>
                <div class="itemRow single">
                    <?php
                      echo $dl->results(["partner"=>$pId, "method"=>"RAND()"], "row", 9);
                    ?>
                </div>

                <?php
                  foreach ($dl->typeNames as $key => $genrename){
                    $result = $dl->results(["partner"=>$pId,"genre"=>$key,"method"=>"popularity"], "row", 9);
                    if ($result != "Hmmm... there aren't many great results."){
                      echo "<h2>$genrename by this Partner</h2>";
                      echo "<div class='itemRow single'>";
                      echo $result;
                      echo "</div>";
                    }
                  }
                 ?>


        </div>
    </div>

    <div w3-include-html="/ds/g/GFooter.html" w3-create-newEl="true"></div>

    <div class="bottomad-container">
        <div class="bottomad">
            <img src="global/plus.png" alt="hi" />
            <a href="/account/BePartner.php">
                Become Partner!
            </a>
        </div>
    </div>
</div>



</body>
</html>
<?php echo $dl->baseVars(); ?>
<?php echo $dl->scripts(); ?>
<script>

</script>
