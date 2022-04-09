<?php

require_once("g/dicEngine.php");
$dic = new dicEngine("Home");

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
            <h1>Explore Blogs</h1>
            <button>CHELLO</button>
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
var why = urlParams.get('i');
if (why == "notfound"){
  createPopup("d:gen;txt:Error. Page could not be found.");
}
else if (why == "unsigned"){
 createPopup("d:gen;txt:You need to sign in to access this.");
}
else if (why == "unconf"){
 createPopup("d:gen;txt:You need to confirm your email first.");
}

</script>
