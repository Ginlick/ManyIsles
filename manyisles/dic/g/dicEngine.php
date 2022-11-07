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

    public $canedit = false;

    function __construct($curPage = "Home") {
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php");
      $this->dicconn = $this->addConn("dic");
      $this->user = new adventurer($this->conn);
      $this->conn = $this->user->conn;
      $this->curPage = $curPage;
      $this->parser = new parser;

      $this->getLanguage();
      if (isset($_GET["i"])){
        //escape improper updates
        $this->user->killCache();
      }
    }
    function checkCredentials($rabid = true) {
      require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
      $this->wiki = new gen("view", 0, $this->language, false, "dic");
      if (!$this->wiki->canedit){
        if ($rabid){$this->go();}
      }
      $this->canedit = $this->wiki->canedit;
      return $this->wiki->canedit;
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
      $word = $this->purify($word, "dicWord");
      if ($row = $this->wordInfo($word, $lang)){
        return $row["id"];
      }
      else {
        $query = 'INSERT INTO words (lang, word) VALUES ('.$lang.', "'.$word.'")';
        if ($this->dicconn->query($query)) {
          return $this->dicconn->insert_id;
        }
      }
    }
    function wordExists(int $wordId, int $lang){
      $query = "SELECT id FROM words WHERE id = '$wordId' AND lang = '$lang'";
      if ($result = $this->dicconn->query($query)){
        if (mysqli_num_rows($result)>0){return true;}
      }
      return false;
    }
    function wordInfo($word, int $lang = null) {
      $fullWord = $this->purify($word, "dicWord");
      $word = $this->purify($word);
      if (preg_match($this->regArrayR["number"], $word)!==1) {
        $query = "SELECT * FROM words WHERE id = '$word'";
      }
      else {
        $word = $this->purify($word, "quotes");
        $query = 'SELECT * FROM words WHERE lang = '.$lang.' AND (simpleWord = "'.$word.'" OR word = "'.$fullWord.'")';
        echo $query;
      }
      if ($result = $this->dicconn->query($query)) {
        if (mysqli_num_rows($result) > 0) {
          while ($row = $result->fetch_assoc()) {
            $art = $row;
            $art["word"] = $this->placeSpecChar($art["word"]);
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
    function giveLeftcol(array $additionalBars = []) {
      $return = <<<HAIL
      <div class="leftblock">
        <h1 class="leftColH1">current-information-place</h1>
        <a href="/dic/home"><h2 class="leftColH2">Many Isles Dictionary</h2></a>
      </div>
      <div class="leftblock">
      <a class="Bar" href="/dic/home">Explore</a>
      <a class="Bar" href="/dic/translate">Translate</a>
      HAIL;
      foreach ($additionalBars as $bar){
        $return .= $bar;
      }
      $return .= <<<HAIL
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
    function giveWordTab($wordInfo) {
      //print_r($wordInfo);
      $wordTab = '      <section class="wordCont">
              <h1 class="wordTitle">'.$this->placeSpecChar($wordInfo["word"]);
      if ($this->isValidArr($wordInfo, "notes")){
        $notes = $wordInfo["notes"];
        if ($this->isValidInput($notes, "phonetic")){
          $wordTab .= "<span class='headingnote title'>".$notes["phonetic"]."</span>";
        }
      }
      $wordTab .= '</h1>
      <p class="headingnote">'.$this->curPage.' word</p>';
      if ($this->isValidArr($wordInfo, "notes")){
        if ($this->isValidInput($notes, "style")){
          $wordTab .= "<p class='headingnote collapsed'>Style: ".$this->purify($notes["style"], "full")."</p>";
        }
        if ($this->isValidInput($notes, "usage")){
          $wordTab .= "<p class='headingnote collapsed'>Context: ".$this->parser->parse($notes["usage"])."</p>";
        }
        if ($this->isValidInput($notes, "misc")){
          $wordTab .= "<p class='headingnote collapsed'>".$this->parser->parse($notes["misc"])."</p>";
        }
      }
      if (isset($wordInfo["specifications"])){
        foreach ($wordInfo["specifications"] as $group) {
          $wordTab .= "<div class='specGroup'>";
          $wordTab .= "<h3 class='wordSubTitle'>".$this->purify($group["wordtype"], "full")."</h3>";
          if (isset($group["conjugation"])){
            $wordTab .= "<p class='headingnote'>".$this->parser->parse($group["conjugation"])."</p>";
          }
          if ($this->isValidArr($group, "definitions")){
            $wordTab .= "<ol class='wordDefinitionUl'>";
            foreach ($group["definitions"] as $definition) {
              if (count($definition)==0){continue;}
              $wordTab .= "<li class='wordDefinitionBlock'>";
              if (isset($definition["definition"])) {
                $wordTab .= "<p>".$this->parser->parse($definition["definition"])."</p>";
              }
              if ($this->isValidArr($definition, "examples")) {
                $wordTab .= "<p class='headingnote example' >Sample Sentence</p><div class='wordExampleBlock'>";
                foreach ($definition["examples"] as $example) {
                  $wordTab .= "<p><span class='wordExampleHeader'>".$this->allLangs[$example["language"]].":</span> ".$this->parser->parse($example["sentence"])."</p>";
                }
                $wordTab .= "</div>";
              }
              if ($this->isValidArr($definition, "synonyms")) {
                $wordTab .= "<p class='headingnote example' >Synonyms</p><div class='wordExampleBlock'>";
                $prefix = "";
                foreach ($definition["synonyms"] as $synonym) {
                  $wordTab .= $prefix.$this->giveWordLink($synonym);
                  $prefix = ", ";
                }
                $wordTab .= "</div>";
              }
              if ($this->isValidArr($definition, "antonyms")) {
                $wordTab .= "<p class='headingnote example'>Antonyms</p><div class='wordExampleBlock'>";
                $prefix = "";
                foreach ($definition["antonyms"] as $antonym) {
                  $wordTab .= $prefix.$this->giveWordLink($antonym);
                  $prefix = ", ";
                }
                $wordTab .= "</div>";
              }
              $wordTab .= "</li>";
            }
            $wordTab .= "</ol>";
          }
          $wordTab .= "</div>";
        }
      }
      if ($this->isValidArr($wordInfo, "notes")){
        // $wordTab .= "<div style='padding-top:1em;'></div>";
        if ($this->isValidInput($notes, "time")){
          $wordTab .= "<p class='headingnote collapsed'>Usage over time: ".$this->parser->parse($notes["time"])."</p>";
        }
        if ($this->isValidInput($notes, "etymology")){
          $wordTab .= "<p class='headingnote collapsed'>Etymology: ".$this->parser->parse($notes["etymology"])."</p>";
        }
      }
      if (isset($wordInfo["translations"])){
        $wordTab .= "<h2 class='wordSectionTitle'>Translations</h2>";
        $wordTab .= "<ul>";
        foreach ($wordInfo["translations"] as $words) {
          if ($this->isValidArr($words, "words")){
            $wordTab .= "<li>".$this->allLangs[$words["language"]].": "; $prefix = "";
            foreach ($words["words"] as $word) {
              if ($word == 0) {continue;}
              $wordTab .= $prefix.$this->giveWordLink($word); $prefix = ", ";
            }
            $wordTab .= "</li>";
          }
        }
        $wordTab .= "</ul>";
        $wordTab .= "<p><a href='/dic/translate?sl=$this->language&s=".$wordInfo["word"]."'>View on translate</a></p>";
      }
      $wordTab .= '</section>';

      return $wordTab;
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

    function isValidArr($arr, $key){
      if (isset($arr[$key]) AND count($arr[$key])>0){
        if (count($arr[$key])==1 AND array_values($arr[$key])[0]==""){return false;}
        return true;
      }
      return false;
    }
    function isValidInput($arr, $key){
      if (isset($arr[$key]) AND $arr[$key]!=""){
        return true;
      }
      return false;
    }
    function giveWordLink($word, $alttext = "") {
      if ($alttext == "") {if ($newword = $this->wordInfo($word)) {$alttext = $newword["word"];} else {return false;}}
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
