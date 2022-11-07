<?php

require_once("g/blogEngine.php");
$blog = new blogEngine("Search");

$tags = []; if (isset($_GET['t'])) {$tags = $blog->getCommaArr(substr($_GET['t'], 0, 1500));}

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title>Search | Blogs</title>
    <?php echo $blog->styles(); ?>
</head>
<style>
.tag-searcher {
  margin: 0 5px;
  width: calc(100% - 10px);
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
            <?php echo $blog->giveSignPrompt(); ?>
            <h1>Search Blogs</h1>
            <?php echo $blog->genSortCont("explore-feed"); ?>
            <div class="tagCont rectangle" id="tagcont">
              Selected tags:
            </div>
            <div class="search-box">
              <input class="tag-searcher" placeholder="Add tags..." onfocus="suggestTags(this)" oninput="suggestTags(this)" onfocusout="hideSuggest()"/>
              <div class="suggestions" id="suggest-tags"></div>
            </div>
            <section class="feed"  blog-feed blog-feed-settings='{"explore":1}' blog-feed-tags="" id="explore-feed">
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

var tagBlock = document.getElementById("tagcont");
if (tagBlock != null){
  var tagblock = "<span class='tag-element fakelink' onclick='removeTag(this)'>#taggname</span>";
  var feedling = document.getElementById("explore-feed");
  var tags = [<?php foreach ($tags as $tag){echo "'$tag'";} ?>];
  for (let tag of tags){
    tagBlock.innerHTML += tagblock.replace("taggname", tag);
  }
  function addTag(tag) {
    if (!tags.includes(tag)){
      fullTagblock = tagblock.replace("taggname", tag);
      tagBlock.innerHTML += fullTagblock;
      tags.push(tag);
      updateFeed();
    }
  }
  function removeTag(elmnt) {
    var tag = elmnt.innerHTML.replace("#", "");
    tags.splice(tags.indexOf(tag), 1);
    elmnt.remove();
    updateFeed();
  }
  function updateFeed() {
    feedling.setAttribute("blog-feed-tags", tags.join(", "));
    fillFeeds();
  }
}
updateFeed();

</script>
