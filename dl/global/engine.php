<?php

class dlengine {
    public $productTab = '
           <div class="container">
                <a  href="SENDMETOTHEPRODUCT">
                    <div class="imgCont" load-image="GREATINDEXIMAGE">
                    </div>
                    <div class="titling">GRANDTITLE</div>
                </a>
            </div>
    ';
    public $conn;
    public $dlconn;
    public $partners = [];
    public $subgenresArr = [
      1 => [
        "c" => "Classes",
        "r" => "Races",
        "u" => "Rules",
        "a" => "Adventures",
        "l" => "Lore",
        "d" => "GM Resources",
      ],
      2 => [
        "h" => "Homebrewing",
        "r" => "Generator",
        "i" => "Index"
      ],
      3 => [
        "v" => "Visual",
        "m" => "Cartography",
        "n" => "Dungeons"
      ]
    ];
    public $gsystArr = [0 => "Any", 1 => "5eS", 2 => "5e"];
    public $typeNames = [1=>"Modules", 2=>"Tools", 3=> "Art", 4=> "Audio"];


    function __construct($conn, $dlconn = null){
        $this->conn = $conn;
        if ($dlconn == null) {
            require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dl.php");
        }
        $this->dlconn = $dlconn;
        require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
        $key = 0; if (isset($_COOKIE["loggedIn"])){$key = $_COOKIE["loggedIn"];}
        $this->user = new adventurer($this->conn, $key);

        $query = "SELECT id, status FROM partners";
        if ($max = $this->conn->query($query)) {
          while ($row = $max->fetch_assoc()){
            $this->partners[$row["id"]] = $row["status"];
          }
        }
    }

    //page generating
    function giveGlobs() {
      return <<<BLYAT
      <div w3-include-html="/Code/CSS/GTopnav.html"  w3-create-newEl="true"></div>
      <div class="showBGer button" onclick="$('.left-col').toggleClass('show');$(this.firstElementChild).toggleClass('rotate');">
        <i class="fa fa-bars"></i>
      </div>
      BLYAT;

    }
    function signPrompt() {
      if (!$this->user->signedIn){
        $signPrompt = '<h3>Sign In</h3>
        <form action="/account/SignIn.php?back=/dl/home" method="POST" onsubmit="seekMaker()" style="text-align:center">
          <label for="loguname"><b>Username</b></label>
          <input type="text" placeholder="Hansfried Dragonslayer" name="uname" id="loguname" autocomplete="username" required>
          <label for="logpassword"><b>Password</b></label>
          <input type="password" placeholder="uniquePassword22" name="psw" id="logpassword" autocomplete="current-password" required>
          <button><i class="fas fa-arrow-right"></i> Sign In</button>
        </form>
        <p>Don\'t have an account? <a href="/account/Account?add=dl">Join us</a></p>
        ';
      }
      else {
          $signPrompt = "<h3>".$this->user->fullName."</h3>
          <p>Currently signed in with a tier ".$this->user->tier." account. <a href='/account/SignedIn' target='_blank'>View account</a></p>
          <button onclick='signOut();'><i class='fas fa-arrow-right'></i> Sign Out</button>
          ";
      }
      $signPormpt = '
          <div class="logoRound">
              <div class="roundling">
                <img src="'.$this->user->image(2).'" />
              </div>
              <div class="accntInfoCont">
                <div class="accntInfoInCont">
                  '.$signPrompt.'
                </div>
              </div>
          </div>';
      return $signPormpt;
    }
    function giveSearch($additive = "Digital Library") {
        $signPrompt = $this->signPrompt();
        $search = '
        <div class="topBlock">
          <div class="accnterCont">
          '.$signPrompt.'
          </div>
            <h1 >'.$additive.'</h1>
            <form onsubmit="return goSearch(this);" class="searchForm">
                <input type="text" id="mySearch" class="search" name="query" placeholder="Search..." autocomplete="off" oninput="suggestNow();" onfocusout="killSugg();" onfocus="suggestNow();">
                <div class="suggestions" id="suggestions" style="display:none;"></div>
                <button class="searchButton"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>';
        return $search;
    }
    function giveAccTab() {
      $signPrompt = $this->signPrompt();
      return "<div class='accLine'>$signPrompt</div>";
    }
    function giveMenu() {
        $menu = <<<MEGAMMAMAM
        <div class="menuCont">
            <a href="/dl/home"><h3>Browse</h3></a>
            <div class="dropdown">
                <button onclick="dropdown()" class="dropbtn" id="showType">Modules</button>
                <div id="myDropdown" class="dropdown-content">
                    <p onclick="typeValue(1)">Modules</p>
                    <p onclick="typeValue(2)">Tools</p>
                    <p onclick="typeValue(3)">Art</p>
                </div>
            </div>
            <ul class="menuList" type="1">
              <li subgenre="">All</li>
              <li subgenre="c">Classes</li>
              <li subgenre="r">Races</li>
              <li subgenre="u">Rules</li>
              <li subgenre="a">Adventures</li>
              <li subgenre="l">Lore</li>
              <li subgenre="d">GM Resources</li>
              <li>
                  <select id="sysDropdown" onchange="newSys(this.value);">
                      <option value="0">Any System</option>
                      <option value="1">5eS</option>
                      <option value="2">5e</option>
                  </select>
              </li>
            </ul>

            <ul class="menuList"  type="2">
              <li subgenre="">All</li>
              <li subgenre="h">Homebrewing</li>
              <li subgenre="r">Generator</li>
              <li subgenre="i">Index</li>
            </ul>

            <ul class="menuList"  type="3">
              <li subgenre="">All</li>
              <li subgenre="v">Visual</li>
              <li subgenre="m">Cartography</li>
              <li subgenre="n">Dungeons</li>
            </ul>
            <form onsubmit="return goSearch(this)">
              <input type="text" class="" name="query" placeholder="Queries...">
              <button class=""><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
        MEGAMMAMAM;
        return $menu;
    }

    //action
    function prodTab($titling, $image, $link, $premium = false) {
        $producttab = str_replace("GREATINDEXIMAGE", $image, $this->productTab);
        $producttab = str_replace("GRANDTITLE", $titling, $producttab);
        $producttab = str_replace("SENDMETOTHEPRODUCT", $link, $producttab);
        if ($premium){$producttab = str_replace("titling", "titling premium", $producttab);}
        return $producttab;
    }
    function checkStat($prodid) {
        $query = 'SELECT partner FROM products WHERE id ='.$prodid;
        $result = $this->dlconn->query($query);
        $partner = "x";
        while ($row = $result->fetch_assoc()) {
            $partner = $row["partner"];
        }
        $query = 'SELECT status FROM partners WHERE name = "'.$partner.'"';
        $status = "active";
        $result = $this->dlconn->query($query);
        while ($row = $result->fetch_assoc()) {
            $status = $row["status"];
        }
        if ($status == "suspended"){return false;}
        else {return true;}
    }
    function prodRow($mode, $limit = 22) {
        $return = "";
        $query = "SELECT * FROM products";
        if ($mode == "popular"){
            $query .= " ORDER BY popularity DESC";
        }
        else if ($mode == "random"){
            $query .= " ORDER BY RAND()";
        }
        else {
            $query .= " ORDER BY id DESC";
        }
        if ($limit < 1){ $limit = 1; } else if ($limit > 22){ $limit = 22; }
        $query .= " LIMIT $limit";

        if ($toprow = $this->dlconn->query($query)) {
            while ($row = $toprow->fetch_assoc()) {
                $return .= $this->prodItemR($row);
            }
        }
        return $return;
    }
    function results(array $queries, $action = "row", $max = 30, $skipper = []) {
      $resulter = [];
      if ($action == "row"){$resulter = "";}

      $query = "";
      $genre = 1;
      $subgenre = "";
      $gsystem = 0;
      $method = "id";
      if (isset($queries["query"])){$query=$queries["query"];}
      if (isset($queries["genre"])){$genre=$queries["genre"];}
      if (isset($queries["subgenre"])){$subgenre=$queries["subgenre"];}
      if (isset($queries["gsystem"])){$gsystem=$queries["gsystem"];}
      if (isset($queries["method"])){$method=$queries["method"];}
      $regcate = "^";
      $categs = str_split($subgenre);
      foreach ($categs as $categ){
        $regcate.= "(?=.*$categ)";
      }
      $regcate = $regcate.".+$";

      $requ = 'SELECT * FROM products WHERE genre = '.$genre.' AND (name LIKE "%'.$query.'%" OR categories LIKE "%'.$query.'%") AND subgenre REGEXP "'.$regcate.'"';
      if (isset($queries["partner"])){$requ .= " AND partner = ".$queries["partner"];}
      $requ .=  ' ORDER BY '.$method.' DESC LIMIT 222';

      if ($result = $this->dlconn->query($requ)){
        if (mysqli_num_rows($result) > 0){
          $nicetotal = 0;
          while ($row = $result->fetch_assoc()){
            if ($nicetotal >= $max){break;}
            $id = $row["id"];
            if (in_array($id, $skipper)){continue;}
            $name = $row["name"];
            if ($row["shortName"] != ""){$shortName = $row["shortName"];} else {$shortName = $name;}
            $link = $this->url($id, $shortName);
            $thumbnail = $this->clearmage($row["image"]);
            if ($row["tier"]>0){$premium = true;}else{$premium = false;}
            $nicetotal++;
            if ($action == "row"){
              $resulter .= $this->prodTab($shortName, $thumbnail, $link, $premium);
            }
            else {
              $resulter[] = ["name"=>$name, "link"=>$link, "image"=> $thumbnail];
            }
          }
        }
      }
      if ($action == "row" AND $resulter == ""){$resulter = "Hmmm... there aren't many great results.";}
      return $resulter;
    }
    function prodItem(int $id){
        $query = "SELECT * FROM products WHERE id = $id";
        if ($toprow = $this->dlconn->query($query)) {
            while ($row = $toprow->fetch_assoc()) {
              return $this->prodItemR($row);
            }
        }
    }
    function prodItemR($row) {
      $partner = $row["partner"];
      if (!isset($this->partners[$partner]) OR $this->partners[$partner]!="active") {return "";}
      $titling = $row["name"];
      if($row["shortName"] != ""){$titling = $row["shortName"];}
      $image = $this->clearmage($row["image"]);
      $link = $this->url($row["id"], $titling);
      $premium = false;
      if ($row["tier"]!=0){$premium = true;}
      return $this->prodTab($titling, $image, $link, $premium);
    }


    function styles() {
      return <<<MAGDA
        <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
        <link rel="stylesheet" type="text/css" href="/ds/g/ds-g.css">
        <link rel="stylesheet" type="text/css" href="/dl/global/dl3.css">
      MAGDA;
    }
    function scripts() {
      return <<<MAGDA
        <script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
        <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
        <script src="/Code/CSS/global.js"></script>
        <script src="/dl/global/dl3.js"></script>
      MAGDA;
    }
    function baseVars($genre = 1, $subgenre = "[]", $gsystem = 0) {
      return <<<MAGDA
      <script>
        var type = $genre;
        var categs = $subgenre;
        var sysNum = $gsystem;
      </script>
      MAGDA;
    }
    function url($id, $name) {
      return "/dl/item/".$id."/".substr(urlencode(str_replace(" ", "_", $name)), 0, 50);
    }
    function clearmage($image) {
      if (!str_contains($image, "/")){
        $image = "/IndexImgs/".$image;
      }
      return $image;
    }
    function fileclear($file, $genre) {
      if (!str_contains($file, "/")){
        if ($genre == 1){
          $file = "/dl/Friiz/".$file;
        }
        else if ($genre == 3){
          $file = "/dl/Art/".$file;
        }
      }
      return $file;
    }
    function parsePartName($name) {
      if ($name == "Traveler"){$name = "a Traveler";}
      else if ($name == "Pantheon"){$name = "the Pantheon";}
      return $name;
    }
    function go($place) {
      echo "<script>window.location.replace('/dl/$place');</script>";
      exit;
    }
}


?>
