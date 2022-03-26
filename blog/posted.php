<?php

require_once("g/blogEngine.php");
$blog = new blogEngine("Post");

if (isset($_GET["p"])){$postId = $blog->baseFiling->purate($_GET['p']);}else {$blog->go("explore?i=notfound");}
$notfound = true;
$query = "SELECT * FROM posts WHERE code = '$postId'";
if ($toprow = $blog->blogconn->query($query)) {
  if (mysqli_num_rows($toprow) == 1) {
    while ($row = $toprow->fetch_assoc()) {
      $notfound = false;
      $pRow = $row;
      $pParsed = $blog->genPost($pRow, 1);
      $pShortName = $blog->giveBlogTitle($row["title"])["title"];
      $pCode = $row["code"];
      $pOwner = $row["buser"];
    }
  }
}
if ($notfound) {$blog->go("explore?i=notfound");}

$commentNumber = $blog->fetchPostCommentNum($postId);
$isTargetBuser = false;
if ($blog->hasProfile($pOwner)){
  $blog->userCheck();
  $isTargetBuser = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title><?php echo $pShortName; ?> | Blogs</title>
    <?php echo $blog->styles(); ?>
</head>
<style>
.userComment {
  padding-bottom: 12px;
  border-bottom: var(--blog-standardborder);
}
.userComment-input {
  text-align: right;
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
            <?php echo $blog->giveSignPrompt("/blog/post/$postId/"); ?>
            <section class="feed">
              <?php echo $pParsed; ?>
              <?php if ($isTargetBuser) {
                echo '
                      <div class="submitBlocc">
                        <a href="/blog/g/delPost?p='.$postId.'"><button type="button" class="blogButton independent grey">Delete Post</button></a>
                      </div>';
              }
              ?>
            </section>
            <section class="<?php if (!$blog->arrAllows($pRow["settings"], "comments")){echo "hidden"; } ?>">
              <div class="<?php if (!$blog->user->signedIn){echo "hidden"; } ?>">
                <h2 id="commentSectionHeader">Write Comment</h2>
                <div class="userComment">
                  <form action="/blog/g/makeComment.php" method="POST">
                    <?php echo $blog->genProfileBlock(1); ?>
                    <div class="userComment-input">
                      <textarea rows="4" placeholder="Write a comment..." name="text" required></textarea>
                      <input name="code" value="<?php echo $pCode; ?>" style="display: none;" />
                      <button class="blogButton lesser" type="submit">Comment</button>
                    </div>
                  </form>
                </div>
              </div>
              <h2>Comments <span class="secondname">&#183; <?php echo $commentNumber; ?></span></h2>
              <div class="comment-feed">
                <?php echo $blog->genSortCont("comment-feed"); ?>
                <section class="feed"  blog-feed blog-feed-type="comments" blog-feed-reference="<?php echo $pCode; ?>" id="comment-feed">
                </section>
              </div>
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
if (why == "pubbed"){
    createPopup("d:gen;txt:Post published!");
}
else if (why == "cpubbed"){
  createPopup("d:gen;txt:Comment posted!");
}
else if (why == "failedDelete"){
  createPopup("d:gen;txt:Error. Failed to delete post.");
}

</script>
