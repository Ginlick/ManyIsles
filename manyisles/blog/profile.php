<?php
$targetBuser = 0;
if (isset($_GET["u"])){$targetBuser = preg_replace("/[^0-9]/", "", $_GET['u']);}

require_once("g/blogEngine.php");
$blog = new blogEngine("Profile");
$blog->fetchBuserId();
$isTargetBuser = false;
if ($targetBuser == 0){
  $targetBuser = $blog->buserId;
}
if ($blog->hasProfile($targetBuser)){
  $blog->userCheck();
  $isTargetBuser = true;
}
if ($isTargetBuser){
  $blog->isPartnerVersion($targetBuser);
}
$targetBuserInfo = $blog->fetchBuserInfo($targetBuser);
$targetBuserPosts = $blog->fetchPostNum($targetBuser);
$tbp = $targetBuserPosts." post"; if ($targetBuserPosts != 1){$tbp .= "s";}
$flws = "<span id='followNum'>".$targetBuserInfo["followNum"]."</span> follower"; if ($targetBuserInfo["followNum"] != 1){$flws .= "s";}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title> Profile | Blogs</title>
    <?php echo $blog->styles(); ?>
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
</head>
<style>
.left-col {
  position:sticky;
  top:0;
}
.profileBlock {
  border-bottom: var(--blog-standardborder);
}

</style>
<body>
    <?php echo $blog->giveTopnav(); ?>
    <div class="flex-container">
        <div class='left-col'>
            <?php echo $blog->giveLeftcol(); ?>
        </div>
        <div class='column'>
          <div class="columnCont">
              <?php echo $blog->giveSignPrompt("/blog/profile?u=".$targetBuser); ?>
              <section class="profileBlock">
                <section class="topinfo">
                  <section class="imageShower">
                      <div class="squareCont">
                          <div class="circle <?php if ($targetBuserInfo["info"]["pptype"]=="round"){echo "circle-rounding";}?>">
                                  <img src="<?php echo $targetBuserInfo["info"]["pp"]; ?>">
                          </div>
                      </div>
                  </section>
                  <div class="rightsquare">
                    <p class="mainname"><?php echo $targetBuserInfo["info"]["uname"];?></p>
                    <p class="secondname"><?php echo $targetBuserInfo["username"];?> (<?php echo $targetBuserInfo["userFullid"];?>)</p>
                    <?php if ($targetBuserInfo["type"]=="adventurer" AND $targetBuserInfo["info"]["setShowDiscord"]==1 AND $targetBuserInfo["userDiscname"]!="") {echo '<p class="secondname">Discord: '.$targetBuserInfo["userDiscname"].'</p>'; }?>
                    <div class="blogSharerCont">
                      <a href="http://www.reddit.com/submit?title=Check out <?php echo $targetBuserInfo["info"]["uname"]; ?>'s posts on the Many Isles!&url=https://manyisles.ch/blog/profile%3Fu%3D<?php echo $targetBuser; ?>" target="_blank" class="fa fa-reddit"></a>
                      <a href="https://twitter.com/intent/tweet?text=Check out <?php echo $targetBuserInfo["info"]["uname"]; ?>'s posts on the Many Isles!%0A&url=https://manyisles.ch/blog/profile?u=<?php echo $targetBuser; ?>&hashtags=manyisles" target="_blank" class="fa fa-twitter"></a>
                      <a href="http://pinterest.com/pin/create/button/?url=https://manyisles.ch/blog/profile?u=<?php echo $targetBuser; ?>&media=<?php echo "https://manyisles.ch".$targetBuserInfo["info"]["pp"]; ?>&description=Check out <?php echo $targetBuserInfo["info"]["uname"]; ?>'s posts on the Many Isles!" target="_blank" class="fa fa-pinterest"></a>
                      <a class="fa fa-link fancyjump" onclick="navigator.clipboard.writeText('https://manyisles.ch/blog/profile?u=<?php echo $targetBuser; ?>');createPopup('d:gen;txt:Link copied!');"></a>
                    </div>
                  </div>
                  <div class="followsquare">
                    <div class="blogButton" id="followButton" onclick="toggleFollow(this)">Follow</div>
                  </div>
                </section>
                <div class="description">
                  <p class="secondname">
                    <?php echo $tbp.", ".$flws; ?>
                  </p>
                  <?php echo $blog->parse->parse($targetBuserInfo["info"]["description"], 1);?>
                </div>
                <?php
                  if ($isTargetBuser) {
                    echo '<div class="submitBlocc"><a href="/blog/profileEdit?'.$blog->profileInset.'"><div class="blogButton">Edit Profile</div></a></div>';
                  }
                ?>
              </section>
              <h2>Posts</h2>
              <?php echo $blog->genSortCont("profile-feed"); ?>
              <section class="feed"  blog-feed="" blog-feed-user="<?php echo $targetBuser; ?>" id="profile-feed">
              </section>
          </div>
        </div>
    </div>
    <?php echo $blog->giveFooter(); ?>

</div>



</body>
</html>
<?php echo $blog->scripts(); ?>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "pubbed"){
    createPopup("d:gen;txt:Post successfully published!");
}
else if (why == "pSetup"){
    createPopup("d:pub;txt:Partnership extension activated!");
}

function buttonUnfollows(button) {
  button.classList.add("grey");
  button.innerHTML = "Unfollow";
}
function toggleFollow(button) {
  var follow = 1;
  var followNum = parseInt(document.getElementById("followNum").innerHTML);
  if (button.classList.contains("grey")){
    button.classList.remove("grey");
    button.innerHTML = "Follow";
    followNum--;
    follow = 0;
  }
  else {
    buttonUnfollows(button);
    followNum++;
  }
  document.getElementById("followNum").innerHTML = followNum;
  let file = "/blog/g/follow.php?d="+follow+"&u="+<?php echo $targetBuser; ?>;
  if (file) {
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
          if (this.readyState == 4) {
            console.log(this.responseText);
          }
      }
      xhttp.open("GET", file, true);
      xhttp.send();
  }
}
<?php
if (in_array($blog->buserId, $targetBuserInfo["followers"])) {
  echo "
  let button = document.getElementById('followButton');
  buttonUnfollows(button);
  ";
}
?>

</script>
