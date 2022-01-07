<?php

class adventurer {
    public $conn;
    public $user;
    public $title = "Adventurer";
    public $uname = "Hansfried";
    public $cpsw = "";
    public $tier = 0;
    public $signedIn = false;
    public $emailConf = false;

    public $titleArr = [
            "Adventurer" => 0, "Poet" => 0, "Trader" => 0, "Journeyman" => 0,
            "Imperial Soldier" => 1, "Dungeon Master" => 1, "Royal Valkyrie" => 1, "Knight" => 1,
            "Loremaster" => 2, "Grand Wizard" => 2, "High Merchant" => 2,
            "Grand Poet" => 3, "Legendar" => 3, "Master Architect" => 3, "Avatar" => 3,
            "Younger God" => 4, "Elder God" => 4
    ];
    public $bannerTArr = [
        0 => "Adventurer.png", 1 => "ImperialSoldier.png", 2 => "ImperialSoldier.png", 3 => "Legendar.png"
    ];
    public $bannerSArr = [
        "Poet" => "Poet.png", "Trader" => "Trader.png", "Royal Valkyrie" => "RoyalValkyrie.png", "Loremaster" => "Loremaster.png", "Grand Wizard" => "GrandWizard.png", "High Merchant" => "HighMerchant.png", "Grand Poet" => "GP.png"
    ];

    function __construct($conn = null, $user = null) {
        if ($user == null){
          if (isset($_COOKIE["loggedIn"])){
            $user = $_COOKIE["loggedIn"];
          }
        }
        if ($conn == null){
          include($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
        }
        $this->conn = $conn;
        $this->user = preg_replace("/[^0-9]/", "", $user);
        $this->signedIn = false;
        if ($this->user == null){$this->user = 0;}

        $query = "SELECT title, tier, uname, password, emailConfirmed FROM accountsTable WHERE id = $user";
        if ($result = $this->conn->query($query)) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $this->signedIn = true;
                    $this->title = $row["title"];
                    $this->tier = $row["tier"]; if ($row["tier"]=="g"){$this->tier = 0;}
                    $this->uname = $row["uname"];
                    $this->cpsw = $row["password"];
                    if ($row["emailConfirmed"]==1){$this->emailConfirmed = true;}
                }
            }
        }
        $this->fullName = $this->title." ".$this->uname;
    }

    function check($mod = false) {
      if ($this->signedIn OR $mod){
        require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw2.php");
        return checkNudePsw($this->cpsw);
      }
      return true;
    }
    function checkInputPsw($psw) {
      require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw2.php");
      return checkInputPsw($psw, $this->cpsw);
    }
    function promote($title){
        if (preg_match("/[0-9]*/", $title)) {
            foreach ($this->titleArr as $key => $tier){
                if ($title == $tier){$title = $key;break;}
            }
        }
        $tier = $this->titleArr[$title];

        $titlePos = array_search($title, $this->titleArr);
        $oldTitlePos = array_search($this->title, $this->titleArr);

        if ($titlePos > $oldTitlePos){
          $query = "UPDATE accountsTable SET title = '$title', tier = $tier WHERE id = $this->user";
          if ($this->conn->query($query)){return true;}
        }
        return true;
    }
    function image($x = 1) {
        if (!$this->signedIn){
          return "/Imgs/doms/acc.png";
        }
        $image = "";
        if (isset($this->bannerSArr[$this->title])){
            $image = $this->bannerSArr[$this->title];
        }
        else {
          $image = $this->bannerTArr[$this->tier];
        }
        if ($x==1) {
          return "/Imgs/Ranks/".$image;
        }
        else if ($x == 2){
          return "/Imgs/Ranks/single/".$image;
        }
    }
    function signPrompt($back = "/dl/home") {
      if (!$this->signedIn){
        $signPrompt = '<h3>Sign In</h3>
        <form action="/account/SignIn.php?back='.$back.'" method="POST" onsubmit="seekMaker()" style="text-align:center">
          <label for="loguname"><b>Username</b></label>
          <input type="text" placeholder="Hansfried Dragonslayer" name="uname" id="loguname" autocomplete="username" required>
          <label for="logpassword"><b>Password</b></label>
          <input type="password" placeholder="uniquePassword22" name="psw" id="logpassword" autocomplete="current-password" required>
          <button class="wikiButton"><i class="fas fa-arrow-right"></i> Sign In</button>
        </form>
        <p>Don\'t have an account? <a href="/account/Account?add=dl">Join us</a></p>
        ';
      }
      else {
          $signPrompt = "<h3>".$this->fullName."</h3>
          <p>Currently signed in with a tier ".$this->tier." account. <a href='/account/SignedIn' target='_blank'>View account</a></p>
          <button onclick='signOut();' class='wikiButton'><i class='fas fa-arrow-right'></i> Sign Out</button>
          ";
      }
      $signPormpt = '
          <div class="logoRound">
              <div class="roundling">
                <img src="'.$this->image(2).'" />
              </div>
              <div class="accntInfoCont">
                <div class="accntInfoInCont">
                  '.$signPrompt.'
                </div>
              </div>
          </div>';
      return $signPormpt;
    }
}

?>
