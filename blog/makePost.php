<?php
require_once($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");

$blog = new blogEngine();
$filing = $blog->fileEngine();

if (!isset($_POST['profile'])){$_POST['profile']=0;}
$ptitle = substr(preg_replace("/[^A-Za-z0-9\(\)\&\'\- ]/", "", $_POST['title']), 0, 70);
$pgenre = substr(preg_replace("/[^A-Za-z0-9\(\)\&\'\- ]/", "", $_POST['genre']), 0, 22);
$profile = substr(preg_replace("/[^0-9]/", "", $_POST['profile']), 0, 22);
$ptext = substr(str_replace('"', '%double_quote%', $_POST['text']), 0, 10000);
$pcomments = 0; if (isset($_POST['comments']) AND $_POST['comments'] == "on"){$pcomments = 1;}
$pnotify = 0; if (isset($_POST['notify']) AND $_POST['notify'] == "on"){$pnotify = 1;}
$banner = null;$placedI = null;
if (isset($_FILES["banner"])) {
  $banner = $_FILES["banner"];
}
if (!$blog->hasProfile($profile)){
  $profile = $blog->buserId;
}
$postCode = $profile.time().$blog->user->generateRandomString(2);

if ($banner != null) {
  if ($realpath = $filing->new($banner, $postCode, "301")) {
    if ($filing->check($banner, "mystimg")){
      $placedI = $filing->add($banner["tmp_name"], $realpath);
    }
  }
}

$settings = [];
$settings["comments"]=$pcomments;
$settings = json_encode($settings);

$query = 'INSERT INTO posts (code, buser, title, genre, banner, text, settings) VALUES (
  "'.$postCode.'", "'.$profile.'", "'.$ptitle.'", "'.$pgenre.'", "'.$placedI.'", "'.$ptext.'", \''.$settings.'\'
)';

//echo $query; exit;

if ($blog->blogconn->query($query)){
  if ($pnotify) {
    $blog->notify($postCode, $profile);
  }
  $pTaitle = $blog->baseFiling->purate($ptitle);
  $blog->go("post/$postCode/$pTaitle?i=pubbed");
}
else {
  echo $query."<br>";
  echo $blog->blogconn->error;
}

?>
