<?php
require_once($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine();

if (!isset($_GET["q"])){$_GET["q"] = "";}
$query = $blog->baseFiling->purate($_GET["q"], "full");

$query = "SELECT * FROM tags WHERE (tag LIKE '%$query%') ORDER BY uses DESC LIMIT 22";
if ($result = $blog->blogconn->query($query)) {
  if (mysqli_num_rows($result)> 0){
    while ($row = $result->fetch_assoc()) {
      echo "<p class='suggest-line fakelink' onclick='addTag(\"".$row["tag"]."\")'>".$row["tag"]." (".$row["uses"].")</p>";
    }
  }
  else {
    echo "<p class='suggest-line'>No tags found.</p>";
  }
}

?>
