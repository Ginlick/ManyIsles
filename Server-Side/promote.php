<?php

class adventurer {
    public $conn;
    public $user;
    public $title = "Adventurer";
    public $uname = "Hansfried";
    public $cpsw = "";
    public $tier = 0;
    public $signedIn = false;

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

    function __construct($conn, $user) {
        $this->conn = $conn;
        $this->user = $user;

        $query = "SELECT title, tier, uname, password FROM accountsTable WHERE id = $user";
        if ($result = $this->conn->query($query)) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $this->signedIn = true;
                    $this->title = $row["title"];
                    $this->tier = $row["tier"]; if ($row["tier"]=="g"){$this->tier = 0;}
                    $this->uname = $row["uname"];
                    $this->cpsw = $row["password"];
                }
            }
        }
        $this->fullName = $this->title." ".$this->uname;
    }

    function check() {
      if ($this->signedIn){
        require($_SERVER['DOCUMENT_ROOT']."/Server-Side/checkPsw2.php");
        return checkNudePsw($this->cpsw);
      }
      return true;
    }
    function promote($title){
        if (preg_match("/[0-9]*/", $title)) {
            foreach ($this->titleArr as $key => $tier){
                if ($title == $tier){$title = $key;break;}
            }
        }
        $tier = $this->titleArr[$title];
        $query = "UPDATE accountsTable SET title = '$title', tier = $tier WHERE id = $this->user";
        if ($this->conn->query($query)){return true;}
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
}

?>
