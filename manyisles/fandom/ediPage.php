<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

if (!isset($_POST['branch'])){$branch = "fandom";} else {if (!checkRegger("wikiName", $_POST["branch"])){header("Location:/fandom/home");exit();} else {$branch = $_POST["branch"];}}
//base information
if ($branch == "docs"){
    $redirect = "/docs/1/home";
}
else if ($branch == "5eS") {
    $redirect = "/5eS/1/home";
}else {
    $redirect = "/fandom/home";
}

$name = ""; $shortName = ""; $wiki = 0;
if (!isset($_POST['writeInfo'])){$_POST['writeInfo'] = true;} else {if (preg_match("/[^0-9]/", $_POST['writeInfo'])==1){header("Location:$redirect");exit();}}
if (!isset($_POST['id'])){$_POST['id'] = 1;} else {if (preg_match("/[^0-9]/", $_POST['id'])==1){header("Location:$redirect");exit();}}
if (!isset($_POST['root'])){$_POST['root'] = 0;} else {if (preg_match("/[0-9]*/", $_POST['root'])!=1){header("Location:$redirect");exit();}}
if (isset($_POST['name'])){$name = purate($_POST['name'], "wikiName");}
if (isset($_POST['shortName'])){$shortName = purate($_POST['shortName'], "wikiName");}
if (!isset($_POST['categories'])){$_POST['categories'] = "";} else {if (preg_match("/[^0-9,]/", $_POST['categories'])==1){header("Location:$redirect");exit();}}
if (!isset($_POST['cate'])){$_POST['cate'] = "";} else {if (preg_match("/[^A-Za-z\- ]{2,}/", $_POST['cate'])==1){header("Location:$redirect");exit();}}
if (!isset($_POST['banner'])){$_POST['banner'] = "current";} else {if (preg_match('["<>]', $_POST['banner'])==1){header("Location:$redirect");exit();}}
if (!isset($_POST['NSFW'])){$_POST['NSFW'] = 0;} else {if (preg_match("/[^0-9]/", $_POST['NSFW'])==1){header("Location:$redirect");exit();}}
if (!isset($_POST['timeStart'])){$_POST['timeStart'] = "";}
if (!isset($_POST['timeEnd'])){$_POST['timeEnd'] = "";}
if (!isset($_POST['queryTags'])){$_POST['queryTags'] = "";} else {if (!checkRegger("basicList", $_POST["queryTags"])){header("Location:$redirect");exit();}}
if (!isset($_POST['importance'])){$_POST['importance'] = 0;} else {if (preg_match("/^[0-9]*$/", $_POST['importance'])!=1){header("Location:$redirect");exit();}}
if (!isset($_POST['sidetabTitle'])){$_POST['sidetabTitle'] = "";}
if (!isset($_POST['sidetabImg'])){$_POST['sidetabImg'] = "";}
if (!isset($_POST['sources'])){$_POST['sources'] ="";}
if (!isset($_POST['sidetabText'])){$_POST['sidetabText'] = "";}
if ($_POST['writeInfo'] == 0) {$writingNew = false;}else {$writingNew = true;}
if (isset($_POST['wiki'])){$wiki = purate($_POST['wiki'], "posint");}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
if ($branch)
$gen = new gen("act", $_POST['id'], $wiki, $writingNew, $branch);
$uname = $gen->userMod->uname;
$title = $gen->userMod->title;

if ($gen->domainType == "spells"){
  $spellable = new spellGen($gen);
  $version = 1;
  if ($writingNew) {
      $query = "SELECT max(id) FROM $gen->database LIMIT 1";
      $firstrow = $gen->dbconn->query($query);
      while ($row = $firstrow->fetch_row()) {
          $id = $row[0]+1;
      }
      $detailsArr = ["id"=>$id, "Name"=>"New Spell"];
  }
  else {
    $id = $gen->page;

    $version = $gen->article->version + 1;
    $detailsArr = $spellable->dic("live", false)[0];
  }
  $artName = "New Spell";
  foreach ($gen->editable as $key => $format){
    $value = "";
    if (isset($_POST[$key])){
      if ($format == "text" OR $format == "url"){
        $value = purate($_POST[$key], "text");
      }
      else if ($format == "int"){
        $value = purate($_POST[$key], "posint");
      }
      else {
        $value = purate($_POST[$key], "wikiName");
      }
      $value = txtParse($value, 2);
      $detailsArr[$key]=$value;
    }
  }
  if (!isset($detailsArr["Level"]) OR $detailsArr["Level"] == ""){$detailsArr["Level"]=0;}

  $query = 'INSERT INTO '.$gen->database.' (id, v, parentWiki, name, details) VALUES ("artId", "artV", "artParWik", "artName", \'artDetails\')';
  $query = str_replace("artId", $id, $query);
  $query = str_replace("artV", $version, $query);
  $query = str_replace("artParWik", $gen->parentWiki, $query);
  $query = str_replace("artName", $detailsArr["Name"], $query);
  $detailsArr = json_encode($detailsArr);
  $query = str_replace("artDetails", $detailsArr, $query);
}
else {
  function addWikiAuth($gen, $uname){
      if ($gen->parentWiki != 0 AND $gen->domain == "fandom"){
          $query = "SELECT authors FROM pages WHERE id = $gen->parentWiki ORDER BY v DESC LIMIT 1";
          $firstrow = $gen->dbconn->query($query);
          while ($row = $firstrow->fetch_assoc()) {
              $authors = $row["authors"];
              if ($authors == ""){$authors = $uname;}
              else if (strpos($authors, $uname) === false) {$authors = $authors.", ".$uname;}
              $query = 'UPDATE pages SET authors = "'.$authors.'" WHERE  id = '.$gen->parentWiki.' ORDER BY v DESC LIMIT 1';
              $gen->dbconn->query($query);
          }
      }
  }

  //escape errors
  if ($_POST["timeStart"]==null){
      $_POST["timeStart"] = "";
  }
  if ($_POST["timeEnd"]==null){
      $_POST["timeEnd"] = "";
  }
  if ($_POST["importance"] == ""){
      $_POST["importance"] = 0;
  }

  if ($writingNew) {
      $query = "SELECT max(id) FROM $gen->database LIMIT 1";
      $firstrow = $gen->dbconn->query($query);
      while ($row = $firstrow->fetch_row()) {
          $id = $row[0]+1;
      }
      $authors = $uname;
      $version = 1;
      $root = $_POST['root'];

      if ($root == 0) {
          $categories = "";
          if ($branch=="fandom"){ $query = "INSERT INTO wiki_settings (id, mods) VALUES ($id, '$gen->user')"; $gen->dbconn->query($query);}
      }
      else {
          $categories = $_POST['categories'];
          addWikiAuth($gen, $uname);
      }
  }
  else {
      $id = $_POST['id'];
      $categories =  $_POST['categories'];
      $version = $gen->article->version + 1;
      $authors = $gen->article->authors;
      if ($authors == ""){$authors = $uname;}
      else if (strpos($authors, $uname) === false) {$authors = $authors.", ".$uname;}

      $resultArray = getWiki($_POST['root'], $gen->database, $gen->dbconn, [], ["return" => "seen"]);
      if (in_array($id, $resultArray)){$_POST['root'] = $gen->parentWiki;}

      if ($gen->article->pop == null) {
          $gen->article->pop = 0;
      }

      if ($gen->article->root == 0 OR $_POST['root']=="" OR $_POST['root']==$id OR getWiki($_POST['root'], $gen->database, $gen->dbconn) != $gen->parentWiki){
          $root = $gen->article->root;
      }
      else {
          $root = $_POST['root'];
      }
      addWikiAuth($gen, $uname);
  }

  if($_POST['banner']=="current"){
      if ($writingNew){
          $banner = "default";
      }
      else {
          $banner = $gen->article->banner;
          if ($banner = $gen->defaultBanner){$banner = "default";}
      }
  }
  else {$banner = $_POST['banner'];}

  //post-details security

  if ($gen->domain == "docs" OR $gen->domain == "5eS") {
      $smolV = $version - 4;
      $query = "DELETE FROM $gen->database WHERE v < $smolV AND id = $id";
      $gen->dbconn->query($query);
  }
  if ($gen->domain == "fandom" AND $gen->power == 1){
      $adventurer = new adventurer($gen->conn, $gen->user);
      $adventurer->promote("Poet");
  }


  //NSFW
  if ($_POST["NSFW"] > 2 OR $_POST["NSFW"] < 0) {
      $NSFW = 0;
  }
  else {
      $NSFW = $_POST["NSFW"];
  }
  if ($root == 0){
      $genre = "Homepage";
  }
  else {
      $genre = $_POST["cate"];
  }
  $canon = 0;
  $parseClear = 0;
  if ($gen->power > 1){
      $canon = 1;
      if ($gen->power > 4){
          $parseClear = 1;
      }
  }


  //super stuff
  if ($gen->power > 1) {
      $queryTags = $_POST['queryTags'];
      $importance = $_POST['importance'];
  }
  else {
      $queryTags = $gen->article->queryTags;
      $importance = $gen->article->importance;
  }

  $name = substr($name, 0, 100);
  $shortName = substr($shortName, 0, 30);

  $body = str_replace('"', '%double_quote%', $_POST['body']);
  $sidetabText = str_replace('"', '%double_quote%', $_POST['sidetabText']);
  $sidetabTitle = str_replace('"', '%double_quote%', $_POST['sidetabTitle']);
  $sidetabImg = str_replace('"', '', $_POST['sidetabImg']);
  $sources = str_replace("'", '’', $_POST['sources']);
  $timeStart = str_replace('"', '', $_POST['timeStart']);
  $timeEnd = str_replace('"', '', $_POST['timeEnd']);
  if ($shortName == ""){$shortName = $name;}
  if ($importance > 99){$importance = 99;}
  if ($sidetabImg != "" AND $sidetabTitle == ""){$sidetabTitle = "###".$shortName;}

  $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, cate, banner, body, authors, pop, canon, root, sidetabTitle, sidetabImg, sidetabText, sources, categories, timeStart, timeEnd, importance, queryTags, parseClear, NSFW, parentWiki) VALUES (artid, artv, "artname", "artshortName", "artgenre", "artbanner", "artbody", "artauthors", artpop, artcanon, artroot, "artsidetabTitle", "artsidetabImg", "artsidetabText", \'artsources\', \'artcategories\', "arttimeStart", "arttimeEnd", artimportance, "artqueryTags", artparseClear, artNSFW, artparentWiki)';

  $query = str_replace("artid", $id, $query);
  $query = str_replace("artv", $version, $query);
  $query = str_replace("artname", $name, $query);
  $query = str_replace("artshortName", $shortName, $query);
  $query = str_replace("artgenre", $genre, $query);
  $query = str_replace("artbanner", $banner, $query);
  $query = str_replace("artbody", $body, $query);
  $query = str_replace("artauthors", $authors, $query);
  $query = str_replace("artpop", $gen->article->pop, $query);
  $query = str_replace("artcanon", $canon, $query);
  $query = str_replace("artroot", $root, $query);
  $query = str_replace("artsidetabTitle", $sidetabTitle, $query);
  $query = str_replace("artsidetabImg", $sidetabImg, $query);
  $query = str_replace("artsidetabText", $sidetabText, $query);
  $query = str_replace("artsources", $sources, $query);
  $query = str_replace("artcategories", $categories, $query);
  $query = str_replace("arttimeStart", $timeStart, $query);
  $query = str_replace("arttimeEnd", $timeEnd, $query);
  $query = str_replace("artimportance", $importance, $query);
  $query = str_replace("artqueryTags", $queryTags, $query);
  $query = str_replace("artparseClear", $parseClear, $query);
  $query = str_replace("artNSFW", $NSFW, $query);
  $query = str_replace("artparentWiki", $gen->parentWiki, $query);
}
//echo $query;exit();

if ($gen->dbconn->query($query)){
        header("Location:".$gen->artRootLink.$id."/".parse2Url($_POST['shortName']));
}
else {
    echo "Error.";
}


?>
