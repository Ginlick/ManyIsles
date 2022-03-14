<?php
require_once($_SERVER['DOCUMENT_ROOT']."/blog/g/blogEngine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");
$blog = new blogEngine();
$blog->userCheck(false);

if (!isset($_POST['buname'])){$_POST['buname']="";}
if (!isset($_POST['description'])){$_POST['description']="";}
$buname = substr(preg_replace("/[^A-Za-z0-9\&\'\- ]/", "", $_POST['buname']), 0, 22);
$description = substr(txtParse($_POST['description']), 0, 2200);

$profileType = "adventurer";
if (isset($_POST['profile']) AND $_POST['profile']=="p"){$profileType = "partnership";$blog->partnerVersion();}
$emailNotifs = 0;
if (isset($_POST['follow_notifs']) AND $_POST['follow_notifs']=="on"){$emailNotifs = 1;}
$public = 0;
if (isset($_POST['public']) AND $_POST['public']=="on"){$public = 1;}
$mentionNotifs = 0;
if (isset($_POST['mention_notifs']) AND $_POST['mention_notifs']=="on"){$mentionNotifs = 1;}

$targetBuserId = $blog->buserId;
if ($profileType == "partnership"){$targetBuserId = $blog->partner;}
$buserInfo = $blog->fetchBuserInfo($targetBuserId, true);

$newBuserInfo = $buserInfo["info"];
$newBuserInfo["uname"] = $buname;
$newBuserInfo["description"] = $description;
$newBuserInfo["setEmailNotifs"] = $emailNotifs;
$newBuserInfo["setMentionNotifs"] = $mentionNotifs;
$newBuserInfo["setPublic"] = $public;
$newBuserInfo = json_encode($newBuserInfo);

$query = "UPDATE busers SET info = '$newBuserInfo', username = \"$buname\" WHERE id = ".$targetBuserId;

if ($blog->blogconn->query($query)){
  $blog->go("profileEdit?i=updatedSucc&".$blog->profileInset);
}

$blog->go("profileEdit?i=updatedFail&".$blog->profileInset);

?>
