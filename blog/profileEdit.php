<?php
require_once("g/blogEngine.php");
$blog = new blogEngine("Profile");
$blog->userCheck();
$buserInfo = $blog->fetchBuserInfo();

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title> Edit Profile | Blogs</title>
    <?php echo $blog->styles(); ?>
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
</head>
<style>
  .bannerBlock {
    width: 30%;
    max-width: 300px;
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
              <?php echo $blog->giveSignPrompt(); ?>
              <div class="bannerBlock ">
                <div clas="buser-pp-squareCont">
                  <div class="circle">
                    <img alt="banner image" class="inBanner" id="bannerBlock" src=""/>
                  </div>
                </div>
              </div>
              <div class="uploadable bannerUploadCont">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i>
                   Select Banner (optional, max 2mb)
                   <input type="file" onchange="readURL(this);" class="fileInput" id="image" value="null" name = "banner" accept=".png, .jpg"/>
              </div>


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
