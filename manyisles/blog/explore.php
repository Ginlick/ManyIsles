<?php

require_once("g/blogEngine.php");
$blog = new blogEngine("Explore");

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title>Explore | Blogs</title>
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
          <div class="columnCont">
            <?php echo $blog->giveSignPrompt(); ?>
            <h1>Explore Blogs</h1>
            <?php echo $blog->genSortCont("explore-feed"); ?>
            <section class="feed"  blog-feed blog-feed-settings='{"explore":1}' id="explore-feed">
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
