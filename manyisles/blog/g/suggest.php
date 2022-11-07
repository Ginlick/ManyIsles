<?php
require_once($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine();

if (!isset($_GET["q"])){$_GET["q"] = "";}
$query = $blog->baseFiling->purate($_GET["q"]);

$query = "SELECT * FROM posts WHERE (title LIKE '%$query%') OR (genre LIKE '%$query%') ORDER BY likes DESC LIMIT 22";
if ($result = $blog->blogconn->query($query)) {
  if (mysqli_num_rows($result)> 0){
    while ($row = $result->fetch_assoc()) {
      echo "<a class='suggest-line' href='/blog/post/".$row["code"]."/".$blog->baseFiling->purate($row["title"])."'>".$row["title"]."</a>";
    }
  }
  else {
    echo "<p class='suggest-line'>No posts found.</p>";
  }
}

?>
