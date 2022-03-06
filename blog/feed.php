<?php

require_once("g/blogEngine.php");
$blog = new blogEngine("Feed");

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title>Feed | Blogs</title>
    <?php echo $blog->styles(); ?>
</head>
<style>

</style>
<body>
    <?php echo $blog->giveTopnav(); ?>
    <div class="flex-container">
        <div class='left-col'>
            <?php echo $blog->giveLeftcol(); ?>
        </div>

        <div class='column'>
          <?php echo $blog->giveSignPrompt(); ?>
            search posts and sorty by, blah blah

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
if (why == "unsigned"){
    createPopup("d:acc;txt:Sorry, you must sign in first.");
}
else if (why == "unconf"){
    createPopup("d:acc;txt:Sorry, you must confirm your email first.");
}
</script>
