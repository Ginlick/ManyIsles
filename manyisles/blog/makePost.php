<?php
require_once($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/src/fileportal/fpi-engine.php");

$blog = new blogEngine();
$fpi = new fpi(301, true);

if (!isset($_POST['profile'])){$_POST['profile']=0;}
$ptitle = $blog->baseFiling->replaceSpecChar($_POST['title']);
$ptitle = substr($blog->baseFiling->purify($ptitle, "full"), 0, 70);
$pgenre = ""; if (isset($_POST['genre'])) {$pgenre = substr($_POST['genre'], 0, 1500);}
$profile = substr(preg_replace("/[^0-9]/", "", $_POST['profile']), 0, 22);
$ptext = substr($_POST['text'], 0, 10000);
$pcomments = 0; if (isset($_POST['comments']) AND $_POST['comments'] == "on"){$pcomments = 1;}
$pnotify = 0; if (isset($_POST['notify']) AND $_POST['notify'] == "on"){$pnotify = 1;}
$placedI = null;

if (!$blog->hasProfile($profile)){
  $profile = $blog->buserId;
}
$postCode = $profile.time().$blog->user->generateRandomString(2);

if (count($_FILES) > 0){
  $r = $fpi->handle("upload", $_FILES);
  if (!isset($r["error"])){
    $placedI = $r["files"][0]["dir"];
  }
}

//user references
$ptext = $blog->prepareText($ptext, $postCode);

$settings = [];
$settings["comments"]=$pcomments;
$settings = json_encode($settings);

$pgenre = $blog->getCommaArr($pgenre, "tag");
$blog->addTags($pgenre);
$pgenre = json_encode($pgenre, JSON_HEX_APOS|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
$ptext = $blog->baseFiling->replaceSpecChar($ptext);

$query = 'INSERT INTO posts (code, buser, title, genre, banner, text, settings) VALUES (
  "'.$postCode.'", "'.$profile.'", "'.$ptitle.'", \''.$pgenre.'\', "'.$placedI.'", "'.$ptext.'", \''.$settings.'\'
)';

//echo $query; exit;

if ($blog->blogconn->query($query)){
  if ($pnotify) {
    $blog->notify($postCode, $profile);
  }
  $blog->user->promote("Poet");

  $pTaitle = $blog->baseFiling->purate($ptitle);
  $blog->go("post/$postCode/$pTaitle?i=pubbed");
}
else {
  echo $query."<br>";
  echo $blog->blogconn->error;
}

?>
