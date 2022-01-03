<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
$dl = new dlengine();
$dl->partner();

?>
<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
    <?php echo $dl->styles("p"); ?>
    <title>Partnership</title>
<style>
.procol {
text-align:center;
display:block;
padding:0;
}
  .dsButton {
    background-color: #d1a720;
    border-radius: 10px;
    padding: 9px;
    font-size: calc(14px + 0.5vw);
    font-weight: normal;
    display: inline;
    margin: 0 10px 40px;
    color: white;
}

    .dsButton:hover {
        background-color: #f0c026;
        transition: .2s ease;
        cursor: pointer;
    }
</style>
</head>
<body>
  <?php
      echo $dl->giveGlobs();
  ?>

  <div class="flex-container">
      <div class='left-col'>
          <h1 class="menutitle">Partnership</h1>
          <ul class="myMenu">
              <li><a class="Bar" href="SignedIn"><i class="fas fa-arrow-left"></i> Account</a></li>
          </ul>
          <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
          <ul class="myMenu bottomFAQ">
            <li><a class="Bar" href="/docs/4/Partnerships" target="_blank">Partnership Program</a></li>
            <li><a class="Bar" href="/docs/59/Partnership_Types" target="_blank">Partnership Extensions</a></li>
          </ul>
      </div>

      <div class='column'>
        <?php
        echo $dl->giveAccTab();
         ?>
      <h1><?php echo $dl->partName; ?>, by <?php echo $dl->user->uname; ?></h1>
      <?php

      if ($dl->ppower>0) {echo "<div><img src='/Imgs/Ranks/HighMerchant.png' alt:'Oopsie!' class='bannerI'></div>";}
      else {echo "<div><img src='/Imgs/Ranks/Trader.png' alt:'Oopsie!' class='bannerI'></div>";}
      ?>
      <p>Your awesome work.</p>
      <?php
      if ($dl->partStat == "suspended"){
          echo '
       <div class="contentBlock">
          <h1>Partnership Suspended</h1>
          <p>The Homeland Institute of Trade temporarily suspended your partnership. If you have any questions, please contact <a href="mailto:godsofmanyisles@gmail.com">godsofmanyisles@gmail.com</a>.<br>
          A number of publishing features will not work until your account is reactivated.    </p>
      </div>
      ';
      }

      ?>

      <div class="contentBlock">
        <h1>Published Products</h1>
    <?php
    if ($dl->totalPub > 0){
      echo '
          <p>Edit your products here.</p>';
      $query = 'SELECT * FROM products WHERE status != "deleted" AND partner = '.$dl->partId.' order by name ASC';
      if ($firstrow = $dl->dlconn->query($query)) {
          while ($row = $firstrow->fetch_assoc()) {
            $column = "<a class='procol' href='%%URL'>%%MEHA (%%GENRE)</a>";
            $column = str_replace("%%URL", "Product?id=".$row["id"], $column);
            $column = str_replace("%%MEHA", $row["name"], $column);
            if ($dl->ppower > 0 && $row["tier"] != 0){$column = str_replace("%%GENRE", "Tier ".$row["tier"].", ".$dl->typeNames[$row["genre"]], $column);}
            else {$column = str_replace("%%GENRE", $dl->typeNames[$row["genre"]], $column);}
          echo $column;
        }
      }
    }
    else {
      echo "<p>Nothing published yet.</p>";
    }
    ?>
    <a href="Product"><button style="margin-top: 30px;"><i class="fas fa-plus"></i> Publish New</button></a>
  </div>


    <div class="contentBlock">
      <h1>Your <?php echo $dl->pType; ?> p#<?php echo $dl->partId; ?></h1>
      <p>This is publically visible information about your partnership. <a href="/dl/partner?id=<?php echo $dl->partId; ?>" target="_blank">View Page</a>
      <form action="SubPar.php" method="POST" class="stanForm" enctype="multipart/form-data">
        <section class="duel">
          <section class="imageShower">
            <div class="squareCont">
                <div class="square">
                  <div class="mySlides fade">
                      <img src="<?php echo $dl->clearmage($dl->partImage); ?>" class="file-upload-image">
                  </div>
                </div>
                <div class="overlay content">
                    <span class="viewOverlay"><i class="fas fa-arrow-up"></i></span>
                    .png or .jpg<br>max 250kb
                    <input type="file" onchange="readURL(this);" id="image" value="null" name = "image" accept=".png, .jpg"/>
                </div>
            </div>
          </section>
          <div class="inputCont sideText">
              <label for="pname">Partnership Name <span>*</span> </label>
              <input type ="text" name="pname"  placeholder="Hansfried's Guildshop." pattern="[A-Za-z0-9\'\- ]{2,22}" value="<?php echo $dl->partName; ?>" required />
              <p class="inputErr info" default="Only letters, numbers, ' and - . Change this as rarely as possible."></p>
          </div>
        </section>
        <div class="inputCont">
            <label for="jacob">Description <span>*</span> </label>
            <textarea name="jacob" rows = "10" class="textBlock" placeholder="I make great lore for the ravenous orcs of northern Balebu and Intralu."  required><?php echo $dl->partDesc; ?></textarea>
            <p class="inputErr info" default="A cool description of your partnership."></p>
        </div>

        <button><i class='fas fa-arrow-right'></i> Submit</button>
    </form>
    </div>

    <?php
    if ($dl->partDS){
        echo '
     <div class="contentBlock">
        <h1>Digital Store Hub</h1>
        <p>Manage your Digital Store items and orders.</p>
    <a href="/ds/p/hub.php"><button class="dsButton"><i class="fas fa-arrow-right"></i> View Hub</button></a>
    </div>
    ';
    }
    else {
        echo '
     <div class="contentBlock">
        <h1>Digital Store Extension</h1>
        <p>Activate the Digital Store extension to start publishing in the digital store.<br>Note that this cannot be undone. <a href="/docs/18/Digital_Store_Extension" target="_blank">More info</a></p>
        <a href="/ds/p/activate.php"><button class="dsButton"><i class="fas fa-arrow-right"></i> Activate</button></a>
    </div>
    ';
    }

    if ($dl->ppower == 0) {
        echo '
     <div class="contentBlock">
        <h1>Premium Partnership Extension</h1>
        <p>Request activation of the Premium Partnership extension to publish tiered products. <a href="/docs/63/Premium_Extension" target="_blank">More info</a><br>
        You should have at least one published product to be cleared.</p>
        <a href="prem/request.php"><button><i class="fas fa-arrow-right"></i> Request Activation</button></a>
    </div>
    ';
    }
    else {
      echo '
     <div class="contentBlock">
        <h1>Premium Partnership </h1>
        <p>Your partnership has an active Premium extension. <a href="/docs/63/Premium_Extension" target="_blank">More info</a></p>
        <a href="prem/hub"><button><i class="fas fa-arrow-right"></i> More</button></a>
    </div>
    ';
    }
    ?>

  <!--  <div class="contentBlock">
      <h1>Delete Partnership</h1>
      <form action=""
    </div> -->

  </div>
</div>
<?php
echo $dl->giveFooter();
?>
</body>
</html>
<?php
  echo $dl->scripts("p");
 ?><script>

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('.file-upload-image').attr('src', e.target.result);
        document.getElementById("rpInput").style.display="block";
    };
    reader.readAsDataURL(input.files[0]);
  }
}

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "created") {
  createPopup("d:pub;txt:Companionship created!");
}
else if (why == "pcreated") {
  createPopup("d:pub;txt:Product published!");
}
else if (why == "updated") {
  createPopup("d:pub;txt:Partnership info updated");
}
else if (why == "badmage") {
  createPopup("d:pub;txt:Image could not be uploaded.");
}
else if (why == "proddel") {
  createPopup("d:pub;txt:Product deleted.");
}
else if (why == "requ") {
  createPopup("d:pub;txt:Request submitted.");
}
else if (why == "notrequ") {
  createPopup("d:pub;txt:Error. Could not submit request.");
}
else if (why == "405") {
  createPopup("d:pub;txt:Error. You aren't allowed to do that.");
}
</script>
