<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

class dicEngine {
    public $conn;
    public $dicconn;
    public $user;
    public $curPage = "";
    use allBase;

    public $language = 0;
    public $word = 0;

    function __construct($curPage = "Home") {
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
      $this->conn = $conn;
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dic.php");
      $this->dicconn = $dicconn;
      $this->user = new adventurer($this->conn);
      $this->curPage = $curPage;

      $this->getLanguage();
      if (isset($_GET["i"])){
        //escape improper updates
        $this->user->killCache();
      }
    }
    function checkPower($rabid = true) {
      $astat = 1;
      $query = "SELECT banned, admin, super FROM poets WHERE id =".$this->user->user;
      if ($max = $this->conn->query($query)) {
          while ($row = $max->fetch_assoc()){
              if ($row["banned"]!=0){$astat = 0;}
              else if ($row["super"]!=0){$astat = 5;}
              else if ($row["admin"]!=0){$astat = 4;}
          }
      }
      if ($astat<5){
        if ($rabid){$this->go();}
        return false;
      }
      return true;
    }

    //setup
    function getLanguage($language = null) {
      if ($language == null) {
        if (isset($_GET["dicd"])) {
          $language = $this->purify($_GET["dicd"], "number");
          if ($language != ""){
            $this->language = $language;
          }
        }
        else if (isset($_GET["dicw"])){
          $word = $this->purify($_GET["dicw"], "number");
          if ($word != "" AND $info = $this->wordInfo($word)){
            $this->word = $word;
            $this->wordInfo = $info;
            $this->language = $info["lang"];
          }
        }
        $language = $this->language;
      }
      $allLangs = [];
      $query = "SELECT * FROM languages";
      if ($result = $this->dicconn->query($query)) {
        if (mysqli_num_rows($result) > 0) {
          while ($row = $result->fetch_assoc()) {
            $allLangs[$row["id"]] = $row["name"];
            if ($row["id"] == $language){
              $this->langInfo = $row;
              $this->curPage = $row["name"];
            }
          }
        }
      }
      $this->allLangs = $allLangs;
      /*if (!isset($this->langInfo)){
        if ($this->language == 1){echo "Language Error";exit;}
        getLanguage(1);
      }*/
    }

    //dic functionalities
    function wordId($word, int $lang = null) {
      if ($lang == null) {$lang = $this->language;}
      $word = $this->purify($word, "quotes");
      if ($row = $this->wordInfo($word, $lang)){
        return $row["id"];
      }
      else {
        $query = "INSERT INTO words (lang, word) VALUES ($lang, '$word')";
        if ($this->dicconn->query($query)) {
          return $this->dicconn->insert_id;
        }
      }
    }
    function wordInfo($word, int $lang = null) {
      if (preg_match($this->regArrayR["number"], $word)!==1) {
        $query = "SELECT * FROM words WHERE id = '$word'";
      }
      else {
        $word = $this->purify($word, "quotes");
        $query = "SELECT * FROM words WHERE lang = $lang AND word = '$word'";
      }
      if ($result = $this->dicconn->query($query)) {
        if (mysqli_num_rows($result) > 0) {
          while ($row = $result->fetch_assoc()) {
            $art = $row;
            $art["specifications"] = json_decode($row["definitions"], true);
            $art["notes"] = json_decode($row["notes"], true);
            $art["translations"] = json_decode($row["translations"], true);
            unset($art["definitions"]);
            return $art;
          }
        }
      }
      return false;
    }
    function quickWord($wid) {
      $query = "SELECT word FROM words WHERE id = $wid";
      if ($result = $this->dicconn->query($query)) {
        if (mysqli_num_rows($result) > 0) {
          while ($row = $result->fetch_assoc()) {
            return $row["word"];
          }
        }
      }
      return false;
    }

    //giver
    function giveStyles($cachable = true) {
      if (!$cachable){
        $this->user->killCache();
      }
      $return = <<<MAGDA
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8" />
        <link rel="icon" href="/Imgs/FaviconDic.png">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/diltou.css?2">
        <link rel="stylesheet" type="text/css" href="/dic/g/dic.css">
      MAGDA;
      return $return;
    }
    function giveTopnav() {
      return '<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>';
    }
    function giveLeftcol() {
      $return = <<<HAIL
      <div class="leftblock">
        <h1 class="leftColH1">current-information-place</h1>
        <a href="/dic/home"><h2 class="leftColH2">Many Isles Dictionary</h2></a>
      </div>
      <div class="leftblock">
      <a class="Bar" href="/dic/home">Explore</a>
      <a class="Bar" href="/dic/translate">Translate</a>
      </div>

      <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
      <ul class="myMenu bottomFAQ">
        <li><a class="Bar" href="" target="_blank">Dictionary Help</a></li>
      </ul>
      HAIL;
      //<div class="dicLogo" load-image="/Imgs/greenbook.webp"></div>
      $return = str_replace("current-information-place", $this->curPage, $return);
      return $return;
    }
    function giveFindWords() {
      return <<<MAD
      <section class="wordCont">
        <div class="findWords">
          <input placeholder="Search for a word..." onfocus="suggestNow(this)" oninput="suggestNow(this)" onfocusout="gKillSugg('suggestions')" />
          <div id="suggestions" class="suggestions"></div>
        </div>
      </section>
      MAD;
    }
    function giveFooter() {
      return '<div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>';
    }
    function giveScripts() {
      $return = <<<MAGDA
        <script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;600&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
        <script src="/Code/CSS/global.js?2"></script>
        <script src="/dic/g/dic.js"></script>
        <script> var language = $this->language; </script>
      MAGDA;
      return $return;
    }

    function giveWordLink($word, $alttext = "") {
      if ($alttext == ""){$alttext = $this->wordInfo($word)["word"];}
      return "<a href='/dic/word/$word'>".$alttext."</a>";
    }
    //misc
    function giveSignPrompt($return = "/dic/home") {
      return $this->user->signPrompt($return);
    }
    function go($where = "home", $dir = "/dic/"){
      $this->user->go($dir.$where);
    }
}


?>
