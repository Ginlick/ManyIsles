<?php

class dlengine {
    public $productTab = '
           <div class="container">
                <a  href="View.php?id=SENDMETOTHEPRODUCT">
                    <div class="imgCont" load-image="/IndexImgs/GREATINDEXIMAGE">
                    </div>
                    <div class="titling">GRANDTITLE</div>
                </a>
            </div>
    ';
    public $conn;
    public $dlconn;

    function __construct($conn, $dlconn = null){
        $this->conn = $conn;
        if ($dlconn == null) {
            require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_dl.php");
        }
        $this->dlconn = $dlconn;
        require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
        $key = 0; if (isset($_COOKIE["loggedIn"])){$key = $_COOKIE["loggedIn"];}
        $this->user = new adventurer($this->conn, $key);
    }

    //page generating
    function giveSearch() {
      if (!$this->user->signedIn){
        $signPrompt = '<h3>Sign In</h3>
        <form action="/account/SignIn.php" method="POST" onsubmit="seekMaker()" style="text-align:center">
          <label for="loguname"><b>Username</b></label>
          <input type="text" placeholder="Hansfried Dragonslayer" name="uname" id="loguname" autocomplete="username" required>
          <label for="logpassword"><b>Password</b></label>
          <input type="password" placeholder="uniquePassword22" name="psw" id="logpassword" autocomplete="current-password" required>
          <button><i class="fas fa-arrow-right"></i> Sign In</button>
        </form> ';
      }
      else {
          $signPrompt = "<h3>".$this->user->fullName."</h3>
          <p>Currently signed in with a tier ".$this->user->tier." account.</p>";
      }
        $search = '
        <div class="topBlock">
            <div class="accnterCont">
                <div class="logoRound">
                    <div class="roundling">
                      <img src="'.$this->user->image(2).'" />
                    </div>
                    <div class="accntInfoCont">
                      '.$signPrompt.'
                    </div>
                </div>
            </div>
            <a href="home"><h1 >Digital Library</h1></a>
            <form action="Search" class="searchForm" method="GET">
                <input type="text" id="mySearch" class="search" name="query" placeholder="Search.." autocomplete="off" oninput="suggestNow();" onfocusout="killSugg();" onfocus="suggestNow();">
                <div class="icon" onclick="shoBar()">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="suggestions" id="suggestions" style="display:none;"></div>
                <button class="searchButton"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>';
        return $search;
    }
    function giveMenu() {
        $menu = <<<MEGAMMAMAM
        <div class="menuCont">
            <h3>Browse</h3>
            <div class="dropdown">
                <button onclick="dropdown()" class="dropbtn" id="showType">Modules</button>
                <div id="myDropdown" class="dropdown-content">
                    <p onclick="typeValue('module')">Modules</p>
                    <p onclick="typeValue('diggie')">Tools</p>
                    <p onclick="typeValue('art')">Art</p>
                </div>
            </div>
            <ul id="moduleMenu" class="menuList">
                <li onclick="resetAll()" class="active" id="gen">General</li>
                <li onclick="clinnation('charop')" style="color:black" id="charop">Classes</li>
                <li onclick="clinnation('race')" style="color:black" id="race">Races</li>
                <li onclick="clinnation('rule')" style="color:black" id="rule">Rules</li>
                <li onclick="clinnation('adventure')" style="color:black" id="adventure">Adventures</li>
                <li onclick="clinnation('lore')" style="color:black" id="lore">Lore</li>
                <li onclick="clinnation('dms')" style="color:black" id="dms">DM Stuff</li>
                <li>
                    <select id="sysDropdown" onchange="newSys(this.value);">
                        <option value="0">Any System</option>
                        <option value="1">5eS</option>
                        <option value="2">5e</option>
                    </select>
                </li>
            </ul>

            <ul id="diggieMenu" class="menuList" style="display:none">
                <li onclick="resetAll()" style="color:white" id="genTu">General</li>
                <li onclick="clinnation('hmbrw')" style="color:black" id="hmbrw">Homebrewing</p></li>
                <li><p onclick="clinnation('genr')" style="color:black" id="genr">Generator</p></li>
                <li><p onclick="clinnation('indx')" style="color:black" id="indx">Index</p></li>
            </ul>

            <ul id="artMenu" class="menuList" style="display:none">
                <li><p onclick="resetAll()" style="color:white" id="genTri">General</p></li>
                <li><p onclick="clinnation('vis')" style="color:black" id="vis">Visual</p></li>
                <li><p onclick="clinnation('cart')" style="color:black" id="cart">Cartography</p></li>
                <li><p onclick="clinnation('dun')" style="color:black" id="dun">Dungeons</p></li>
            </ul>
            <input type="text" class="" name="query" placeholder="Queries..." autocomplete="off" oninput="suggestNow();" onfocusout="killSugg();" onfocus="suggestNow();">
            <button class=""><i class="fas fa-search"></i> Search</button>
        </div>
        MEGAMMAMAM;
        return $menu;
    }

    //action
    function prodTab($titling, $image, $link, $premium = false) {
        $producttab = str_replace("GREATINDEXIMAGE", $image, $this->productTab);
        $producttab = str_replace("GRANDTITLE", $titling, $producttab);
        $producttab = str_replace("SENDMETOTHEPRODUCT", $link, $producttab);
        if ($premium == true){$producttab = str_replace("class='titling'", "class='titling premium'", $producttab);}
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
                $titling = $row["name"];
                if($row["shortName"] != ""){$titling = $row["shortName"];}
                $image = $row["image"];
                $link = $row["id"];
                $premium = false;
                if ($row["tier"]!=0){$premium = true;}
                $return .= $this->prodTab($titling, $image, $link, $premium);
            }
        }
        return $return;
    }
    function prodItem(int $id){
        $query = "SELECT * FROM products WHERE id = $id";
        if ($toprow = $this->dlconn->query($query)) {
            while ($row = $toprow->fetch_assoc()) {
                $titling = $row["name"];
                if($row["shortName"] != ""){$titling = $row["shortName"];}
                $image = $row["image"];
                $link = $row["id"];
                $premium = false;
                if ($row["tier"]!=0){$premium = true;}
                return $this->prodTab($titling, $image, $link, $premium);
            }
        }
    }

}


?>
