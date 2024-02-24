<?php
require($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
if (!class_exists("adventurer")){
  class adventurer {
      use allBase;
      public $conn;
      public $user;
      public $usertag = "u#0";
      public $title = "Adventurer";
      public $uname = "Hansfried";
      public $fullName = "Adventurer Hansfried";
      public $email = "";
      public $discname = "";
      public $cpsw = "";
      public $tier = 0;
      public $region = 1;
      public $power = 1;
      public $signedIn = false;
      public $emailConfirmed = false;
      public $moderator = false;

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
          $conn = $this->addConn("accounts");
        }
        $this->conn = $conn;
        if ($user == ""){$user = 0;}
        $this->user = preg_replace("/[^0-9]/", "", $user);
        $this->signedIn = false;
        if ($this->user == null){$this->user = 0;}
        else if ($this->user != 0){
          $this->constructUInfo();
        }

      }
      function constructUInfo(){
        $query = "SELECT * FROM accountsTable WHERE id = '$this->user'";
        if ($result = $this->conn->query($query)) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $this->signedIn = true;
                    $this->title = $row["title"];
                    $this->tier = $row["tier"]; if ($row["tier"]=="g"){$this->tier = 0;}
                    $this->uname = $row["uname"];
                    $this->email = $row["email"];
                    $this->cpsw = $row["password"];
                    $this->power = $row["power"];
                    $this->region = $row["region"];
                    
                    $persInfo = json_decode($row["persInfo"], true);
                    if ($persInfo == null){$persInfo = ["fName" => "", "lName" => "", "references" => ["discName"=>""]];}
                    $this->persInfo = $persInfo;
                    $this->discname = $persInfo["references"]["discName"];

                    $this->usertag = "u#".$this->user;
                    if ($row["emailConfirmed"]==1){$this->emailConfirmed = true;}
                    if ($this->power > 3){$this->moderator = true;}
                }
            }
        }
        $this->fullName = $this->title." ".$this->uname;
      }

      function createAccount($uname, $email, $psw, $region) {
        $redirect = false;
        //check input
        if ($this->signedIn){$redirect = "accountExists";} //will auto-redirect to SignedIn after anyway
        else if (preg_match($this->regArrayR["account"], $uname)!=1){$redirect = "uname";}
        else if (preg_match("/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)!=1){$redirect = "email";}
        else if (preg_match("/[A-Za-z0-9!.\-_ ]{1,}/", $psw)!=1){$redirect = "psw";}
        else if (preg_match("/[1-3]/", $region)!=1){$redirect = "reg";}
        if (!$redirect){
          if (!$redirect){
            //check for duplicates
            if ($result = $this->conn->query(sprintf("SELECT email FROM accountsTable WHERE email='%s';", $email))) {
               if ($result->num_rows > 0) {
                 $redirect = "EmailTaken";
               }
            }
            if ($result = $this->conn->query(sprintf("SELECT uname FROM accountsTable WHERE uname='%s';", $uname))) {
               if ($result->num_rows > 0) {
                 $redirect = "UnameTaken";
               }
            }
            if (!$redirect){
              //dew it
              $hashedPsw = password_hash($psw, PASSWORD_DEFAULT);
              $sql = sprintf(
                "INSERT INTO accountsTable (uname, title, email, region, password) VALUES ('%s', 'Adventurer', '%s', %s, '%s');",
                $uname,
                $email,
                $region,
                $hashedPsw);
              $this->conn->query($sql);
              if ($this->signIn($uname, $psw)){
                $this->sendConfirmer(1);
              }
              else {
                $query = "DELETE FROM accountsTable WHERE uname = '".$uname."'";
                $this->conn->query($query);
                $redirect = "dataPlacing";
              }
            }
          }
        }
        if ($redirect){return $redirect;}
        return true;
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
              $this->constructUInfo();
              return true;
            }
          }
        }
        return false;
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
      function check($mod = false, $emailConfirmedMatters = false, $deadly = false) {
        $skip = false;
        if ($this->signedIn OR $mod){
          if ($emailConfirmedMatters){
            if (!$this->emailConfirmed){
              $skip = true;
            }
          }
          if ((isset($_COOKIE["loggedIn"])  OR !$skip) AND isset($_COOKIE["loggedCode"])){
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
          if ($deadly) {
            $this->go("/account/home?error=notSignedIn");
          }
          return false;
        }
        return true;
      }
      function modcheck($minpower = 4, $return="/account/home?show=credentials"){
        $this->check(true, true, true);
        if ($this->power < $minpower){
          $this->go($return);
        }
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

          $titlePos = array_search($title, array_keys($this->titleArr));
          $oldTitlePos = array_search($this->title, array_keys($this->titleArr));

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
      function signPrompt($back = "/account/home") {
        if (!$this->signedIn){
          $signPrompt = '<h3>Sign In</h3>
          <div id="signMeUpHere">Loading...</div>
          <p>Don\'t have an account? <a href="/account/home" target="_blank">Join us!</a></p>
          ';
          $signPrompt .= '
                    <script>
                    function acp_launcher() {
                      let acpBuilder = new acp_builder();
                      acpBuilder.createPortal(document.getElementById("signMeUpHere"), "signInLine");
                    }
                    </script>';
        }
        else {
            $signPrompt = "<h3>".$this->fullName."</h3>
            <p>Currently signed in with a tier ".$this->tier." account. <a href='/account/home' target='_blank'>View account</a></p>
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
          </div>
          ';
        return $signPormpt;
      }

      function newConfirmCode() {
        $query = "DELETE FROM confirmer WHERE id = $this->user";
        $this->conn->query($query);

        $conCode = $this->generateRandomString(7, 1);

        $query = "INSERT INTO confirmer (id, code) VALUES ($this->user, '$conCode')";
        if ($this->conn->query($query)){
          return $conCode;
        }
      }
      function sendConfirmer($mailType = 0) {
        $conCode = $this->newConfirmCode();

        //set subject and message
        if ($mailType == 1){
          $subject = "Welcome to the Many Isles!";
          $message = <<<MYGREATMAIL
          <!DOCTYPE html>
          <html>
          <head>
              <meta charset="utf-8" />
              <title></title>
          </head>
          <body style="padding:0;margin:0;">
            <section style="max-width: 900px; margin: auto;background-image:url(https://manyisles.ch/Imgs/OshBacc.png);background-color: #8dceff;background-attachment: fixed; background-size: contain;padding-top: 1px;">
              <div style="width: 100%;
              height: 200px;
              background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.5));"></div>
              <div style="background-color: white;padding: 10px 0;">
                <div style="width: 85%; margin:auto;">
                  <h1 style="font-size: 30px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;">Welcome to the Many Isles, %%UNAME%%!</h1>
                  <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                      We're happy you've decided to join us. You'll get free premium products, as well as access to our great community! We love worldbuilding and homebrewing, and you'll fit right in.
                  </p>
                </div>
                <!-- offer -->
                <div style="border-top: 3px solid #61b3dd;margin-top:50px">
                  <div style="width: 85%; margin:auto;">
                    <h2 style="font-size: 25px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;margin:20px 0 0;">What we offer</h2>
                    <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                        A selection of what you can find on the Many Isles.
                    </p>
                    <table>
                      <tbody style="vertical-align:top">
                        <tr style="padding-bottom:10px">
                          <td>
                            <img src="https://manyisles.ch/IndexImgs/Pen.png" alt="Hello There!" style="height:200px;width:200px;object-fit:cover;display:block;border-radius:15px;" />
                          </td>
                          <td style="padding-left: 10px">
                            <h3 style="font-size: 20px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;margin:0;">The Fandom Wiki</h2>
                            <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                                We host a cool <a style="color:#61b3dd" href="https://manyisles.ch/fandom/home">fandom wiki</a>, accessible to all. Once you've confirmed your email, you can participate your own articles and even write a whole wiki about your own world!
                            </p>
                          </td>
                        </tr>
                        <tr style="padding-bottom:10px">
                          <td>
                            <img src="https://manyisles.ch/Imgs/Prods.png" alt="Hello There!" style="height:200px;width:200px;object-fit:cover;display:block;border-radius:15px;" />
                          </td>
                          <td style="padding-left: 10px">
                            <h3 style="font-size: 20px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;margin:0;">An awesome Digital Library</h2>
                            <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                                Our main goal is to promote small creators that want to get their awesome free stuff out there. Check out our <a style="color:#61b3dd" href="https://manyisles.ch/dl/home">digital library</a> today, and start publishing from your <a style="color:#61b3dd" href="https://manyisles.ch/account/home">account page</a>!
                            </p>
                          </td>
                        </tr>
                        <tr style="padding-bottom:10px">
                          <td>
                            <img src="https://media.manyisles.ch/IndexImgs/Dark.png" alt="Hello There!" style="height:200px;width:200px;object-fit:cover;display:block;border-radius:15px;" />
                          </td>
                          <td style="padding-left: 10px">
                            <h3 style="font-size: 20px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;margin:0;">Handbook of Dark Secrets</h2>
                            <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                              A guide to possession, exorcism, the afterlife, demon forging, and fiends. It includes special feats, exclusive spells, unique magic items, and more infernal secrets.<br>
                              <a style="color:#61b3dd" href="https://manyisles.ch/dl/item/8/Handbook_of_Dark_Secrets">Check it out!</a>
                            </p>
                          </td>
                        </tr>
                        <tr style="padding-bottom:10px">
                          <td>
                            <img src="https://manyisles.ch/Imgs/slides/blogs.png" alt="Hello There!" style="height:200px;width:200px;object-fit:cover;display:block;border-radius:15px;" />
                          </td>
                          <td style="padding-left: 10px">
                            <h3 style="font-size: 20px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;margin:0;">Many Isles Blogs</h2>
                            <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                                View and publish awesome posts about fantasy with our <a style="color:#61b3dd" href="https://manyisles.ch/blog/explore">blog tool</a>.
                            </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- confirm -->
                <div style="border-top: 3px solid #61b3dd;margin-top:50px">
                  <div style="width: 85%; margin:auto;">
                    <h2 style="font-size: 25px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;margin:20px 0 0;">Confirm Email</h2>
                    <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                      By clicking the button below, you'll confirm your email unlock the many features of the Many Isles!
                    </p>
                    <button class="popupButton" style="margin:2vw auto 2vw;padding:10px;display:block;background-color:#61b3dd;border:0px;border-radius:4px;font-weight:bold;color:white;font-size:20px;"><a href="https://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife" style="text-decoration:none;color:white;">Confirm and Join</a></button>
                    <p style="color: #7d7d7d; padding:10px;font-size: 14px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">If the button does not work, try moving the email out of your spam folder, or paste this link into your browser: <a href="https://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife" style="color:#61b3dd">https://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife</a> </p>
                  </div>
                </div>
                <!-- footer -->
                <div style="border-top: 3px solid #61b3dd;text-align:center;margin-top:200px">
                  <img src="https://manyisles.ch/Imgs/branding/s/community.png" alt="Many Isles logo" style="width:250px;margin:30px auto; display:block;" />
                  <a href="https://manyisles.ch" style="color:#61b3dd;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">manyisles.ch</a>
                </div>
              </div>
            </section>
          </body>
          </html>
          MYGREATMAIL;
        }
        else {
          $subject = "Confirm Email";
          $message = <<<MYGREATMAIL
          <!DOCTYPE html>
          <html>
          <head>
              <meta charset="utf-8" />
              <title></title>
          </head>
          <body style="padding:0;margin:0;">
            <section style="max-width: 900px; margin: auto;background-image:url(https://manyisles.ch/Imgs/OshBacc.png);background-color: #8dceff;background-attachment: fixed; background-size: contain;padding-top: 1px;">
              <div style="width: 100%;
              height: 200px;
              background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.5));"></div>
              <div style="background-color: white;padding: 10px 0;">
                <div style="width: 85%; margin:auto;">
                  <h1 style="font-size: 30px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;">Confirm Email</h1>
                  <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                      Click the button below to confirm your Many Isles email, %%UNAME%%.
                      <br>You may receive this message due to having changed your account's email address, or having requested a new code from your account page.
                  </p>
                  <button class="popupButton" style="margin:2vw auto 2vw;padding:10px;display:block;background-color:#61b3dd;border:0px;border-radius:4px;font-weight:bold;color:white;font-size:20px;"><a href="https://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife" style="text-decoration:none;color:white;">Confirm and Join</a></button>
                  <p style="color: #7d7d7d; padding:10px;font-size: 14px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">If the button does not work, try moving the email out of your spam folder, or paste this link into your browser: <a href="https://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife" style="color:#61b3dd">https://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife</a> </p>
                </div>
                <!-- footer -->
                <div style="border-top: 3px solid #61b3dd;text-align:center;margin-top:200px">
                  <img src="https://manyisles.ch/Imgs/branding/s/community.png" alt="Many Isles logo" style="width:250px;margin:30px auto; display:block;" />
                  <a href="https://manyisles.ch" style="color:#61b3dd;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">manyisles.ch</a>
                </div>
              </div>
            </section>
          </body>
          </html>
      MYGREATMAIL;
        }
        $message = str_replace("%%UNAME%%", $this->uname, $message);
        $message =  str_replace("massiveTreeofLife", urlencode($conCode), $message);
        $message = str_replace("https://media.manyisles.ch", $this->giveServerInfo("servername_media"), $message);
        $message = str_replace("manyisles.ch", $this->giveServerInfo("servername"), $message); //this can do bugs: should have better regex, not just all "manyisles.ch"

        $mailer = $this->addMailer();
        if ($mailer->sendMail([[$this->email, $this->fullName]], $subject, $message)){
          return true;
        }
      }
  }
}


?>
