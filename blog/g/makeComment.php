<?php
require_once($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");

$blog = new blogEngine();
$filing = $blog->fileEngine();

if (!isset($_POST['profile'])){$_POST['profile']=0;}
$text = substr(str_replace('"', '%double_quote%', $_POST['text']), 0, 10000);
$profile = substr(preg_replace("/[^0-9]/", "", $_POST['profile']), 0, 22);
$postCode = preg_replace($blog->baseFiling->regArrayR["basic"], "", $_POST['code']);
if (!$blog->hasProfile($profile)){
  $profile = $blog->buserId;
}

$comCode = "c".$profile.time().$blog->user->generateRandomString(2);


$query = 'INSERT INTO comments (code, buser, refPost, text) VALUES (
  "'.$comCode.'", "'.$profile.'", "'.$postCode.'", "'.$text.'"
)';

//echo $query; exit;

if ($blog->blogconn->query($query)){
  $blog->go("post/$postCode/post?i=cpubbed#commentSectionHeader");
}
else {
  echo $query."<br>";
  echo $blog->blogconn->error;
}

?>
