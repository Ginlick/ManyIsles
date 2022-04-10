<?php

require_once("g/dicEngine.php");
$dic = new dicEngine();

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
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
            <h1>Explore Dictionary</h1>
            <?php

             ?>
          </div>
        </div>
    </div>
    <?php echo $dic->giveFooter(); ?>

</div>



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
