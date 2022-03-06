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
if ($targetBuser==$blog->buserId){//later replace with $blog->isUsers($targetBuser)
  $blog->userCheck();
  $isTargetBuser = true;
}
$targetBuserInfo = $blog->fetchBuserInfo($targetBuser);


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
.topinfo {
  padding-top: 80px;
  display: flex;
  flex-direction: row;
}
  .imageShower {
    width: 30%;
  }
.rightsquare, .description {
  padding: 20px;
}
.rightsquare .mainname {
  font-size: var(--all-fonts-h2);
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
              <section class="topinfo">
                <section class="imageShower">
                    <div class="squareCont">
                        <div class="square">
                                <img src="<?php echo $targetBuserInfo["info"]["pp"]; ?>">
                        </div>
                    </div>
                </section>
                <div class="rightsquare">
                  <p class="mainname"><?php echo $targetBuserInfo["info"]["uname"];?></p>
                  <p class="secondname"><?php echo $targetBuserInfo["username"];?> (<?php echo $targetBuserInfo["userFullid"];?>)</p>
                  <div class="blogSharerCont">
                    <a href="http://www.reddit.com/submit?title=Check out <?php echo $targetBuserInfo["info"]["uname"]; ?>'s posts on the Many Isles!&url=https://manyisles.ch/blog/profile%3Fu%3D<?php echo $targetBuser; ?>" target="_blank" class="fa fa-reddit"></a>
                    <a href="https://twitter.com/intent/tweet?text=Check out <?php echo $targetBuserInfo["info"]["uname"]; ?>'s posts on the Many Isles!%0A&url=https://manyisles.ch/blog/profile?u=<?php echo $targetBuser; ?>&hashtags=manyisles" target="_blank" class="fa fa-twitter"></a>
                    <a href="http://pinterest.com/pin/create/button/?url=https://manyisles.ch/blog/profile?u=<?php echo $targetBuser; ?>&media=<?php echo "https://manyisles.ch".$targetBuserInfo["info"]["pp"]; ?>&description=Check out <?php echo $targetBuserInfo["info"]["uname"]; ?>'s posts on the Many Isles!" target="_blank" class="fa fa-pinterest"></a>
                    <a class="fa fa-link fancyjump" onclick="navigator.clipboard.writeText('https://manyisles.ch/blog/profile?u=<?php echo $targetBuser; ?>');createPopup('d:gen;txt:Link copied!');"></a>
                  </div>
                </div>
              </section>
              <div class="description">
                <?php echo $targetBuserInfo["info"]["description"];?>
              </div>
              <?php
                if ($isTargetBuser) {
                  echo '<div class="submitBlocc"><a href="/blog/profileEdit"><div class="blogButton">Edit Profile</div></a></div>';
                }
              ?>
              <section class="feed"  blog-feed="" blog-feed-user="<?php echo $targetBuser; ?>">
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
</script>
