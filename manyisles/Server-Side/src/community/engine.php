<?php
class communityEngine { //for all the extra user info: blog profile, partner
    public $conn;
    public $blogconn;
    public $user;
    public $buserType = "adventurer";
    public $buserId = 0;
    public $profileInset = "";
    public $partnerVersion = false;

    function __construct(){
      require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
      $this->user = new adventurer;
      $this->conn = $this->user->conn;
      $this->blogconn = $this->user->addConn("blogs");

      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php");
      $this->parse = new parser;

      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
      $this->baseFiling = new smolengine;

      $this->buserId = $this->fetchBuserId();
      $this->loadProfiles();
      $this->partner();
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

    //element generating
    function giveSignPrompt($return = "/home") {
      return $this->user->signPrompt($return);
    }
    function commStyles() {
      $return = <<<MAGDA
        <link rel="stylesheet" type="text/css" href="/blog/g/blog2.css">
      MAGDA;
      return $return;
    }
    function giveTopnav() {
      return '<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>';
    }
    function giveFooter() {
      return '<div w3-include-html="/blog/g/footer2.html" w3-create-newEl="true"></div>';
    }
    function processElement($text, $buserInfo){
      $text = str_replace("%%BUSERNAME", $buserInfo["info"]["uname"], $text);
      $text = str_replace("%%FULLUSERNAME", $buserInfo["username"], $text);
      $text = str_replace("%%USERID", $buserInfo["userFullid"], $text);
      $text = str_replace("%%BUSERID", $buserInfo["id"], $text);
      $text = str_replace("%%BUSERIMG", $buserInfo["info"]["pp"], $text);
      $imgStyle = ""; if ($buserInfo["info"]["pptype"]=="round"){$imgStyle == "circle-rounding";}
      $text = str_replace("%%BUSERIMGSTYLE", $imgStyle, $text);
      $discordRef = ""; if ($buserInfo["type"]=="adventurer" AND $buserInfo["info"]["setShowDiscord"]==1 AND $buserInfo["userDiscname"]!=""){$discordRef = '<p class="secondname">Discord: '.$buserInfo["userDiscname"].'</p>';}
      $text = str_replace("%%DISCORDREF", $discordRef, $text);
      return $text;
    }
    function genPP($image, $pptype, $classes = "") {
      $pp = $this->pp;
      if ($pptype=="full"){$pp = str_replace("circle-rounding", "", $pp);}
      $pp = str_replace("post-banner%%", $image, $pp);
      $pp = str_replace("specclasses", $classes, $pp);
      return $pp;
    }

    //miscellaneous tools
    function giveTimeDiff($regdate){
      $now = new DateTime();
      $ago = new DateTime($regdate);
      //see if just give date
      $absdiff = $now->diff($ago)->format("%a");
      if ($absdiff > 7) {return $ago->format("jS F Y");}

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
      return "Just Now";
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

    //element templates
    public $pp = <<<mediaDir
    <div class="buser-pp specclasses">
        <div class="buser-pp-squareCont">
            <div class="circle circle-rounding">
                <img src="post-banner%%" />
            </div>
        </div>
    </div>
    mediaDir;
    public $buserSquare = <<<HEREDOC
    <section class="topinfo">
      <section class="imageShower">
          <div class="squareCont">
              <div class="circle %%BUSERIMGSTYLE">
                  <img src="%%BUSERIMG">
              </div>
          </div>
      </section>
      <div class="rightsquare">
        <p class="mainname">%%BUSERNAME</p>
        <p class="secondname">%%FULLUSERNAME (%%USERID)</p>
        %%DISCORDREF
        %%EXTRAREFS
      </div>
      %%EXTRACONT
    </section>
    HEREDOC;
}


?>
