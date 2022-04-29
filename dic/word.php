<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();
if (!($wordInfo = $dic->wordInfo)) {
  $dic->go("home?i=notfound");
}
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
            <?php
            $additional = [];
            if ($dic->canedit) {
              $additional = ["<a class='Bar' href='/dic/users/langhub?dicd=$dic->language'>Language Hub</a>"];
            }
             echo $dic->giveLeftcol($additional);
             ?>
        </div>

        <div class='column'>
          <div class="columnCont">
            <?php echo $dic->giveSignPrompt("/dic/word/".$wordInfo["id"]); ?>
            <?php echo $dic->giveFindWords(); ?>
            <?php echo $dic->giveWordTab($wordInfo); ?>

           <?php
            if ($dic->canedit) {
              echo "<div style='margin-top:50px'><a href='/dic/editw?dicw=".$wordInfo["id"]."'><button>Edit</button></a><a href='/dic/editw?dicd=$dic->language'><button>New</button></a></div>";
            }
            else if ($dic->user->check(true, true)) {
              echo "<a href='/dic/users/rcur.php?lang=$dic->language'>Request editing rights</a>";
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
var why = urlParams.get('i');
if (why == "changed"){
  createPopup("d:poet;txt:Information changed.");
}
else if (why == "error"){
  createPopup("d:poet;txt:Error. Changes could not be made.");
}


</script>
