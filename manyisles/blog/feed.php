<?php

require_once("g/blogEngine.php");
$blog = new blogEngine("Feed");

$blog->userCheck();
$userInfo = $blog->fetchBuserInfo();
$following = json_encode($userInfo["following"]);
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
          <div class="columnCont">
            <?php echo $blog->giveSignPrompt(); ?>
            <h1>Personal Feed</h1>
            <p>Posts from people you follow.</p>
            <?php echo $blog->genSortCont("explore-feed"); ?>
            <section class="feed"  blog-feed blog-feed-user='<?php echo $following; ?>' id="explore-feed">
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
if (why == "postDeleted"){
    createPopup("d:gen;txt:Post successfully deleted.");
}



</script>
