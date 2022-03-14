<?php
require_once("g/blogEngine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");
$blog = new blogEngine("Profile");
$blog->userCheck();
$targetBuser = $blog->buserId;
$blog->isPartnerVersion($targetBuser);
$buserInfo = $blog->fetchBuserInfo($targetBuser);

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title> Edit Profile | Blogs</title>
    <?php echo $blog->styles(false); ?>
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
</head>
<style>
.pp-upload-cont {
  width: 30%;
  max-width: 300px;
}
  .bannerBlock, .uploadable {
    width: 100%;
  }
  .bannerBlock.two {
    height: auto;
  }
  <?php
    if ($blog->partnerVersion) {
      echo ".imageform {display:none;}";
    }

   ?>
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
              <div class="crumbs">
                <a href="/blog/profile?<?php echo $blog->profileInset; ?>">Profile</a> - Edit
              </div>
              <h1>Edit Profile</h1>
              <div class="blogForm imageform">
                <div class="pp-upload-cont">
                  <div class="bannerBlock two">
                    <div class="buser-pp-squareCont">
                      <div class="circle">
                        <img alt="profile image" id="bannerBlock" src="<?php echo $buserInfo["info"]["pp"]; ?>"/>
                      </div>
                    </div>
                  </div>
                  <div class="uploadable">
                      <i class="fa-solid fa-arrow-up-from-bracket"></i>
                       Select Profile Picture<br> (max 450kb)
                       <input type="file" onchange="newPP(this);" class="fileInput" id="image-pp" value="null" name = "pp" accept=".png, .jpg"/>
                  </div>
                </div>
              </div>
              <form action="makeProfileEdit.php" method="POST" class="blogForm">
                <input type="text" placeholder="Username" name="buname" value="<?php echo $buserInfo["info"]["uname"]; ?>" />
                <textarea rows="5" placeholder="Description: What do you post about?" name="description"><?php echo txtUnparse($buserInfo["info"]["description"]); ?></textarea>
                <input type="text" style="display:none;opacity:0;" name="profile" value="<?php echo $blog->profileInset; ?>" />
                <?php if (!$blog->partnerVersion) {
                  echo '<div class="checkbox-block"><input type="checkbox" name="follow_notifs" '.$blog->giveRadiobutInset($buserInfo["info"]["setEmailNotifs"]).'/>
                  <label for="follow_notifs">Receive email notifications from people I follow</label></div>';
                }
                ?>
                <div class="checkbox-block">
                  <input type="checkbox" name="mention_notifs" <?php echo $blog->giveRadiobutInset($buserInfo["info"]["setMentionNotifs"])?> />
                  <label for="mention_notifs">Receive email notifications when I'm mentioned</label>
                </div>
                <div class="checkbox-block">
                  <input type="checkbox" name="public" <?php echo $blog->giveRadiobutInset($buserInfo["info"]["setPublic"])?> />
                  <label for="public">Show up on explore feed</label>
                </div>
                <div class="submitBlocc">
                  <button type="submit" class="blogButton independent">Save</button>
                </div>
              </form>
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
if (why == "updatedSucc"){
    createPopup("d:gen;txt:Profile successfully saved!");
}
else if (why == "updatedFail"){
    createPopup("d:gen;txt:Error. Profile couldn't be saved.");
}

function newPP(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.readAsDataURL(input.files[0]);

    getFile = encodeURI("uploadPP.php");
    var xhttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("file", input.files[0]);
    xhttp.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {
          console.log(this.responseText);
          if (this.responseText.includes("error") || this.responseText==""){
            createPopup("d:gen;txt:Error. Image could not be uploaded");
          }
          else {
            createPopup("d:gen;txt:Profile picture uploaded");
            document.getElementById("bannerBlock").setAttribute("src", this.responseText);
          }
        }
        else if (this.readyState == 4) {
          createPopup("d:gen;txt:Error. Image could not be uploaded");
        }
    };
    xhttp.open("POST", getFile, true);
    xhttp.send(formData);
  }
}
</script>
