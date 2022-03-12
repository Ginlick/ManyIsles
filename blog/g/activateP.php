<?php
require($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
$blog = new blogEngine;
if (!$blog->partner) {
  $blog->dlEngine->partner(true);

  $info = [];
  $info["uname"] = $blog->dlEngine->partName;
  $info["pp"] = $blog->dlEngine->partImage;
  $info["description"] = $blog->dlEngine->partDesc;
  $info = json_encode($info, JSON_HEX_APOS);

  $query = "INSERT INTO busers (user, type, info) VALUES ('".$blog->user->user."', 'partnership', '$info')";
  if ($blog->blogconn->query($query)) {
    $blog->go("profile?p&i=pSetup");
  }
  $blog->go("Publish?i=notExtend", "/account/");
}
$blog->go("profile?p&i=pSetup");

?>
