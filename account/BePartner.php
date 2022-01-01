<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();
if ($dl->partner(false)) {$dl->go("Publish", "p");}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
    <?php echo $dl->styles("p"); ?>
    <title>Become Partner | Partnership</title>
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
              <li><a class="Bar" href="/docs/60/Publishing_Terms" target="_blank">Publishing Terms</a></li>
              <li><a class="Bar" href="/docs/4/Partnerships" target="_blank">Partnership Program</a></li>
            </ul>
        </div>

        <div class='column'>
                <?php
                    echo $dl->giveAccTab();

                      if (!$dl->user->emailConfirmed) {
                        setcookie("seeker", "/account/BePartner?w=clear", time() + 222000, "/");

                        echo "<h1>Confirm Email</h1>
                        <img src='/Imgs/Recruit.png' alt:'Oopsie!' class='bannerI' />
                        <p>Confirm your email to become a trader!<br></p>
                        <a href='resendConfirm.php?id=41'><button class='popupButton'><i class='fas fa-arrow-right'></i> Resend</button></a>
                        <p>You will be redirected back from the confirmation page.</p>
                        </div></div>
                        </div>
                        </div>
                        ";
                        echo $dl->giveFooter();
                        echo $dl->scripts("p");
                        exit();
                      }
                ?>

                <h1>Let's make you a Trader!</h1>
                <div><img src='/Imgs/Ranks/HighMerchant.png' alt:'Oopsie!' class="bannerI"></div>
                <p>
                  With a <a href="/docs/59/Partnership_Types?v=1" target="_blank">companionship</a>, you'll be able to publish content in the digital library - for free. You'll also glean the Trader <a href="/docs/10/Tiers%20and%20Titles" target="_blank">title</a>.
                </p>


                <form action="SubPar.php" method="POST" class="stanForm" enctype="multipart/form-data">
                  <section class="duel">
                    <section class="imageShower">
                      <div class="squareCont">
                          <div class="square">
                            <div class="mySlides fade">
                                <img src="/dl/PartIm/Traveler.png" class="file-upload-image">
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
                        <input type ="text" name="pname"  placeholder="Hansfried's Guildshop." pattern="[A-Za-z0-9\'\- ]{2,22}" required />
                        <p class="inputErr info" default="Only letters, numbers, ' and - ."></p>
                    </div>
                  </section>
                  <div class="inputCont">
                      <label for="jacob">Description <span>*</span> </label>
                      <textarea name="jacob" rows = "10" class="textBlock" placeholder="I make great lore for the ravenous orcs of northern Balebu and Intralu."  required></textarea>
                      <p class="inputErr info" default="A cool description of your partnership."></p>
                  </div>

                <button><i class='fas fa-arrow-right'></i> Submit</button>
        </div>
    </div>
<?php
  echo $dl->giveFooter();
?>


</body>
</html>
<?php
  echo $dl->scripts("p");
 ?>
 <script>
function next() {
            document.getElementById("modal").style.display = "block";
            document.getElementById("mod").style.display = "block";
}
function doPops() {
            document.getElementById("modal").style.display = "none";
            document.getElementById("mod").style.display = "none";
}

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why == "wrongTitle"){
    createPopup("d:pub;txt:There was an arror.");
}
else if (why == "badimg"){
    createPopup("d:pub;txt:There was an error uploading your image.");
}

function readURL(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {

      $('.file-upload-image').attr('src', e.target.result);

    };

    reader.readAsDataURL(input.files[0]);

  }
}
</script>
