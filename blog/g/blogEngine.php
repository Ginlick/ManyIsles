<?php

class blogEngine {
    public $conn;
    public $blogconn;
    public $user;
    public $curPage;
    public $buserType = "adventurer";
    public $buserId = 0;
    public $profileInset = "";
    public $partnerVersion = false;

    function __construct($curPage = "Feed"){
      $this->curPage = $curPage;
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
      $this->conn = $conn;
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_blogs.php");
      $this->blogconn = $blogconn;

      require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
      $this->user = new adventurer($this->conn);

      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php");
      $this->parse = new parser;

      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
      $this->baseFiling = new smolengine;

      //for embedded links
      include($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
      include($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
      $this->dlEngine = new dlengine($this->conn);

      $this->buserId = $this->fetchBuserId();
      $this->loadProfiles();
      $this->partner();

      if (isset($_GET["i"])){
        //escape improper updates
        $this->user->killCache();
      }
    }
    function createBuser() {
      $query = "INSERT INTO busers (user, type) VALUES ('".$this->user->user."', 'adventurer')";
      if ($this->blogconn->query($query)) {
        return $this->fetchBuserId();
      }
    }
    function fetchBuserId() {
      $query = "SELECT id FROM busers WHERE user = ".$this->user->user." AND type = 'adventurer'";
      if ($toprow = $this->blogconn->query($query)) {
        if (mysqli_num_rows($toprow) == 0) {
          return $this->createBuser();
        }
        while ($row = $toprow->fetch_assoc()) {
            return $row["id"];
        }
      }
    }
    function userCheck($return = "explore") {
      if (!$this->user->check(true, true)){
        $red = "?i=unsigned";
        if ($this->user->signedIn){$red = "?i=unconf";}
        if ($return) {
          $this->user->killCache();
          $this->go($return.$red);
        }
        echo "error credentials";
        exit;
      }
      return true;
    }
    function fetchBuserInfo($targetBuid = 0, $reusable = false){
      if ($targetBuid == 0) {$targetBuid = $this->buserId;}
      $result = [];
      $query = "SELECT * FROM busers WHERE id = ".$targetBuid;
      if ($toprow = $this->blogconn->query($query)) {
        if (mysqli_num_rows($toprow) == 1) {
          while ($row = $toprow->fetch_assoc()) {
            $targetBuserUser = new adventurer($this->conn, $row["user"]);
            $result["id"] = $targetBuid;
            $result["type"] = $row["type"];
            $result["user"] = $row["user"];
            $result["userFullid"] = "u#".$targetBuserUser->user;
            $result["username"] = $targetBuserUser->fullName;
            $result["userDiscname"] = $targetBuserUser->discname;

            $result["status"] = $row["status"];
            $result["actions"] = $row["actions"];
            $result["followers"] = $this->getArray($row["followers"]);
            $result["followNum"]=count($result["followers"]);
            $result["following"] = $this->getArray($row["following"]);
            $result["liked"] = $this->getArray($row["liked"]);

            $info = $this->getArray($row["info"]);
            if ($result["type"]=="partnership"){
              $query = "SELECT * FROM partners WHERE user = ".$targetBuserUser->user;
              if ($toprow = $this->conn->query($query)) {
                if (mysqli_num_rows($toprow) > 0) {
                  while ($row2 = $toprow->fetch_assoc()) {
                    $result["userFullid"] = "p#".$row2["id"];
                    $result["username"] = $row2["name"];
                    $info["pp"] = $row2["image"];
                  }
                }
              }
            }
            $info["pptype"]="round";
            if (!isset($info["uname"]) OR $info["uname"]==""){$info["uname"]=$targetBuserUser->uname;}
            if (!isset($info["description"])){$info["description"]="";}
            if (!isset($info["setEmailNotifs"])){$info["setEmailNotifs"]=1;}
            if (!isset($info["setMentionNotifs"])){$info["setMentionNotifs"]=1;}
            if (!isset($info["setPublic"])){$info["setPublic"]=1;}
            if (!isset($info["setShowDiscord"])){$info["setShowDiscord"]=1;}
            if (!$reusable){
              if (!isset($info["pp"]) OR $info["pp"]==""){$info["pp"]=$targetBuserUser->image(2);$info["pptype"]="full";}
              else {$info["pp"]=$this->baseFiling->clearmage($info["pp"]);}
            }
            $result["info"] = $info;

            return $result;
          }
        }
      }
      return false;
    }
    function loadProfiles() {
      $query = "SELECT id FROM busers WHERE user = ".$this->user->user;
      $profiles = [];
      if ($toprow = $this->blogconn->query($query)) {
        if (mysqli_num_rows($toprow) > 0) {
          while ($row = $toprow->fetch_assoc()) {
            $info = $this->fetchBuserInfo($row["id"]);
            $profiles[$info["type"]] = $info;
          }
        }
      }
      $this->profiles = $profiles;
    }
    function hasProfile($profile) {
      $hasit = false;
      foreach ($this->profiles as $prof) {
        if ($prof["id"]==$profile){
          return $prof;
        }
      }
      return false;
    }
    function deleteBuser() {
      //should also remove from "following"
      if ($this->user->check(true)){
        $query = "DELETE FROM comments WHERE buser = ".$this->buserId;
        if ($this->blogconn->query($query)) {
          $query = "DELETE FROM posts WHERE buser = ".$this->buserId;
          if ($this->blogconn->query($query)) {
            $query = "DELETE FROM busers WHERE id = ".$this->buserId;
            if ($this->blogconn->query($query)) {
              foreach ($this->profiles as $profile){
                foreach ($profile["following"] as $following){
                  $this->follow($profile["id"], $following, 0);
                }
              }
              return true;
            }
          }
        }
      }
      return false;
    }

    //partnership mess
    function partner() {
      $this->partner = false;
      if (isset($this->profiles["partnership"])){
        $this->partner = $this->profiles["partnership"]["id"];
        return true;
      }
      return false;
    }
    function partnerVersion() {
      $this->partnerVersion = true;
      $this->profileInset="p";
    }
    function isPartnerVersion(&$targetBuser = 0) {
      if (isset($_GET["p"]) AND $this->partner){
        $targetBuser = $this->partner;
        $this->partnerVersion();
      }
      else if ($prof = $this->hasProfile($targetBuser)) {
        if ($prof["type"]=="partnership"){
          $this->partnerVersion();
        }
      }
    }

    function giveTopnav() {
      return '<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>
      <div class="showBGer" onclick="showMenu(this)" id="barsBoi">
          <i class="fas fa-bars" > </i >
      </div >';
    }
    function giveLeftcol() {
      $return = <<<HAIL
      <div class="titleblock">
        <h1 class="leftColH1">current-information-place</h1>
        <a href="/blog/explore"><h2 class="leftColH2">Many Isles Blogs</h2></a>
      </div>
      <div class="left-menu">
        <div class="search-box">
          <input class="left-menu-search" type="text" oninput="suggestPosts(this);" onfocus="suggestPosts(this);" onfocusout="hideSuggest();" placeholder="Search posts..." />
          <div class="suggestions" id="suggest-this"></div>
        </div>
        <a class="left-menu-a" href="/blog/explore"><i class="fa-solid fa-house"></i> Explore</a>
        <a class="left-menu-a" href="/blog/search"><i class="fa-solid fa-hashtag"></i> Search by Tags</a>
        <a class="left-menu-a" href="/blog/feed"><i class="fa-regular fa-comment"></i> My Feed</a>
        profile-line
      </div>

      <a href="/blog/post?u=1"><div class="blogButton">Post</div></a>

      <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
      <ul class="myMenu bottomFAQ">
        <li><a class="Bar" href="/docs/44/Terms_of_Service" target="_blank">Terms of Service</a></li>
        <li><a class="Bar" href="/docs/80/Blogs" target="_blank">Blog Help</a></li>
      </ul>
      HAIL;
      $return = str_replace("?u=1", "?u=".$this->buserId, $return);
      $return = str_replace("current-information-place", $this->curPage, $return);
      $insert = '<a class="left-menu-a" href="/account/Account?error=signIn" target="_blank"><i class="fa-regular fa-user"></i> Sign In</a>';
      if ($this->user->signedIn){
        $insert = '<a class="left-menu-a" href="/blog/profile"><i class="fa-solid fa-user"></i> Profile</a>';
        if ($this->partner){
          $insert .= '<a class="left-menu-a" href="/blog/profile?p"><i class="fa-solid fa-user-pen"></i> Partnership Profile</a>';
        }
      }
      $return = str_replace("profile-line", $insert, $return);
      return $return;
    }
    function giveSignPrompt($return = "/blog/explore") {
      return $this->user->signPrompt($return);
    }
    function giveFooter() {
      return '<div w3-include-html="/blog/g/footer2.html" w3-create-newEl="true"></div>';
    }

    function styles($cachable = true) {
      if (!$cachable){
        $this->user->killCache();
      }
      $return = <<<MAGDA
        <meta charset="UTF-8" />
        <link rel="icon" href="/Imgs/Favicon.png">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/diltou.css">
        <link rel="stylesheet" type="text/css" href="/blog/g/blog2.css">
      MAGDA;
      return $return;
    }
    function scripts() {
      $return = <<<MAGDA
        <script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
        <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
        <script src="/Code/CSS/global.js?2"></script>
        <script src="/blog/g/blog-feed.js"></script>
      MAGDA;
      return $return;
    }

    //follow
    function doChanges($followers, $targetId, $otherId, $dir, $keyword = "followers") {
      if (($key = array_search($otherId, $followers)) !== false) {
        unset($followers[$key]);
      }
      if ($dir == "1"){
        $followers[] = $otherId;
      }
      $followers = json_encode($followers, JSON_HEX_APOS);
      $query = "UPDATE busers SET $keyword = '$followers' WHERE id = ".$targetId;
      echo $query;
      $this->blogconn->query($query);
      return true;
    }
    function follow($follower, $creator, $dir) {
      $buserInfo = $this->fetchBuserInfo($follower);
      $creatorInfo = $this->fetchBuserInfo($creator);

      if ($creatorInfo AND $buserInfo) {
        if ($this->doChanges($buserInfo["following"], $follower, $creator, $dir, "following")) {
          $this->doChanges($creatorInfo["followers"], $creator, $follower, $dir);
          if (count($creatorInfo["followers"])>= 21){
            $creator = new adventurer($this->conn, $creatorInfo["user"]);
            $creator->promote("Loremaster");
          }
        }
      }
    }
    //like
    function like($liker, $postId){
      $dir = 1;
      $buserInfo = $this->fetchBuserInfo($liker);
      if (($key = array_search($postId, $buserInfo["liked"])) !== false) {
        unset($buserInfo["liked"][$key]);
        $dir = 0;
      }
      if ($dir == 1){
        $buserInfo["liked"][] = $postId;
      }
      $liked = json_encode($buserInfo["liked"], JSON_HEX_APOS);
      $query = "UPDATE busers SET liked = '$liked' WHERE id = ".$liker;
      if ($this->blogconn->query($query)){
        if ($dir == 0){$direc = "-";}else {$direc = "+";}
        $db = $this->givePostDB($postId);
        $query = "UPDATE $db SET likes = likes $direc 1 WHERE code = '$postId'";
        if ($this->blogconn->query($query)){
          return $direc;
        }
      }
      return false;
    }
    //Tags
    function addTags(array $tagArr){
      foreach ($tagArr as $tag){
        if ($tag==""){continue;}
        $query = "SELECT id FROM tags WHERE tag = '$tag'";
        if ($result = $this->blogconn->query($query)){
          if (mysqli_num_rows($result) > 0) {
            while ($row = $result->fetch_assoc()){
              $id = $row["id"];
              $query2 = "UPDATE tags SET uses = uses + 1 WHERE id = $id";
            }
          }
          else {
            $query2 = "INSERT INTO tags (tag, uses) VALUES ('$tag', 1)";
          }
          $this->blogconn->query($query2);
        }
      }
    }

    //posts
    function genPostEmbed($image, $title, $link, $place) {
      $embedded = $this->embedStencil;
      $embedded = str_replace("%%embed_image", $image, $embedded);
      $embedded = str_replace("%%embed_title", $title, $embedded);
      $embedded = str_replace("%%embed_link", $link, $embedded);
      $embedded = str_replace("%%embed_place", $place, $embedded);
      return $embedded;
    }
    function giveEmbeds(&$postText) {
      $allEmbeds = "";$embedNum = 0;
      if (preg_match_all("/^.*\[embed\](.+)\[\/embed\].*$/m", $postText, $lineMatches)){
        foreach ($lineMatches[1] as $link){
          if ($embedNum == 5){break;}
          $embedNum++;
          if (preg_match("/\/fandom\/wiki\/([0-9]+)\/.*/", $link, $match)){
            $id = $match[1];
            if (class_exists("article")){
              $articleInfo = new article(["id"=>$id], $this->conn);
              if (preg_match("/^http.*$/", $articleInfo->banner)){$image = $articleInfo->banner;}else{$image = "/wikimgs/banners/".$articleInfo->banner;}
              $allEmbeds .= $this->genPostEmbed($image, $articleInfo->name, $link, "Fandom wiki article");
            }
          }
          else if (preg_match("/\/dl\/item\/([0-9]+)\/.*/", $link, $match)){
            $id = $match[1];
            if (class_exists("dlengine")){
              $query = "SELECT * FROM products WHERE id = ".$id;
              if ($toprow = $this->dlEngine->dlconn->query($query)) {
                if (mysqli_num_rows($toprow) > 0) {
                  while ($row2 = $toprow->fetch_assoc()) {
                    $allEmbeds .= $this->genPostEmbed($this->dlEngine->clearmage($row2["image"]), $row2["name"], $link, "Digital library publication");
                  }
                }
              }
            }
          }
          else if (preg_match("/\/ds\/([0-9]+)\/.*/", $link, $match)){
            $id = $match[1];
            $query = "SELECT * FROM dsprods WHERE id = ".$id;
            if ($toprow = $this->conn->query($query)) {
              if (mysqli_num_rows($toprow) > 0) {
                while ($row2 = $toprow->fetch_assoc()) {
                  $allEmbeds .= $this->genPostEmbed($row2["thumbnail"], $row2["name"], $link, "Digital store item");
                }
              }
            }
          }
          else {
            $name = $link; if (preg_match("/^.*\/([^\/\?]+).*$/", $link, $match)){$name = $match[1];}
            $name = substr($name, 0, 44);
            $allEmbeds .= $this->genPostEmbed("/Imgs/docs.png", $name, $link, "Link");
          }
        }
      }
      $postText = preg_replace("/^(.*)\[embed\].+\[\/embed\](.*)$/m", "$1$2", $postText);
      return $allEmbeds;
    }
    function giveReferences(&$postText) {
      //user references
      if (preg_match_all("/@u([0-9]+)/m", $postText, $lineMatches)){
        foreach ($lineMatches[1] as $userref){
          $userrefInfo = $this->fetchBuserInfo($userref);
          $newInset = "<a href='/blog/profile?u=$userref'>@".$userrefInfo["info"]["uname"]."</a>";
          $postText = str_replace("@u$userref", $newInset, $postText);
        }
      }
      //tag references
      $allTags = [];
      /*if (preg_match_all("/#([^\"'<\[#]+)#/m", $postText, $lineMatches)){
        foreach ($lineMatches[1] as $tag){
          if (!in_array($tag, $allTags)){$allTags[] = $tag;}else {continue;}
          $insert = "<a href='/blog/search?t=$tag' class='tag-element'>#".$tag."</a>";
          echo $insert;
          $postText = str_replace("#$tag#", $insert, $postText);
        }
      }*/
      if (preg_match_all("/#([^ ]+)/m", $postText, $lineMatches)){
        foreach ($lineMatches[1] as $tag){
          if (!in_array($tag, $allTags)){$allTags[] = $tag;}else {continue;}
          if (str_contains("'", $tag)){continue;}
          $insert = "<a href='/blog/search?t=$tag' class='tag-element'>#".$tag."</a>";
          $postText = preg_replace("/([^>])#$tag/", "$1".$insert, $postText);
        }
      }
    }
    function genPost($prow, $extent = 0, $public = false){
      $row = $prow;
      $post = $this->postStencil;
      $postBuser = $row["buser"];
      $postBuserInfo = $this->fetchBuserInfo($postBuser);
      $postAge = $this->givePostAge($row["reg_date"]);
      $postText = $this->parse->parse($row["text"], 1);
      $postTitleInf = $this->giveBlogTitle($row["title"], $postBuserInfo["info"]["uname"]); $postTitle = $postTitleInf["title"];
      if ($public AND $postBuserInfo["info"]["setPublic"]==0){return false;}
      $tags = $this->getArray($row["genre"]); $tagList = "";
      foreach ($tags as $tag){
        if ($tag == ""){continue;}
        $tagList .= "<a href='/blog/search?t=$tag'><span class='tag-element fakelink'>#$tag</span></a>";
      }
      $post = str_replace("post-imagge%%", $this->genPP($postBuserInfo["info"]["pp"], $postBuserInfo["info"]["pptype"]), $post);
      $post = str_replace("post-title-url", urlencode($postTitle), $post);
      $post = str_replace("post-buser-buname-url", urlencode($postBuserInfo["info"]["uname"]), $post);
      $post = str_replace("post-link", "blog/post/".$row["code"]."/".urlencode(substr(str_replace(" ", "_", $this->baseFiling->purate($postTitle)),0,22)), $post);
      $post = str_replace("post-code", $row["code"], $post);
      $post = str_replace("post-banner%%", $this->baseFiling->clearmage($row["banner"]), $post);
      $post = str_replace("post-buser-buname", $postBuserInfo["info"]["uname"], $post);
      $post = str_replace("post-buser-id", $postBuserInfo["id"], $post);
      $post = str_replace("post-buser-fullName", $postBuserInfo["username"], $post);
      $post = str_replace("post-age", $postAge, $post);
      if ($postTitleInf["hasTitle"]){$post = str_replace("%post-title", $postTitle, $post);} else {$post = str_replace("%post-title", "", $post);}
      $post = str_replace("%post-genre", $tagList, $post);
      $post = str_replace("post-likes", $row["likes"], $post);
      $post = str_replace("%post-comments", $this->fetchPostCommentNum($row["code"]), $post);
      if (!$this->arrAllows($row["settings"], "comments")){$post = str_replace("ALLOWS%COMMENTS", "hidden", $post);}
      if (in_array($row["code"], $this->profiles["adventurer"]["liked"])){$post = str_replace("likeable fa-regular", "likeable fa-solid active", $post);}

      if ($extent > 0){
        $post = str_replace("ADDMAINPOSTCLASS", "bigpost", $post);
      }
      //embedded links
      $post = str_replace("ADDEMBEDS*", $this->giveEmbeds($postText), $post);
      //references
      $this->giveReferences($postText);

      $post = str_replace("post-text", $postText, $post);
      return $post;
    }
    function genComment($row) {
      $post = $this->commentStencil;
      $postBuser = $row["buser"];
      $postBuserInfo = $this->fetchBuserInfo($postBuser);
      $postAge = $this->givePostAge($row["reg_date"]);
      $postText = $this->parse->parse($row["text"], 1);

      $post = str_replace("post-imagge%%", $this->genPP($postBuserInfo["info"]["pp"], $postBuserInfo["info"]["pptype"]), $post);
      $post = str_replace("%post-code", $row["code"], $post);
      $post = str_replace("%post-buser-buname", $postBuserInfo["info"]["uname"], $post);
      $post = str_replace("%post-buser-id", $postBuserInfo["id"], $post);
      $post = str_replace("%post-buser-fullName", $postBuserInfo["username"], $post);
      $post = str_replace("%post-age", $postAge, $post);
      $post = str_replace("%post-likes", $row["likes"], $post);
      if (in_array($row["code"], $this->profiles["adventurer"]["liked"])){$post = str_replace("likeable fa-regular", "likeable fa-solid active", $post);}

      $post = str_replace("ADDEMBEDS*", $this->giveEmbeds($postText), $post);
      $this->giveReferences($postText);

      $post = str_replace("%post-text", $postText, $post);
      return $post;
    }
    function genSortCont($feed) {
      $sortCont = <<<MAC
      <div class="sort-cont rectangle" id="sortcont$feed" target-feed="$feed" onload="sortify(this, '$feed')" >
        <div class="sort-tab selected" sort-by="new">
          <i class="fa-regular fa-sun"></i> New
        </div>
        <div class="sort-tab" sort-by="likes">
          <i class="fa-regular fa-heart"></i> Liked
        </div>
        <div class="sort-tab" sort-by="random">
          <i class="fas fa-dice-d20"></i> Random
        </div>
        <div class="sort-tab" sort-by="old">
          <i class="fa-regular fa-moon"></i> Old
        </div>
      </div>
      <div class="returnToTopEr fakelink" onclick="backToTop('sortcont$feed')" id='backToTop'>
        <i class="fas fa-arrow-up"></i> Back to top
      </div>
      MAC;
      return $sortCont;
    }
    function genProfileBlock($size = 0) {
      $pBlock = <<<PROFILEBLOCK
      <div class="profilesBlock rectangle" profile-selector="" id="pSelect">
        gimmeUserBlocks
        <div class="profileSuggs">
          gimmePSUGS
        </div>
        <input name="profile" style="display:none;" value="firstOptionBuser" id="pSelectinput" />
      </div>
      PROFILEBLOCK;
      $userBlock = <<<HI
      <div class="inside me" profile-inside="%%buid">
        gimmeIAFMEFIFFK
        <p class="mainname">gimmeMYNAME <i class="fa-regular fa-square-chevron-down"></i></p>
      </div>
      HI;
      if ($size == 1) {$pBlock = str_replace("profilesBlock", "profilesBlock small", $pBlock);}

      $profileResults = "";$selProfileResults = "";$firstOption = true;
      foreach ($this->profiles as $profile) {
        $block2 = $userBlock;
        if ($firstOption) {
          $pBlock = str_replace("firstOptionBuser", $profile["id"], $pBlock);
          $block2 = str_replace("inside me", "inside visible", $block2);
          $firstOption = false;
        }
        $block2 = str_replace("gimmeIAFMEFIFFK", $this->genPP($profile["info"]["pp"], $profile["info"]["pptype"], "small"), $block2);
        $block2 = str_replace("gimmeMYNAME", $profile["info"]["uname"], $block2);
        $block2 = str_replace("%%buid", $profile["id"], $block2);
        $profileResults .= $block2;
        $selProfileResults .= '<p class="mainname" profile-option="'.$profile["id"].'" profile-t-selector="pSelect">'.$profile["info"]["uname"].' ('.$profile["userFullid"].')</p>';
      }

      $pBlock = str_replace("gimmeUserBlocks", $profileResults, $pBlock);
      $pBlock = str_replace("gimmePSUGS", $selProfileResults, $pBlock);
      return $pBlock;
    }
    function genPP($image, $pptype, $classes = "") {
      $pp = $this->pp;
      if ($pptype=="full"){$pp = str_replace("circle-rounding", "", $pp);}
      $pp = str_replace("post-banner%%", $image, $pp);
      $pp = str_replace("specclasses", $classes, $pp);
      return $pp;
    }
    function giveBlogTitle($rawtitle, $buserUname = "") {
      $title = $this->baseFiling->placeSpecChar($rawtitle); $hasTitle = true;
      if ($title == ""){
        $title = "Post";
        $hasTitle = false;
        if ($buserUname != ""){
          $title .= " by ".$buserUname;
        }
      }
      return ["title"=>$title, "hasTitle"=>$hasTitle];
    }

    //actions
    function notify($postCode, $postBuserId, $concerns = "follow", $targetBuserId = 0) {
      $concArr = []; if (gettype($concerns)=="array"){$concArr = $concerns;$concerns = $concArr["concerns"];}

      $postBuser = $this->fetchBuserInfo($postBuserId);
      $query = "SELECT * FROM posts WHERE code = '$postCode'";
      if ($result = $this->blogconn->query($query)) {
        while ($row = $result->fetch_assoc()) {
          $postTitle = $row["title"];
          $postText = substr($this->parse->parse(preg_replace("/[\n\r]/", "", $row["text"]), 1), 0, 88)."...";
        }
      }
      if (!isset($postTitle)){return false;}
      $email = $this->emailPostNotif;
      $email = str_replace("%%POSTTITLE", $postTitle, $email);
      $email = str_replace("%%POSTTEXT", $postText, $email);
      $email = str_replace("%%POSTAUTHOR", $postBuser["info"]["uname"], $email);
      $email = str_replace("%%POSTAUTHLINK", "https://manyisles.ch/blog/profile?u=".$postBuserId, $email);
      $email = str_replace("%%POSTLINK", "https://manyisles.ch/blog/post/".$postCode."/".$this->baseFiling->purate($postTitle), $email);
      $headers = "From: pantheon@manyisles.ch" . "\r\n";
      $headers .= "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      if ($concerns == "mention"){
        if ($postBuserId == $targetBuserId){return true;}
        $subject = $postBuser["info"]["uname"].' mentioned you';
        $targetBuser = $this->fetchBuserInfo($targetBuserId);
        if ($targetBuser["info"]["setMentionNotifs"]==0){return;}
        if (isset($concArr["textType"]) AND $concArr["textType"] == "comment"){$mentText = "You've been mentioned in a comment";}else {$mentText = "You've been mentioned in this post";}
        $email = str_replace("%%POSTWHATSUP", $mentText, $email);
        $this->notifySendmail($email, $targetBuser, $targetBuserId, $subject, $headers);
      }
      else {
        $subject = $postBuser["info"]["uname"].' posted something';
        foreach ($postBuser["followers"] as $followerId){
          $follower = $this->fetchBuserInfo($followerId);
          if ($follower["info"]["setEmailNotifs"]==0){continue;}
          $email = str_replace("%%POSTWHATSUP", "There's a new post for you", $email);
          $this->notifySendmail($email, $follower, $followerId, $subject, $headers);
        }
      }
      return true;
    }
    function notifySendmail($specEmail, $follower, $followerId, $subject, $headers) {
      $specEmail = str_replace("%%USERIMG", $follower["info"]["pp"], $specEmail);
      $specEmail = str_replace("%%USERNAME", $follower["username"], $specEmail);
      mail($this->giveBuserEmail($follower["user"]), $subject, $specEmail, $headers);
    }
    function giveBuserEmail($buserId) {
      $query = "SELECT email FROM accountsTable WHERE id = ".$buserId;
      if ($result = $this->conn->query($query)) {
        while ($row = $result->fetch_assoc()) {
          return $row["email"];
        }
      }
      return "";
    }
    function prepareText($ptext, $postCode, $textType = "post") {
      if (preg_match_all("/@([^ ]+)/m", $ptext, $lineMatches)){
        foreach ($lineMatches[1] as $userrefo){
          $userref = $this->baseFiling->purify(strtolower($userrefo));
          $query = 'SELECT id FROM busers WHERE LOWER(REGEXP_REPLACE(username, " ", "")) = "'.$userref.'"';
          if ($toprow = $this->blogconn->query($query)) {
            if (mysqli_num_rows($toprow) > 0) {
              while ($row = $toprow->fetch_assoc()) {
                $replaceWith = "@u".$row["id"];
                $ptext = str_replace("@$userrefo", $replaceWith, $ptext);
                $this->notify($postCode, $row["id"], ["concerns"=>"mention","textType"=>$textType]);
              }
            }
          }
        }
      }
      return $ptext;
    }

    //miscellaneous tools
    function givePostAge($regdate){
      $now = new DateTime();
      $ago = new DateTime($regdate);
      //see if just give date
      $absdiff = $now->diff($ago)->format("%a");
      if ($absdiff > 7) {return $ago->format("dS F Y");}

      //fancy days
      $diff = $now->diff($ago);
      $diff->w = floor($diff->d / 7);
      $diff->d -= $diff->w * 7;

      $stringNames = array(
          'y' => 'year',
          'm' => 'month',
          'w' => 'week',
          'd' => 'day',
          'h' => 'hour',
          'i' => 'minute'
      );
      foreach ($stringNames as $k => $v) {
        if ($diff->$k > 0){
          $suffix = "s"; if ($diff->$k==1){$suffix="";}
          $text = $diff->$k." ".$v.$suffix." ago";
          return $text;
        }
      }
      return "Just Posted";
    }
    function givePostDB($postCode) {
      $db = "posts";
      if (preg_match("/^c/", $postCode)){$db = "comments";}
      return $db;
    }
    function fetchPostNum($targetBuid = 0){
      if ($targetBuid == 0) {$targetBuid = $this->buserId;}
      $postnum = 0;
      $query = "SELECT id FROM posts WHERE buser = ".$targetBuid;
      if ($result = $this->blogconn->query($query)){
        $postnum = mysqli_num_rows($result);
      }
      return $postnum;
    }
    function fetchPostCommentNum($postCode) {
      $postnum = 0;
      $query = "SELECT id FROM comments WHERE refPost = '$postCode'";
      if ($result = $this->blogconn->query($query)){
        $postnum = mysqli_num_rows($result);
      }
      return $postnum;
    }

    function go($place, $dom = "/blog/") {
      $this->user->go($dom.$place);
    }
    function getArray($arr) {
      $followers = [];
      $arr = preg_replace('/[\r]/', '\n', $arr);
      $arr = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $arr);
      $arr = str_replace("u0027", "'", $arr);
      $arr = json_decode($arr, true, 22, JSON_INVALID_UTF8_SUBSTITUTE);
      if (gettype($arr)=="array"){
        foreach ($arr as $k=>$value) {
          $arr[$k]=utf8_decode($value);
        }
        $followers = $arr;
      }
      return $followers;
    }
    function getCommaArr($arr, $reg = "full", $limit = 22) {
      $pgenre = explode(", ", $arr);
      $pgenre = array_slice($pgenre, 0, $limit);
      foreach ($pgenre as $key => &$tag) {
        $tag = $this->baseFiling->purify($tag, $reg);
        if ($tag == ""){unset($pgenre[$key]);}
      }
      $pgenre = array_values($pgenre);
      return $pgenre;
    }
    function arrAllows($arr, $key) {
      if (gettype($arr)!="array"){
        $arr = $this->getArray($arr);
      }
      if (isset($arr[$key]) AND $arr[$key]==1){
        return true;
      }
      return false;
    }
    function giveRadiobutInset($value) {
      $inset = "";
      if ($value == 1){
        $inset = "checked";
      }
      return $inset;
    }
    function fileEngine() {
      $this->userCheck();
      return new fileEngine($this->user->user);
    }

    public $pp = <<<mediaDir
    <div class="buser-pp specclasses">
        <div class="buser-pp-squareCont">
            <div class="circle circle-rounding">
                <img src="post-banner%%" />
            </div>
        </div>
    </div>
    mediaDir;
    public $postStencil =  <<<MACss
    <div class="post" id="post-code">
      <div class="buser-info">
        post-imagge%%
        <div class="buser-info-names">
          <a href="/blog/profile?u=post-buser-id"><p><span class="mainname">post-buser-buname</span> &#183; <span class="secondname">post-buser-fullName</span></p></a>
          <p><span class="secondname">post-age</span></p>
        </div>
      </div>
      <div class="main-post ADDMAINPOSTCLASS">
        <div class="main-post-banner" load-image="post-banner%%">
        </div>
        <div class="main-post-content">
          <h3 class="main-post-title">%post-title</h3>
          <div class="genreList">
            %post-genre
          </div>
          <div>
            post-text
          </div>
        </div>
        <a class="main-post-overlay" href="/post-link"></a>
      </div>
      ADDEMBEDS*
      <div class="bottom-infos">
        <div class="bottom-infos-left">
          <div class="smolinfo">
            <i class="likeable fa-regular fa-heart fakelink" onclick="toggleLike(this, 'post-code');"></i>
            <span id="likenumberpost-code">post-likes</span>
          </div>
          <div class="smolinfo ALLOWS%COMMENTS">
            <i class="fa-regular fa-message"></i>
            <span>%post-comments</span>
          </div>
        </div>
        <div class="bottom-infos-sharer">
          <a href="http://www.reddit.com/submit?title=post-buser-buname-url posted post-title-url on the Many Isles!&url=https://manyisles.ch/post-link" target="_blank" class="fa fa-reddit"></a>
          <a href="https://twitter.com/intent/tweet?text=post-buser-buname-url posted post-title-url on the Many Isles!&url=https://manyisles.ch/post-link&hashtags=manyisles" target="_blank" class="fa fa-twitter"></a>
          <a class="fa fa-link fancyjump" onclick="navigator.clipboard.writeText('https://manyisles.ch/post-link');createPopup('d:gen;txt:Link copied!');"></a>
        </div>
      </div>
    </div>
    MACss;
    public $commentStencil =  <<<MACss
    <div class="comment" id="%post-code">
      <div class="buser-info small">
        post-imagge%%
        <div class="buser-info-names">
          <a href="/blog/profile?u=%post-buser-id"><p><span class="mainname">%post-buser-buname</span> &#183; <span class="secondname">%post-buser-fullName</span></p></a>
          <p><span class="secondname">%post-age</span></p>
        </div>
      </div>
      <div class="main-post">
        <div class="main-post-content">
            %post-text
          </div>
        </div>
      </div>
      ADDEMBEDS*
      <div class="bottom-infos">
        <div class="bottom-infos-left">
          <div class="smolinfo">
            <i class="likeable fa-regular fa-heart fakelink" onclick="toggleLike(this, '%post-code');"></i>
            <span id="likenumber%post-code">%post-likes</span>
          </div>
        </div>
      </div>
    </div>
    MACss;
    public $embedStencil =  <<<MACss
      <div class="main-post embedded">
        <div class="main-post-content embedded">
          <div class="embed-thumbnail">
            <div class="buser-pp-squareCont">
              <div class="circle">
                <img src="%%embed_image" />
              </div>
            </div>
          </div>
          <div class="embeddedCont">
            <h3 class="main-post-title">%%embed_title</h3>
            <i>%%embed_place</i>
          </div>
        </div>
        <a class="main-post-overlay" href="%%embed_link" target="_blank"></a>
      </div>
    MACss;

    //emails
    public $emailPostNotif = <<<MYGREATMAIL
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8" />
      <title></title>
    </head>
    <body>
    <section style="width: 600px;background-color:white;margin:auto;min-height:100%;padding:80px 40px;font-family:arial">
          <div style="display:flex;align-content:center;">
              <div style="padding:10px;position:relative;">
                  <div style="position:relative;height:60px;width:60px;overflow:hidden;border-radius: 100px;">
                      <img style="width:100%;height:100%;object-fit: cover;" src="%%USERIMG">
                  </div>
              </div>
                <p style="margin:0;line-height: 80px;font-family:arial">%%USERNAME</p>
          </div>
          <div>
            <h1 style="font-family:arial">%%POSTWHATSUP</h1>
            <div style="border:1px solid #ddd;padding:10px;border-radius:6px;">
              <h3 style="margin-bottom:5px;">%%POSTTITLE</h3>
              <p style="margin-top:0;"><i>by <a href="%%POSTAUTHLINK">%%POSTAUTHOR</a></i></p>
              <p>%%POSTTEXT</p>
              <a href="%%POSTLINK">read more</a>
            </div>
          </div>
          <p>You can turn these notifications off from your Many Isles blogs <a href="https://manyisles.ch/blog/profile">profile</a>.
      </section>
    </body>
    </html>
    MYGREATMAIL;
}


?>
