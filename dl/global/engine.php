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
    public $partners = [0=>["status"=>"suspended"]];
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
      ],
      4 => [
        "a" => "Ambient Music",
        "p" => "Active Music"
      ],
      5 => [
        "p" => "Player Character",
        "m" => "Monster Figurine",
        "t" => "Terrain",
        "a" => "Accessory"
      ]
    ];
    public $gsystArr = [0 => "Any", 1 => "5eS", 2 => "5e"];
    public $typeNames = [1=>"Modules", 2=>"Tools", 3=> "Art", 4=> "Audio", 5 => "3d Models"];
    public $typeDets = [1=>["type"=>"dlPdf"],2=>["type"=>"dlPdf"],3=>["type"=>"dlArt"],4=>["type"=>"bigAudio"],5=>["type"=>"dl3d"]];


    function __construct($conn = null, $dlconn = null){
        if ($conn == null) {
            global $conn;
            require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
        }
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
    function equipPart($row) {
      $this->partId = $row["id"];
      $this->partName = $row["name"];
      $this->partImage = $row["image"];
      $this->partStat = $row["status"];
      $this->partDesc = $row["jacob"];
      $this->pUsId = $row["user"];
      $this->ppower = 0;
      if ($row["type"]!= ""){$this->ppower = $row["type"];}
      $regDate = $row["reg_date"];

      $this->pType = "Companionship";
      if ($this->ppower == 1){$this->pType = "Full Partnership";}
      $date_array = date_parse($regDate);
      $this->pRegDate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];

      $this->partDS = false;
      $query = 'SELECT acceptCodes FROM partners_ds WHERE id = '.$this->partId;
      if ($result = $this->conn->query($query)){
          if (mysqli_num_rows($result) != 0) {
              $this->partDS = true;
          }
      }

      $this->totalPub = 0;
      $this->totalPrem = 0; $this->totalPop = 0; $this->totalDl = 0;
      $query = "SELECT tier, popularity, downloads  FROM products WHERE partner = $this->partId AND status != 'deleted'";
      if ($firstrow = $this->dlconn->query($query)){
        while ($row = $firstrow->fetch_assoc()) {
          $this->totalPub += 1;
          if ($row["tier"]!=0){$this->totalPrem += 1;}
          $this->totalPop += $row["popularity"];
          $this->totalDl += $row["downloads"];
        }
      }
    }
    function partner($full = false) {
      $query = "SELECT * FROM partners WHERE user = ".$this->user->user;
      if ($max = $this->conn->query($query)) {
        while ($row = $max->fetch_assoc()){
          $this->equipPart($row);
          if ($full){
            if (!$this->user->check(true, true)){$this->go("Account", "p");}
            if ($this->partStat == "deleted") {$this->go("Account", "p");}
            if ($full == "ds"){
              if (!$this->partDS){
                $this->go("activate", "ds");
              }
            }
          }
          return true;
        }
      }
      if ($full) {
        $this->go("BePartner", "p");
      }
      return false;
    }
    function partInfo($pId) {
      $query = "SELECT * FROM partners WHERE id = $pId";
      if ($max = $this->conn->query($query)) {
        while ($row = $max->fetch_assoc()){
          $this->equipPart($row);
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
    function giveSearch($additive = "Digital Library") {
        $signPrompt = $this->user->signPrompt("/dl/home");
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
    function giveAccTab($back = "/dl/home") {
      $signPrompt = $this->user->signPrompt($back);
      return "<div class='accLine'>$signPrompt</div>";
    }
    function giveMenu() {
        $menu = <<<MEGAMMAMAM
          <div class="menuCont">
            <a href="/dl/home"><h3>Browse</h3></a>
            <div class="dropdown">
                <button onclick="dropdown()" class="dropbtn" id="showType">Modules</button>
                <div id="myDropdown" class="dropdown-content">
          MEGAMMAMAM;
          foreach ($this->typeNames as $key => $vale){
            $menu .= "<p onclick='typeValue($key)'>$vale</p>";
          }
          $menu .= '
                </div>
            </div>';
          foreach ($this->typeNames as $key => $vale){
            $menu .= "<ul class='menuList' type='$key'>
            <li subgenre=''>All</li>";
            foreach ($this->subgenresArr[$key] as $key2 =>$subgenre){
              $menu .= "<li subgenre='$key2'>$subgenre</li>";
            }
            if ($key == 1){
              $menu .= '
                <li>
                    <select id="sysDropdown" onchange="newSys(this.value);">
                        <option value="0">Any System</option>
                        <option value="1">5eS</option>
                        <option value="2">5e</option>
                    </select>
                </li>';
            }
            $menu .= "</ul>";
          }
        $menu .= '
        <form onsubmit="return goSearch(this)">
          <input type="text" class="" name="query" placeholder="Queries...">
          <button class=""><i class="fas fa-search"></i> Search</button>
        </form>
        </div>';
        return $menu;
    }
    function giveFooter() {
      return '<div w3-include-html="/ds/g/GFooter.html" w3-create-newEl="true"></div>';
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
      $genre = 0;
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

      $requ = 'SELECT * FROM products WHERE (name LIKE "%'.$query.'%" OR categories LIKE "%'.$query.'%") AND subgenre REGEXP "'.$regcate.'"';
      if (isset($queries["partner"])){$requ .= " AND partner = ".$queries["partner"];}
      if ($genre != 0){$requ .= " AND genre = ".$genre;}
      $requ .=  ' ORDER BY '.$method.' DESC LIMIT 222';
      if ($result = $this->dlconn->query($requ)){
        if (mysqli_num_rows($result) > 0){
          $nicetotal = 0;
          while ($row = $result->fetch_assoc()){
            if ($nicetotal >= $max){break;}
            if ($row["status"]!="active"){continue;}
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
              $resulter[] = ["name"=>$name, "link"=>$link, "image"=> $thumbnail, "id"=>$id];
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
    function prodItemR($row, $return = "tab") {
      $partner = $row["partner"];
      if (!isset($this->partners[$partner]) OR $this->partners[$partner]!="active") {return "";}
      if ($row["status"]!="active"){return "";}
      $titling = $row["name"];
      if($row["shortName"] != ""){$titling = $row["shortName"];}
      $image = $this->clearmage($row["image"]);
      $link = $this->url($row["id"], $titling);
      $premium = false;
      if ($row["tier"]!=0){$premium = true;}
      return $this->prodTab($titling, $image, $link, $premium);
    }


    function styles($dom = "dl") {
      $return = <<<MAGDA
        <meta charset="UTF-8" />
        <link rel="icon" href="/Imgs/Favicon.png">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
        <link rel="stylesheet" type="text/css" href="/ds/g/ds-g.css">
        <link rel="stylesheet" type="text/css" href="/dl/global/dl3.css">
      MAGDA;
      if ($dom == "p"){
        $return .= '<link rel="stylesheet" type="text/css" href="/ds/p/form.css">';
        $return .= '<link rel="stylesheet" type="text/css" href="/account/g/GGMdl2.css">';
      }
      return $return;
    }
    function scripts($dom = "dl") {
      $return = <<<MAGDA
        <script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
        <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
        <script src="/Code/CSS/global.js"></script>
      MAGDA;
      if ($dom == "dl"){
        $return .= '<script src="/dl/global/dl3.js"></script>';
      }
      else if ($dom == "p"){
        $return .= '<script src="/ds/p/form.js"></script>';
      }
      return $return;
    }
    function baseVars($genre = 1, $subgenre = "[]", $gsystem = 0) {
      $typeeme = json_encode($this->typeNames);
      return <<<MAGDA
      <script>
        var type = $genre;
        var categs = $subgenre;
        var sysNum = $gsystem;
        var typeNames = $typeeme;
      </script>
      MAGDA;
    }

    function checkOwner(int $prodId, int $partId = null) {
      if ($partId==null){$partId = $this->partId;}
      $query = "SELECT partner FROM products WHERE id = $prodId";
      if ($toprow = $this->dlconn->query($query)) {
          while ($row = $toprow->fetch_assoc()) {
              if ($row["partner"]==$partId){return true;}
          }
      }
      return false;
    }
    function hasBlogs() {
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_blogs.php");
      $query = "SELECT * FROM busers WHERE type = 'partnership' AND user = ".$this->user->user;
      if ($result = $blogconn->query($query)) {
        if (mysqli_num_rows($result) != 0) {
          return true;
        }
      }
      return false;
    }
    function url($id, $name) {
      return "/dl/item/".$id."/".substr(urlencode(str_replace(" ", "_", $name)), 0, 50);
    }
    function clearmage($image, $type = "indeximg") {
      if (!str_contains($image, "/")){
        if ($type == "pimg"){
          $image = "/dl/PartIm/".$image;
        }
        else {
          $image = "/IndexImgs/".$image;
        }
      }
      if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
        $image = "https://media.manyisles.ch".$image;
      }
      else {
        $image = "http://25.36.111.17:8080".$image;
      }
      return $image;
    }
    function fileclear($file, $genre, $direct = false) {
      if (!str_contains($file, "/")){
        if ($genre == 1){
          $file = "/dl/Friiz/".$file;
        }
        else if ($genre == 3){
          $file = "/dl/Art/".$file;
        }
      }
      if ($direct AND preg_match("/^\/.*$/", $file)){
        if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
          $file = "https://media.manyisles.ch".$file;
        }
        else {
          $file = "http://25.36.111.17:8080".$file;
        }
      }
      return $file;
    }
    function parsePartName($name) {
      if ($name == "Traveler"){$name = "a Traveler";}
      else if ($name == "Pantheon"){$name = "the Pantheon";}
      return $name;
    }
    function go($place, $dom = "dl") {
      if ($dom == "p"){$dom = "/account/";}
      else if ($dom == "dl"){$dom = "/dl/";}
      else if ($dom == "ds"){$dom = "/ds/p/";}
      echo "<script>window.location.replace('$dom$place');</script>";
      exit;
    }
}


?>
