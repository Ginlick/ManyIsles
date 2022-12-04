<?php
if (isset($_GET["id"])){if (preg_match("/^[0-9]+$/", $_GET["id"])!=1){header("Location: /dl/home");exit();} else {$artId = $_GET["id"];} } else {header("Location: /dl/home");exit();}
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/Parsedown.php");
require_once("global/engine.php");
$dl = new dlengine($conn);
if (!$dl->user->check()){$dl->go("home", "dl");}
$parse= new parseDown();
$parse->setSafeMode(true);

$digital = false;
$query = "SELECT * FROM products WHERE id = $artId";
if ($firstrow = $dl->dlconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()){
        $artName = $row["name"];
        $artShortName = $row["shortName"]; if ($artShortName == ""){$artShortName = $artName;}
        $artPartnerId = $row["partner"];
        $artImage = $dl->clearmage($row["image"]);
        $artGenre = $row["genre"];
        $artSubgenre = $row["subgenre"];
        $artTier = $row["tier"];
        $artDesc = $row["description"];
        $artLink = $row["link"];
        $artDl = $row["downloads"];
        $artSupp = $row["support"];
        $artDetails = json_decode($row["more"], true);
        $artGsystem = 0;
        $artRegDate = $row["reg_date"];
        $artStatus = $row["status"];
    }
}
$artType = $dl->typeDets[$artGenre]["type"];
if ($artName==""){$dl->go("home?i=baditem", "dl");}
else if ($dl->partners[$artPartnerId]!="active"){$dl->go("home?i=badpartner", "dl");}
else if ($artStatus != "active" AND $artStatus != "paused"){$dl->go("home?i=baditem", "dl");}
$canPeruse = true;
if ($artTier > $dl->user->tier){$canPeruse = false;}
$tierMsg = "Tier $artTier"; if ($artTier == 0){$tierMsg = "Free";}
$date_array = date_parse($artRegDate);
$artPubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];
if (isset($artDetails["gsystem"])) {$artGsystem = $artDetails["gsystem"];}

$query = "SELECT * FROM partners WHERE id = $artPartnerId";
if ($firstrow = $dl->conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()){
        $artPartner = $row["name"];
    }
}
$isPartner = false;

if ($dl->partner(false)) {if ($dl->partId == $artPartnerId){$isPartner = true;}}

//access ways
$access = "Download";
if (isset($artDetails["indirect"]) AND $artDetails["indirect"]==1){
  $access = "View";
}
else if ($artGenre == 2) {
  $access = "View";
}
function replaceBusiness($template, $fasfa, $leadme, $target = "target='_blank'") {
  $template = str_replace("FASFAREREY", $fasfa, $template);
  $template = str_replace("LEADMETO", $leadme, $template);
  $template = str_replace("GIMMETARGET", $target, $template);
  return $template;
}
$overlay = <<<AAA
      <div class="overlay">
        <a href="LEADMETO" GIMMETARGET>
             <span class="viewOverlay"><i class="FASFAREREY"></i></span>
             ACCESSMODENAME
        </a>
      </div>
 AAA;
 $checkoutPair = <<<BBB
       <div class="checkoutBox">
        <a href="LEADMETO" GIMMETARGET>
           <button class="checkout">
               <i class="FASFAREREY"></i> ACCESSMODENAME
           </button>
         </a>
         BUTTWO
       </div>
BBB;
$overlay = str_replace("ACCESSMODENAME", $access, $overlay);
$checkoutPair = str_replace("ACCESSMODENAME", $access, $checkoutPair);
if ($canPeruse){
 if ($access == "Download"){
   if ($artType=="dlPdf" OR $artType=="dlArt" OR $artType=="bigAudio") {
     $checkoutPair = str_replace("BUTTWO", " <a href='".$dl->fileclear($artLink, $artGenre, true)."' target='_blank'><button class='checkout'> <i class='fas fa-arrow-up'></i> Open in Browser</button></a>",   $checkoutPair);
   }
   else {
     $checkoutPair = str_replace("BUTTWO", "",   $checkoutPair);
   }
   $overlay = replaceBusiness($overlay, "fas fa-arrow-down", "/Server-Side/downStuff.php?name=".urlencode($artShortName)."&dlid=".$artId."&dl=".$dl->fileclear($artLink, $artGenre), "download='' target='_blank'");
   $checkoutPair = replaceBusiness($checkoutPair, "fas fa-arrow-down", "/Server-Side/downStuff.php?name=".urlencode($artShortName)."&dlid=".$artId."&dl=".$dl->fileclear($artLink, $artGenre), "download='' target='_blank'");
 }
 else {
   $checkoutPair = str_replace("BUTTWO", "",   $checkoutPair);
   $overlay = replaceBusiness($overlay, "fas fa-arrow-up", $dl->fileclear($artLink, $artGenre));
   $checkoutPair = replaceBusiness($checkoutPair, "fas fa-arrow-up", $dl->fileclear($artLink, $artGenre));
 }
}
else {
  $checkoutPair = "<div class='checkoutBox'><a href='LEADMETO' target='_blank'> <button class='checkout'> <i class='fas fa-arrow-right'></i> Purchase Tier</button></a></div>";
  $overlay = replaceBusiness($overlay, "fas fa-times", "/ds/tiers?i=".($artTier - 1));
  $overlay = str_replace($access, $tierMsg, $overlay);
  $checkoutPair = replaceBusiness($checkoutPair, "fas fa-times", "/ds/tiers?i=".($artTier - 1));
}

//tracker
$query = "UPDATE products SET popularity = popularity + 1 WHERE id = $artId";
$dl->dlconn->query($query);

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
    <title><?php echo $artShortName; ?> | Digital Library</title>
    <?php echo $dl->styles(); ?>
</head>
<style>
    .details {
      max-height: 300px;
      overflow: hidden;
      position : relative;
    }

    .details:after {
      content  : "";
      position : absolute;
      z-index  : 1;
      bottom   : 0;
      left     : 0;
      pointer-events   : none;
      background-image : linear-gradient(to bottom,
                        rgba(255,255,255, 0),
                        rgba(255,255,255, 1) 90%);
      width    : 100%;
      height   : 100px;
    }
    .details.full {
      max-height: none;
    }
    .details.full:after {
      display: none;
    }
    .viewMore {
      margin-top: 0;
      text-align: left;
    }
    .lined {
      border-bottom: 1px var(--all-color-albord) solid;
    }
</style>
<body>
  <?php
      echo $dl->giveGlobs();
  ?>
      <div class="flex-container">
        <div class='left-col'>
            <ul class="myMenu">
                <li><a class="Bar" href="/dl/home"><i class="fas fa-arrow-left"></i> Library</a></li>
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
            <?php echo $dl->giveAccTab($dl->url($artId, $artShortName)); ?>
            <div class="crumbs"><a href="/dl/home">Digital Library</a> - <?php echo $dl->typeNames[$artGenre]; ?> - <?php echo $artShortName; ?></div>
            <div class="flexer">
                <section class="imageShower">
                    <div class="squareCont">
                        <div class="square slideshow-container">
                            <?php
                                echo '
                                    <div class="mySlides fade">
                                        <img src="'.$artImage.'">
                                    </div>
                                ';
                            ?>
                        </div>
                        <?php echo $overlay; ?>
                    </div>
                </section>

                <section class="rightOvertails">
                    <div class="overtail">
                        <h1><?php echo $artName; ?></h1>
                        <?php
                        echo '<div class="tabs-row">';
                        foreach (str_split($artSubgenre) as $subg){
                          if (isset($dl->subgenresArr[$artGenre][$subg])){
                            echo "<div class='tab'>".$dl->subgenresArr[$artGenre][$subg]."</div>";
                          }
                        }
                        echo "</div>";
                        ?>
                        <p>
                            By <?php echo '<a href="/dl/partner?id='.$artPartnerId.'">'.$dl->parsePartName($artPartner).'</a>'; ?><br />
                            Published <?php echo $artPubdate; ?> <br>
                            <?php if (isset($artDetails["format"] )) {echo $artDetails["format"]; } ?>
                        </p>
                        <?php
                          if ($canPeruse){
                            echo "<p class='iShipping green'><i class='fas fa-check'></i> $tierMsg</p>";
                          }
                          else {
                            echo "<p class='iShipping premium'><i class='fas fa-times'></i> $tierMsg required</p>";
                          }
                          if ($artDl > 4){
                            echo "<p>Downloaded $artDl times</p>";
                          }
                        ?>
                    </div>
                    <?php if ($artSupp == 1){
                      echo <<<MAEUAIJO
                        <div class="overtail support">
                          <form action="/ds/basket.php" method="POST" enctype="multipart/form-data">

                          <label for="creditAmount"><i class="fas fa-coins"></i> Support Partner</label>
                          <div class="supportGroup"><input type="text" id="creditAmount" name="nope" placeholder="10.50" pattern="^[0-9\.]*$" oninput="inputGramm(this)" required>
                          <button onclick="submitNormal()" class="checkout"><i class="fas fa-arrow-right"></i> Support</button></div>

                          <input style="display:none" name="basketing" id="basketing" value="3" />
                          <input style="display:none" name="supportPair" id="supportPair" value="" />

                          </form>
                        </div>
                      MAEUAIJO;
                    }
                    ?>
                    <div class="overtail">
                      <?php
                      echo $checkoutPair;
                      if ($isPartner) {
                        echo '
                        <a href="/account/Product?id='.$artId.'" >
                           <button class="checkout">
                               <i class="fas fa-arrow-right"></i> Edit
                           </button>
                         </a>';
                      }
                      if ($artStatus == "paused"){
                        echo '<p class="warning blue">This product is paused and cannot currently be found in the library.</p>';
                      }
                      else if ($artPartner != "Pantheon" AND $access == "Download" AND $canPeruse){
                        echo '<p class="warning">The Many Isles bear no responsibility for possible malware in third-party products.</p>';
                      }
                       ?>
                    </div>

                    <div>
                      <a href="https://www.facebook.com/sharer/sharer.php?u=https://manyisles.ch<?php echo $dl->url($artId, $artShortName); ?>" target="_blank" class="fa fa-facebook"></a>
                      <a href="http://www.reddit.com/submit?title=Check out the <?php echo $artName; ?> on the Many Isles!&url=https://manyisles.ch<?php echo $dl->url($artId, $artShortName); ?>" target="_blank" class="fa fa-reddit"></a>
                      <a href="https://twitter.com/intent/tweet?text=Check out the awesome <?php echo $artName; ?> on the Many Isles!%0A&url=https://manyisles.ch<?php echo $dl->url($artId, $artShortName); ?>&hashtags=manyisles,dnd" target="_blank" class="fa fa-twitter"></a>
                      <a href="http://pinterest.com/pin/create/button/?url=https://<?php echo $_SERVER["HTTP_HOST"].$dl->url($artId, $artShortName); ?>&media=<?php echo $artImage; ?>&description=Check out the awesome <?php echo $artName; ?> on the Many Isles!" target="_blank" class="fa fa-pinterest"></a>
                      <a class="fa fa-link fancyjump" onclick="navigator.clipboard.writeText('https://<?php echo $_SERVER["HTTP_HOST"].$dl->url($artId, $artShortName); ?>');createPopup('d:gen;txt:Link copied!');"></a>
                    </div>
                </section>
            </div>

            <section class="lined">
              <div  class="details" id="detailsBlock">
                <div>
                        <?php if ($artGsystem != 0){echo "<p>Game System: ".$dl->gsystArr[$artGsystem]."</p>";}
                        echo txtUnparse($parse->text($artDesc)); ?>
                </div>
              </div>
              <p class="viewMore fakelink" id="viewMore" onclick="toggleView()">
                More
              </p>
            </section>
            <section>
              <?php
              if ( $artPartner != "Pantheon" and $artPartner != "Traveler"){
                $results = $dl->results(["partner"=>$artPartnerId, "method"=>"RAND()"], "row", 9, [$artId]);
                if ($results != "Hmmm... there aren't many great results."){
                  echo "<a href='/dl/partner?id=".$artPartnerId."'><h2 >More by This Partner</h2></a>";
                  echo "<div class='itemRow single'>";
                  echo $results;
                  echo "</div>";
                }
              }
               ?>
              <h2>Similar Products</h2>
              <div class="itemRow single">
                <?php
                  echo $dl->results(["genre"=>$artGenre, "subgenre"=>$artSubgenre, "gsystem"=>$artGsystem], "row", 8, [$artId]);
                 ?>
              </div>
            </section>

        </div>
    </div>
    <div w3-include-html="/ds/g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<?php
  $subgenre2 = "[";
  foreach (str_split($artSubgenre) as $subg){
    $subgenre2 .= "'$subg',";
  }
  $subgenre2 .= "]";
  echo $dl->baseVars($artGenre, $subgenre2, $artGsystem);
?>
<?php echo $dl->scripts(); ?>
<script>
function submitNormal() {
    if (document.getElementById("creditAmount").value != ""){
    let sponsoring = parseFloat(document.getElementById("creditAmount").value);
        sponsoring = sponsoring * 100.0;
        sponsoring = sponsoring.toFixed(0);
        document.getElementById("supportPair").value = "(<?php echo $artPartner; ?>/"+sponsoring+")";
        document.getElementById("coolForm").submit();
    }
}
function toggleView(force = false) {
  if (document.getElementById("detailsBlock").classList.contains("full") && !force){
    document.getElementById("detailsBlock").classList.remove("full");
    document.getElementById("viewMore").innerHTML = "More";
  }
  else {
    document.getElementById("detailsBlock").classList.add("full");
    document.getElementById("viewMore").innerHTML = "Less";
  }
}
if (document.getElementById("detailsBlock").clientHeight < 300){
  toggleView(true);
  document.getElementById("viewMore").style.display = "none";
}
</script>
