<?php

require_once("g/dicEngine.php");
$dic = new dicEngine();
$wordInfo = $dic->wordInfo;
$dic->checkCredentials(false);

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $wordInfo["word"]; ?> | <?php echo $dic->curPage; ?> Dictionary</title>
    <?php echo $dic->giveStyles(); ?>
</head>
<style>

</style>
<body>
    <?php echo $dic->giveTopnav(); ?>
    <div class="flex-container">
        <div class='left-col'>
            <?php echo $dic->giveLeftcol(); ?>
        </div>

        <div class='column'>
          <div class="columnCont">
            <?php echo $dic->giveSignPrompt(); ?>
            <?php echo $dic->giveFindWords(); ?>
            <?php echo $dic->giveWordTab($wordInfo); ?>

           <?php
            if ($dic->canedit) {
              echo "<div style='margin-top:50px'><a href='/dic/editw?dicw=".$wordInfo["id"]."'><button>Edit</button></a><a href='/dic/editw?lang=$dic->language'><button>New</button></a></div>";
            }
           ?>
          </div>
        </div>
    </div>
    <?php echo $dic->giveFooter(); ?>


</body>
</html>
<?php echo $dic->giveScripts(); ?>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('w');
if (why == "notfound"){
  createPopup("d:gen;txt:Error. Page could not be found.");
}


</script>
