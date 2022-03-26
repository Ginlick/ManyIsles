<?php

require_once("g/blogEngine.php");
$blog = new blogEngine("Post");
$blog->userCheck();
$buserInfo = $blog->fetchBuserInfo();
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title>New Post | Blogs</title>
    <?php echo $blog->styles(); ?>
    <style>
    .inputErr {
        font-size: min(calc(9px + .3vw), 14px);
        color: red;
        text-align:left;
        margin:0;
        padding-left:.4vw;
        display:none;
    }
    </style>
</head>
<body>
    <?php echo $blog->giveTopnav(); ?>
    <div class="flex-container">
        <div class='left-col'>
            <?php echo $blog->giveLeftcol(); ?>
        </div>

        <div class='column'>
          <?php echo $blog->giveSignPrompt(); ?>
          <div class="columnCont">
            <h1>Create a Post</h1>

            <form action="makePost.php" method="POST" enctype="multipart/form-data" class="blogForm">
              <?php echo $blog->genProfileBlock(); ?>

              <div class="bannerBlock" >
                <img alt="banner image" class="inBanner" id="bannerBlock" src=""/>
              </div>
              <div class="uploadable bannerUploadCont">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i>
                   Select Banner (optional, max 2mb)
                   <input type="file" onchange="readURL(this);" class="fileInput" id="image" value="null" name = "banner" accept=".png, .jpg"/>
              </div>
              <input type="text" placeholder="Title (Optional)" name="title"/>
              <datalist id="genreSugg" />
                <option value="Lore" />
                <option value="News" />
                <option value="Game System - Feature" />
                <option value="Fan Theory" />
                <option value="Project" />
              </datalist>
              <textarea rows="10" placeholder="Text" name="text" id="textBody" oninput="setWarning();" required></textarea>
              <p id="charWarn" class="inputErr">Too many characters!</p>
              <input type="text" placeholder="Tags (eg. d&d, news, release)" name="genre" list="genreSugg" />
              <div class="checkbox-block">
                <input type="checkbox" name="comments" checked/><label for="comments">Allow comments</label>
              </div>
              <div class="checkbox-block">
                <input type="checkbox" name="notify" checked/><label for="notify">Send email notification to my followers</label>
              </div>
              <div class="submitBlocc">
                <a href="/blog/feed"><button type="button" class="blogButton independent grey">Discard</button></a>
                <button type="submit" class="blogButton independent">Publish</button>
              </div>
            </form>
            <p>
              The text body takes <a href="/docs/24/Markdown" target="_blank">markdown</a>.
              You can also use <a href="/docs/81/Special_Markdown" target="_blank">embedded links</a> and <a href="/docs/81/Special_Markdown" target="_blank">user references</a>.
            </p>
          </div>
        </div>
    </div>
    <?php echo $blog->giveFooter(); ?>

</div>



</body>
</html>
<?php echo $blog->scripts(); ?>
<script>

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById("bannerBlock").setAttribute("src", e.target.result);
      document.getElementById("bannerBlock").style.display = "block";
    };
    reader.readAsDataURL(input.files[0]);
  }
}

var textBody = document.getElementById("textBody");
var showing = false;
function setWarning() {
  fullNum = textBody.value.length;
  if (fullNum > 22){
    if (!showing) {
      document.getElementById("charWarn").style.display = "block";
      showing = true;
    }
  }
  else if (showing) {
    document.getElementById("charWarn").style.display = "none";
    showing = false;
  }
}
</script>
