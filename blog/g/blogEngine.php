<?php

class blogEngine {
    public $conn;
    public $blogconn;
    public $user;
    public $curPage;
    public $buserType = "adventurer";
    public $buserId = 0;

    function __construct($curPage = "Feed"){
      $this->curPage = $curPage;
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
      $this->conn = $conn;
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_blogs.php");
      $this->blogconn = $blogconn;

      require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
      $key = 0; if (isset($_COOKIE["loggedIn"])){$key = $_COOKIE["loggedIn"];}
      $this->user = new adventurer($this->conn, $key);

      require($_SERVER['DOCUMENT_ROOT']."/wiki/parse.php");
      $this->parse = new parse($this->conn, 0, 0);

      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
      $this->baseFiling = new smolengine;
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
            $this->buserId = $row["id"];
        }
      }
    }
    function userCheck($return = "feed") {
      if (!$this->user->check(true, true)){
        $red = "?i=unsigned";
        if ($this->user->signedIn){$red = "?i=unconf";}
        $this->go($return.$red);
      }
      $this->fetchBuserId();

      return true;
    }
    function fetchBuserInfo($targetBuid = 0){
      if ($targetBuid == 0) {$this->fetchBuserId(); $targetBuid = $this->buserId;}
      $result = [];
      $query = "SELECT * FROM busers WHERE id = ".$targetBuid;
      if ($toprow = $this->blogconn->query($query)) {
        if (mysqli_num_rows($toprow) == 1) {
          while ($row = $toprow->fetch_assoc()) {
            $targetBuserUser = new adventurer($this->conn, $row["user"]);
            $result["id"] = $targetBuid;
            $result["user"] = $row["user"];
            $result["userFullid"] = "u#".$targetBuserUser->user;
            $result["username"] = $targetBuserUser->fullName;
            $result["type"] = $row["type"];
            $result["status"] = $row["status"];
            $result["actions"] = $row["actions"];
            $info = [];
            $arr = json_decode($row["info"]); if (gettype($arr)=="array"){$info = $arr;}
            $info["pptype"]="round";
            if (!isset($info["uname"])){$info["uname"]=$targetBuserUser->uname;}
            if (!isset($info["pp"])){$info["pp"]=$targetBuserUser->image(2);$info["pptype"]="full";}
            if (!isset($info["description"])){$info["description"]="";}

            $result["info"] = $info;
            return $result;
          }
        }
      }
      return false;
    }

    function giveTopnav() {
      return '<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>';
    }
    function giveLeftcol() {
      $return = <<<HAIL
      <div class="titleblock">
        <h1 class="leftColH1">current-information-place</h1>
        <a href="/blog/feed"><h2 class="leftColH2">Many Isles Blogs</h2></a>
      </div>
      <div class="left-menu">
        <a class="left-menu-a">A search bar</a>
        <a class="left-menu-a" href="/blog/explore"><i class="fa-solid fa-house"></i> Explore</a>
        <a class="left-menu-a" href="/blog/feed"><i class="fa-solid fa-hashtag"></i> My Feed</a>
        profile-line
      </div>

      <a href="/blog/post"><div class="blogButton">Post</div></a>

      <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
      <ul class="myMenu bottomFAQ">
          <li><a class="Bar" href="" target="_blank">Help</a></li>
      </ul>
      HAIL;
      $return = str_replace("current-information-place", $this->curPage, $return);
      $insert = '<a class="left-menu-a" href="/blog/profile"><i class="fa-solid fa-user-large"></i> Profile</a>';
      if (!$this->user->signedIn){
        $insert = '<a class="left-menu-a" href="/account/Account?error=signIn" target="_blank"><i class="fa-solid fa-user-large"></i> Sign In</a>';
      }
      $return = str_replace("profile-line", $insert, $return);
      return $return;
    }
    function giveSignPrompt($return = "/blog/feed") {
      return $this->user->signPrompt($return);
    }
    function giveFooter() {
      return '<div w3-include-html="/blog/g/footer.html" w3-create-newEl="true"></div>';
    }

    function styles($dom = "base") {
      $return = <<<MAGDA
        <meta charset="UTF-8" />
        <link rel="icon" href="/Imgs/Favicon.png">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
        <link rel="stylesheet" type="text/css" href="/ds/g/ds-g.css">
        <link rel="stylesheet" type="text/css" href="/blog/g/blog.css">
      MAGDA;
      return $return;
    }
    function scripts($dom = "dl") {
      $return = <<<MAGDA
        <script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
        <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
        <script src="/Code/CSS/global.js"></script>
        <script src="/blog/g/blog-feed.js"></script>
      MAGDA;
      return $return;
    }

    function genPost($prow){
      $row = $prow;
      $post = $this->postStencil;
      $postBuser = $row["buser"];
      $postBuserInfo = $this->fetchBuserInfo($postBuser);
      $postAge = $this->givePostAge($row["reg_date"]);
      $postText = $this->parse->bodyParser($row["text"], 1);

      if ($postBuserInfo["info"]["pptype"]=="full"){$post = str_replace("circle-rounding", "", $post);}
      $post = str_replace("post-title-url", urlencode($row["title"]), $post);
      $post = str_replace("post-buser-buname-url", urlencode($postBuserInfo["info"]["uname"]), $post);
      $post = str_replace("post-link", "blog/post/".$row["code"]."/".urlencode(substr(str_replace(" ", "_", $row["title"]),0,22)), $post);
      $post = str_replace("post-code", $row["code"], $post);
      $post = str_replace("post-buser-pp", $postBuserInfo["info"]["pp"], $post);
      $post = str_replace("post-buser-buname", $postBuserInfo["info"]["uname"], $post);
      $post = str_replace("post-buser-fullName", $postBuserInfo["username"], $post);
      $post = str_replace("post-age", $postAge, $post);
      $post = str_replace("post-banner%%", $this->baseFiling->clearmage($row["banner"]), $post);
      $post = str_replace("post-title", $row["title"], $post);
      $post = str_replace("post-genre", $row["genre"], $post);
      $post = str_replace("post-likes", $row["likes"], $post);
      $post = str_replace("post-te", $row["genre"], $post);
      $post = str_replace("post-text", $postText, $post);
      return $post;
    }
    function givePostAge($regdate){
      $now = new DateTime();
      $ago = new DateTime($regdate);
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
          $text = $diff->$k." ".$v."s ago";
          return $text;
        }
      }
      return "Just Published";
    }

    function go($place, $dom = "/blog/") {
      echo "<script>window.location.replace('$dom$place');</script>";
      exit;
    }
    function fileEngine() {
      $this->userCheck();
      return new fileEngine($this->user->user);
    }


    public $postStencil =  <<<MACss
    <div class="post" id="post-code">
      <div class="buser-info">
        <div class="buser-pp">
            <div class="buser-pp-squareCont">
                <div class="circle circle-rounding">
                    <img src="post-buser-pp">
                </div>
            </div>
        </div>
        <div class="buser-info-names">
          <p><span class="mainname">post-buser-buname</span> &#183; <span class="secondname">post-buser-fullName</span></p>
          <p><span class="secondname">post-age</span></p>
        </div>
      </div>
      <div class="main-post">
        <div class="main-post-banner" load-image="post-banner%%">
        </div>
        <div class="main-post-content">
          <h3 class="main-post-title">post-title <span class="genreTab"> &#183; post-genre</span></h3>
          <div>
            post-text
          </div>
        </div>
        <a class="main-post-overlay" href="post-link"></a>
      </div>
      <div class="bottom-infos">
        <div class="bottom-infos-left">
          <div class="smolinfo">
            <i class="fas fa-heart fakelink" onclick="togglelike(this);"></i>
            <span likenumber>post-likes</span>
          </div>
          <div class="smolinfo">
            <i class="fa-solid fa-message"></i>
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
}


?>
