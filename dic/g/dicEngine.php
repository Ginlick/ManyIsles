<?php

class dicEngine {
    public $conn;
    public $dicconn;
    public $user;
    public $curPage;

    function __construct($curPage = "Home"){
      $this->curPage = $curPage;
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
      $this->conn = $conn;
      //require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dic.php");
      //$this->dicconn = $dicconn;
      require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
      $this->user = new adventurer($this->conn);

      if (isset($_GET["i"])){
        //escape improper updates
        $this->user->killCache();
      }
    }

    //giver
    function giveStyles($cachable = true) {
      if (!$cachable){
        $this->user->killCache();
      }
      $return = <<<MAGDA
        <meta charset="UTF-8" />
        <title>current-information-place | Dictionary</title>
        <link rel="icon" href="/Imgs/FaviconDic.png">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/diltou.css?2">
        <link rel="stylesheet" type="text/css" href="/dic/g/dic.css">
      MAGDA;
      $return = str_replace("current-information-place", $this->curPage, $return);
      return $return;
    }
    function giveTopnav() {
      return '<div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl="true"></div>';
    }
    function giveLeftcol() {
      $return = <<<HAIL
      <div class="leftblock">
        <div class="dicLogo" load-image="/Imgs/greenbook.webp"></div>
        <h1 class="leftColH1">current-information-place</h1>
        <a href="/dic/home"><h2 class="leftColH2">Many Isles Dictionary</h2></a>
      </div>
      <div class="leftblock">

      </div>

      <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
      <ul class="myMenu bottomFAQ">
        <li><a class="Bar" href="" target="_blank">Dictionary Help</a></li>
      </ul>
      HAIL;
      $return = str_replace("current-information-place", $this->curPage, $return);
      return $return;
    }
    function giveFooter() {
      return '<div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>';
    }
    function giveScripts() {
      $return = <<<MAGDA
        <script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
        <script src="/Code/CSS/global.js"></script>
      MAGDA;
      return $return;
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
