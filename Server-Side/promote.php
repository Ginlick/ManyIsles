<?php
require($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
class adventurer {
    use allBase;
    public $conn;
    public $user;
    public $title = "Adventurer";
    public $uname = "Hansfried";
    public $email = "";
    public $discname = "";
    public $cpsw = "";
    public $tier = 0;
    public $signedIn = false;
    public $emailConfirmed = false;

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
        $query = "SELECT * FROM accountsTable WHERE id = $user";
        if ($result = $this->conn->query($query)) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $this->signedIn = true;
                    $this->title = $row["title"];
                    $this->tier = $row["tier"]; if ($row["tier"]=="g"){$this->tier = 0;}
                    $this->uname = $row["uname"];
                    $this->email = $row["email"];
                    $this->discname = $row["discname"];
                    $this->cpsw = $row["password"];
                    if ($row["emailConfirmed"]==1){$this->emailConfirmed = true;}
                }
            }
        }
        $this->fullName = $this->title." ".$this->uname;
    }

    function signIn($subUname, $subPsw) {
      $subUname = str_replace("'", "", $subUname);
      $query = "SELECT id, password FROM accountsTable WHERE uname = '".$subUname."'";
      if ($userrow = $this->conn->query($query)){
        if ($userrow->num_rows == 1) {
          if ($row = $userrow->fetch_assoc()){
            if (!password_verify($subPsw, $row["password"])){return false;}

            $this->user = $row["id"];
            $code = $this->generateRandomString(22);
            $query = "DELETE FROM signCodes WHERE (reg_date < now() - interval 22 DAY) AND user = ".$this->user; $this->conn->query($query);
            $query = "INSERT INTO signCodes (user, code) VALUES ('$this->user', '$code')"; $this->conn->query($query);
            setcookie("loggedIn", $this->user, time()+1900800, "/");
            setcookie("loggedCode", $code, time()+1900800, "/");
            $this->signedIn = true;
            return true;
          }
        }
      }
      return false;
    }
    function check($mod = false, $emailConfirmedMatters = false) {
      if ($this->signedIn OR $mod){
        if ($emailConfirmedMatters){
          if (!$this->emailConfirmed){
            return false;
          }
        }
        if (isset($_COOKIE["loggedIn"]) AND isset($_COOKIE["loggedCode"])){
          $code = $_COOKIE["loggedCode"];
          $query = "SELECT * FROM signCodes WHERE user = $this->user";
          if ($result = $this->conn->query($query)) {
            if (mysqli_num_rows($result) > 0) {
              while ($row = $result->fetch_assoc()) {
                if ($code == $row["code"]){return true;}
              }
            }
          }
        }
        $this->signOut();
        return false;
      }
      return true;
    }
    function checkInputPsw($psw) {
      if (password_verify($psw, $this->cpsw)){return true;}
      return false;
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
    function signPrompt($back = "/account/Account") {
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
    function signOut() {
      $query = "DELETE FROM signCodes WHERE user = ".$this->user;
      if (isset($_COOKIE["loggedCode"])){
        $loggedCode = $this->purify($_COOKIE["loggedCode"], "quotes");
        $query.= " AND code = '$loggedCode'";
      }
      $this->conn->query($query);
      setcookie("loggedIn", "", time()-222222, "/");
      setcookie("loggedCode", "", time()-222222, "/");
      $this->signedIn = false;
    }
}

?>
