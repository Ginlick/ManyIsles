<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
class dsEngine {
  public $moneyconn;
  public $inbasket;
  public $type = "items"; //either items or subs
  private $ds_info = [];
  use allBase;
  use allDStools;

  function __construct($meaninglessParam = true) {
    require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");
    $this->moneyconn = $moneyconn;
    require($_SERVER['DOCUMENT_ROOT']."/ds/g/countries.php");
    $this->countries = $countries;

    $this->construct(); //allBase construct
    $this->userInformation();
    $this->updateBasket();

    //get info
    $this->ds_info = $this->giveServerInfo("ds");
  }

  function userInformation() {
    require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
    $this->user = new adventurer;
    $this->user->check();
    $this->user->dsHasordered = false; $this->user->dsHassubs = false;

    if ($this->user->signedIn){
      $query = "SELECT ud FROM dsorders WHERE buyer = ".$this->user->user;
      if ($result = $this->conn->query($query)) {
        if (mysqli_num_rows($result) != 0) {
          $this->user->dsHasordered = true;
        }
      }
      $query = "SELECT id FROM ds_asubs WHERE buyer = ".$this->user->user;
      if ($result = $this->moneyconn->query($query)) {
        if (mysqli_num_rows($result) != 0) {
          $this->user->dsHassubs = true;
        }
      }
    }
  }
  function partnerCheck($deadly = false) {
    $this->user->check(true, false, true);
    $query = 'SELECT * FROM partners WHERE account = "'.$this->user->uname.'"';
    if ($firstrow = $this->conn->query($query)) {
        while ($row = $firstrow->fetch_assoc()) {
          $this->partnerId = $row["id"];
          $this->partnerStatus = $row["status"];
          //eventually: check if status = active?
        }
    }
    if (!isset($this->partnerId)){
      if ($deadly){
        $this->go("/account/BePartner");
      }
      return false;
    }
    return true;
  }

  function updateBasket() {
    //input preparation
    if ( session_status() !== PHP_SESSION_ACTIVE ) {session_start();}
    if (!isset($_SESSION["basket"])){$_SESSION["basket"]="";}

    if (isset($_POST["creditAmount"])) {if (preg_match("/[0-9]{1,}$/", $_POST['creditAmount'])!=1){$this->go("store");} }
    if (isset($_POST["supportPair"])) {if (preg_match("/[(]+[a-zA-Z0-9' ]+\/[0-9]+[)]$/", $_POST['supportPair'])!=1){$this->go("store?2=".$_POST['supportPair']);} }
    if (isset($_POST["orderDetails"])) {if (preg_match("/[^a-zA-Z0-9:', ]/", $_POST['orderDetails'])==1){$this->go("store");} }
    if (isset($_POST["specified"])) {if (preg_match("/[0-9]{1}$/", $_POST['specified'])!=1){$this->go("store");} }
    //make changes to item basket
    $inbasket = explode(",", $_SESSION["basket"]);
    if (isset($_POST["basketing"]) AND count($inbasket) < 11) {
        if (preg_match("/[^0-9]/", $_POST['basketing'])===1){$this->go("/ds/store?w=3");session_destroy();exit();}

        if (isset($_POST["quickBuy"])) {
            if ($_POST["basketing"] == 2){array_push($inbasket, "2-1000");}
            else if ($_POST["basketing"] == 3){array_push($inbasket, "3(the Pantheon/500)");}
            else {array_push($inbasket, $_POST["basketing"]."[]");}
        }
        else if (isset($_POST["orderDetails"])){
            $insertTo = str_replace(",", "-", $_POST['orderDetails']);
            $topush = $_POST["basketing"]."[".$insertTo."]";
            array_push($inbasket, $topush);
        }
        else if (isset($_POST["supportPair"])) {
            $topush = $_POST["basketing"].$_POST["supportPair"];
            array_push($inbasket, $topush);
        }
        else if (!isset($_POST["specified"]) AND !isset($_POST["creditAmount"])){
            array_push($inbasket, $_POST["basketing"]);
        }
        else if (isset($_POST["creditAmount"])) {
            if ($_POST["creditAmount"]>10000){$creditAmount = 10000;}else {$creditAmount = $_POST["creditAmount"];}
            $topush = $_POST["basketing"]."-".$creditAmount;
            array_push($inbasket, $topush);
        }
        $_SESSION["basket"] = implode(",", $inbasket);
        unset($_SESSION["subbasket"]);
    }
    if (isset($_SESSION["subbasket"])) {$inbasket = $_SESSION["subbasket"];$this->type="subs";}

    $this->inbasket = $inbasket;
    $this->basketed = new loopBasket;
    $this->basketed->loopBasket($this->conn, $this->inbasket, true, false, false, $this->type);

    //redirect
    if (isset($_POST["goTo"])) {
        if ($_POST["goTo"] != "nope" AND $_POST["goTo"] != "" ){
            $this->go($_POST["goTo"]);
        }
    }
  }
  function sideBasket() {
    $fullText = "";
    $basketed = $this->basketed;

    if (str_contains($_SERVER['REQUEST_URI'], "/store")) {
      if ($this->user->dsHasordered){
        $fullText .= "<ul class='myMenu'><li><a class='Bar' href='/account/home?display=orders' target='_blank'>My Orders</a></li></ul>";
      }
      if ($this->user->dsHassubs) {
        $fullText .= "<ul class='myMenu'><li><a class='Bar' href='subs/hub'>My Subscriptions</a></li></ul>";
      }
    }
    if ((isset($_SESSION["basket"]) AND $_SESSION["basket"] != "" AND $basketed->type == "items") OR (isset($_SESSION["subbasket"]) AND $_SESSION["subbasket"] != "" AND $basketed->type == "subs")){
        $fullText .= '
        <div class="toBeHidden">
        <img src="/Imgs/Bar2.png" alt="GreyBar" class="separator">
        <a href="/ds/basket"><h3 class="basketTitle">Basket</h3></a>
        <table class="basketTable">
            <tbody>';

        if ($basketed->type == "subs"){
            foreach ($basketed->itemArray as $item) {
                $fullText .= "<tr>";
                $fullText .= '<td><img src="'.$item["row"]["image"].'" alt="thumbnail" /></td>';
                $fullText .= '<td>'.$item["row"]["shortName"].'</td>';
                $fullText .= '<td>'.$this->makeHuman($item["price"]).'</td>';
                $fullText .= "</tr>";
            }
        }
        else {
            foreach ($basketed->itemArray as $item) {
                $name = $item["row"]["name"];
                if ($item["row"]["shortname"] != null) {$name = $item["row"]["shortname"];}
                $link = $this->linki($item["row"]["id"], $item["row"]["link"], $item["row"]["name"]);
                $fullText .= "<tr>";
                $fullText .= '<td><img src="'.$this->clearImgUrl($item["row"]["thumbnail"]).'" alt="thumbnail" /></td>';
                $fullText .= '<td><a href="'.$link.'">'.$name.'</a></td>';
                $fullText .= '<td>'.$this->makeHuman($item["price"]).'</td>';
                $fullText .= "</tr>";
            }
        }
        $fullText .= '
                <tr>
                    <td></td>
                    <td><b>Subtotal</b></td>
                    <td><b>'.$this->makeHuman($basketed->totalPrice).'</b></td>
                </tr>';
        $fullText .= '
            </tbody>
        </table>';

        if (strpos($_SERVER['REQUEST_URI'], "checkout")===false AND strpos($_SERVER['REQUEST_URI'], "basket")===false) {
               $fullText .= '         <div class="checkoutBox" style="margin:2vw auto">
                            <a href="/ds/checkout1.php">
                            <button class="checkout">
                                <i class="fas fa-arrow-right"></i>
                                <span>Checkout</span>
                            </button>
                            </a>
                        </div>
                        </div>';
            //mobile
            $bottomad = '
                <div class="bottomad-container">
                    <a href="/ds/basket.php">
                    <div class="bottomad">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    </a>
                </div>';
            if ($basketed->prodNum > 0) {
                $bottomad = str_replace("</i>", '</i><div class="bottomadProdnum">'.$basketed->prodNum.'</div>', $bottomad);
            }
            $fullText .= $bottomad;
        }
        else {
            $fullText .= "</div>";
        }
    }
    return $fullText;
  }
  function giveHead() {
    $text =  <<<MAXX
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/diltou.css">
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-g-2.css">
    MAXX;
    return $text;
  }

  function fetchAddress($id = 0) {
    if ($id == 0) {$id = $this->user->user;}
    $address = ["exists"=>false];
    $query = "SELECT * FROM address WHERE id = ".$id;
    if ($firstrow = $this->conn->query($query)) {
        while ($row = $firstrow->fetch_assoc()){
            $address["exists"] = true;
            $address["fullname"] = $row["fullname"];
            $address["address"] = $row["address"];
            $address["city"] = $row["city"];
            $address["zip"] = $row["Zip"];
            $address["country"] = $row["Country"];
        }
    }
    if ($id == $this->user->user){
      $this->address = $address;
    }
    return $address;
  }
  function shipping() {
    $totalShipping = 0;
    if ($this->basketed->type == "items") {
      $this->fetchAddress();
      $country = $this->address["country"];

      foreach ($this->basketed->itemArray as $item){
        $totalShipping += $this->itemShipping($item, $country);
      }
    }
    return $totalShipping;
  }

  //article tab generation
  function makeArtTab($row, $itemNumArray = [], $showNoStock = false){
    if ($row["status"] != "deleted"){
      $query = "SELECT status FROM partners WHERE id = ".$row["sellerId"];
      $clearPartner = true;
      if ($result = $this->conn->query($query)){
          if (mysqli_num_rows($result) == 0) { $clearPartner = false; }
          while ($nRow = $result->fetch_assoc()) {
              if ($nRow["status"]!="active"){$clearPartner = false; }
          }
      }
      if ($clearPartner) {
          $articleId = $row["id"];
          $canBuy = true;
          $hasStock = false;

          if ($totalStock = $this->hasAnyStock($row["specifications"], $row["stock"])){
            if (isset($itemNumArray[$articleId])){
              if ($totalStock - $itemNumArray[$articleId] > 0) {
                $hasStock = true;
              }
            }
            else {
              if ($totalStock != 0){$hasStock = true; }
            }
          }

          if ($hasStock or $showNoStock){
            if ($row["status"]=="paused" ) {$canBuy = false;}
            $titling = $row["name"];
            $thumbnail = $this->clearImgUrl($row["image"]);
            $link = $this->linki($row["id"], $row["link"], $row["shortname"]);
            $price = $row["price"];
            if (stripos($price, ",")){
                $price = substr($price, 0, stripos($price, ","));
            }
            if ($price == 0) {$humPrice = "Special Price";}
            else {
                if ($row["specifications"] == ""){if ($row["minPrice"] > $price) { $price = $row["minPrice"];}$humPrice = $this->makeHuman($price);}
                else {
                    $specsArray = json_decode($row["specifications"], true);
                    foreach ($specsArray as $specArray){
                        $price += intval($specArray["options"][0]["price"]);
                    }
                    if ($row["minPrice"] > $price) { $price = $row["minPrice"];}
                    $humPrice = $this->makeHuman($price)." +";
                }
            }

            if ($row["viewImgs"] != NULL){
                $viewImgArray = explode (",", $row["viewImgs"]);
                $altImg = $this->clearImgUrl($viewImgArray[0]);
            } else {$altImg = $thumbnail;}

            $itemTab = $this->itemStencil;
            if ($hasStock == false){$canBuy = false;}
            if (!$canBuy){
                $itemTab = str_replace('onclick="purchase(ITEMID)', 'onclick="createPopup(\'d:dsp;txt:Item cannot be purchased\');', $itemTab);
                $itemTab = str_replace('checkout homescreen', "checkout homescreen grey", $itemTab);
            }
            $itemTab = str_replace("MEGALINK", $link, $itemTab);
            $itemTab = str_replace("MEGATHUMBNAIL", $thumbnail, $itemTab);
            $itemTab = str_replace("MEGAVIEWIMG", $altImg, $itemTab);
            $itemTab = str_replace("MEGANAME", $titling, $itemTab);
            $itemTab = str_replace("MEGAPRICE", $humPrice, $itemTab);
            $itemTab = str_replace("ITEMID", $articleId, $itemTab);
            if ($canBuy OR $showNoStock) {
                return $itemTab;
            }
          }
        }
      }
  }
  public $itemStencil = <<<NABSDAI
          <div class="artContainer">
          <div class="incontainer">
          <a href="MEGALINK">
              <div class="imagCont">
                  <div class="artSquare" onmouseover="showView(this.children[1], 'MEGAVIEWIMG');" onmouseout="showView(this.children[1], 'MEGATHUMBNAIL');">
                      <div class="hoverDiv"><span>View</span></div>
                      <img src="MEGATHUMBNAIL" alt="Thumbnail" class="linkim">
                  </div>
              </div>
              <div class='titling'>MEGANAME <hr class="solid"> <span class="price">MEGAPRICE</span><br></div>
          </a>
              <button class="checkout homescreen" onclick="purchase(ITEMID)"><i class="fas fa-shopping-basket"></i><span> Basket</span></button>
          </div>
          </div>
  NABSDAI;

  function makeAddressList($addressArray = "", $showTitle = false) {
    if ($addressArray == ""){
      $addressArray = $this->fetchAddress();
    }
    $addressArray = $this->parseAddressList($addressArray);


    $return = '
    <ul class="address">';
    if ($showTitle) {
      $return .= '<li class="pHeader">Address</li>';
    }
    foreach ($addressArray as $key => $line){
      $return .= "<li>".$line."</li>";
    }
    $return .= "</ul>";
    return $return;
  }
  function parseAddressList($addressArray){
    if (gettype($addressArray)=="string") {
      $addressArray = explode(";", $addressArray);
    }
    $resultArray = [];
    foreach ($addressArray as $key => $line){
      if (isset($this->countries["GLO"][$line])){
        $resultArray["country"] = $this->countries["GLO"][$line]." (".$line.")";
      }
      else {
        $resultArray[$key] = $line;
      }
    }
    return $resultArray;
  }

  //stock-related functionality
  function alertStock($stock, $giveStatus = false){
      if ($stock > 8    ) {
          $span = "<span style='color:#2a7d14'>$stock</span>";
          $status = 2;
      }
      else if ($stock >= 3) {
          $span = "<span style='color:black'>$stock</span>";
          $status = 1;
      }
      else {
          $span = "<span style='color:#cf2715'>$stock</span>";
           $status = 0;
      }

      if ($giveStatus){
          return $status;
      }
      else {
          return $span;
      }
  }
  function alertStatus($status){
      if ($status == "active") {
          $span = "<span style='color:var(--ds-status-green)'>$status</span>";
      }
      else if ($status == "paused") {
          $span = "<span style='color:var(--ds-status-blue)'>$status</span>";
      }
      else {
          $span = "<span style='color:black'>deleted</span>";
      }
      return $span;
  }
  function hasAnyStock($specs, $baseStock){
    if (gettype($specs)=="string"){$specs = json_decode($specs, true);}
    $smartstock = false;
    $totalStock = 0;
    foreach ($specs as $spec){
      if (isset($spec["smartstock"]) AND $spec["smartstock"] == 1){
        $smartstock = true;
        foreach ($spec["options"] as $option){
          if (isset($option["stock"])){
            $totalStock += $option["stock"];
          }
        }
      }
    }
    if (!$smartstock){
      $totalStock = $baseStock;
    }
    return $totalStock;
  }

  function give_actcode() {
    return $this->ds_info["actcode"];
  }
  function give_stripe_pk() {
    return $this->ds_info["stripe"]["pk"];
  }
  function give_stripe_sk() {
    return $this->ds_info["stripe"]["sk"];
  }
  function give_stripe_whsec() {
    return $this->ds_info["stripe"]["whsec"];
  }
}


class loopBasket {
    public $itemArray = [];
    public $totalCredit = 0;
    public $pureDigit = true;
    public $prodNum = 0;
    public $totalPrice = 0;
    public $itemNumArray = [];
    public $difficultShippingItems = [];
    public $fullDCodeReduction = 0;
    public $sideBasket = false;
    public $theoretical = false;
    public $codesMatter = false;
    public $codeList = [];
    public $countries = [];
    public $deliverableCountries = [];
    public $codesExist = true;
    use allDStools;

    //domestic
    function calcReducedPrice($itemDetails, $alterMode, $amount, $currentDeduc){
        $okCode = true;
        $query = "SELECT acceptCodes FROM partners_ds WHERE id = ".$itemDetails["row"]["sellerId"];
        if ($result = $this->conn->query($query)){
            if (mysqli_num_rows($result) == 0) { $okCode = false; }
            else {
                while ($nRow = $result->fetch_assoc()) {
                    if ($nRow["acceptCodes"]!=1){$okCode = false; }
                }
            }
        }
        if ($okCode){
            if ($alterMode == "linear"){
                $newPrice = $itemDetails["price"] - intval($amount);
            }
            else if ($alterMode == "geometric") {
                $newPrice = $itemDetails["price"] - floor(($itemDetails["price"] * intval($amount)) / 1000);
            }
            if ($newPrice > $itemDetails["price"]){$newPrice = $itemDetails["price"];}

            $deduc = $itemDetails["price"] - $newPrice;
            if ($itemDetails["price"] - ($deduc + $currentDeduc) < $itemDetails["minPrice"]){$deduc = $itemDetails["price"] - $itemDetails["minPrice"] -$currentDeduc;}
            return $deduc;
        }
        else {
            return 0;
        }
    }


    function loopBasket($conn, $inbasket, $sideBasket = false, $theoretical = false, $codesMatter = false, $type = "items", $effect = false) {
        global $codeCookieReplacement;
        if (!isset($this->countries) OR count($this->countries) == 0) {
          require($_SERVER['DOCUMENT_ROOT']."/ds/g/countries.php");
          $this->countries = $countries;
        }

        $this->conn = $conn;
        $this->inbasket = $inbasket;
        $this->sideBasket = $sideBasket;
        $this->theoretical = $theoretical;
        $this->codesMatter = $codesMatter;
        $this->deliverableCountries = $this->countries["GLO"];
        $this->type = $type;

        if ($this->type == "subs"){
          if (gettype($this->inbasket) != "array"){
              $this->inbasket = json_decode($this->inbasket, true);
          }
          if (isset($codeCookieReplacement)){
              $this->codeList = $codeCookieReplacement;
          }
          else if (isset($_COOKIE["ds_codes"])) {
              $this->codeList = $_COOKIE["ds_codes"];
          }
          else {
              $this->codesMatter = false;
          }
        }
        else {
          if (gettype($this->inbasket) != "array"){
              $this->inbasket = explode(",", $this->inbasket);
          }
        }
        if (gettype($this->inbasket) != "array"){$this->inbasket = [];}

        if ($this->codesMatter){
          if (gettype($this->codeList)=="string") {
            $this->codeList = explode(",", $this->codeList);
          }
          if (count($this->codeList)==0){$this->codesMatter = false;}
          else if (count($this->codeList)>5){
              setcookie("ds_codes", "", time() -3600, "/");
              $this->codesMatter = false;
          }
        }

        if ($this->type == "subs"){
            foreach ($this->inbasket as $sub){
                $this->prodNum++;
                $this->codesExist = false;
                $query = "SELECT * FROM dssubs WHERE id = ".$sub["id"];
                if ($result = $this->conn->query($query)){
                    while ($row = $result->fetch_assoc()) {
                        $sub["row"] = $row;
                        $sub["name"] = $row["name"];
                        $sub["row"]["datas"] = json_decode($row["datas"], true);
                        $sub["price"] = $sub["row"]["datas"]["price"];
                        if (isset($sub["row"]["datas"]["lesser"])) {$this->lessers = $sub["row"]["datas"]["lesser"]; } else {$this->lessers = [];}
                    }
                }
                $this->totalPrice += $sub["price"];
                $this->itemArray[] = $sub;
            }
        }
        else {
            foreach ($this->inbasket as $x => $value) {
                if (stripos($value, "[")) {
                    $shortitem = substr($value, 0, strpos($value, "["));
                }
                else if (stripos($value, "-")) {
                    $shortitem = substr($value, 0, strpos($value, "-"));
                }
                else if (stripos($value, "(")) {
                    $shortitem  = substr($value, 0, strpos($value, "("));
                }
                else {$shortitem = $value;}
                if (preg_match("/^[0-9]+$/", $shortitem)!=1){continue;}
                $query = "SELECT * FROM dsprods WHERE id = ".$shortitem;
                if ($result = $this->conn->query($query)) {
                    $this->prodNum++;
                    while ($row = $result->fetch_assoc()) {
                        //validity
                        $killThis = false;
                        $query = "SELECT status FROM partners WHERE id = ".$row["sellerId"];
                        if ($result = $this->conn->query($query)){
                            if (mysqli_num_rows($result) == 0) { $killThis = true; }
                            while ($nRow = $result->fetch_assoc()) {
                                if ($nRow["status"]!="active"){$killThis = true; }
                            }
                        }
                        if ($this->prodNum > 10){$killThis = true;}
                        if ($killThis) {
                            unset($this->inbasket[$x]);
                            if ($this->sideBasket) {$_SESSION["basket"] = implode(",",$this->inbasket);}
                            continue;
                        }

                        $itemDetails["row"] = $row;
                        $itemDetails["quant"] = 1;
                        $itemDetails["prodSpecs"] = [];
                        $itemDetails["codeReducs"] = [];
                        $itemDetails["assocDico"] = [];
                        $itemDetails["totalCodeReduc"] = 0;
                        $itemDetails["basketPos"] = $x;
                        $addName = "";

                        //parse details
                        $itemMinPrice = intval($row["minPrice"]);
                        $coolProdSpecs = [];
                        $quantOption = 1;
                        if (stripos($value, "[")) {
                            $specsArray = json_decode($row["specifications"], true);

                            if (stripos($row["price"], ",")) {
                                $priceOptions = explode(",", $row["price"]);
                                $price = $priceOptions[0];
                            }
                            else{$price = $row["price"];}

                            if (stripos($value, "[]")){
                                $assocDico = [];
                            }
                            else {
                                $stringDico = substr($value, stripos($value, "[")+1, stripos($value, "]") - 1);
                                $chunks = array_chunk(preg_split('/(:|-)/', $stringDico), 2);
                                $assocDico = array_combine(array_column($chunks, 0), array_column($chunks, 1));
                                $itemDetails["assocDico"] = $assocDico;
                            }
                            $itemDetails["assocDico"] = $assocDico;
                            //check quantity / stock
                            $itemStock = $row["stock"];
                            $smartstock = false;
                            foreach ($specsArray as $spec){
                              if (isset($spec["smartstock"]) AND $spec["smartstock"] == 1){
                                $smartstock = true;break;
                              }
                            }

                            //parse specifications
                            $specShipping = []; $itemNumId = $shortitem;
                            foreach($specsArray as $key => $specArray){
                                $option = $specArray["name"];
                                if (isset($assocDico[$option])){
                                    $selected = intval($assocDico[$option]);
                                }
                                else {
                                    $selected = 0;
                                }
                                if (!isset($specArray["options"][$selected])){
                                    $selected = 0;
                                }
                                if (isset($specArray["options"][$selected]["shipping"])){
                                    $specShipping[] = $specArray["options"][$selected]["shipping"];
                                }
                                if ($smartstock AND isset($specArray["smartstock"]) AND $specArray["smartstock"] == 1){
                                  $itemStock = $specArray["options"][$selected]["stock"];
                                  $itemNumId = $shortitem."_".$selected; $neatKey = $key; $neatSelected = $selected;
                                }

                                $price += $specArray["options"][$selected]["price"];
                                $coolProdSpecs[] = $option.": ".$specArray["options"][$selected]["name"];

                                if ($shortitem == 1){
                                    if ($selected == 2){$addName = "Legendar";}else if($selected==1){$addName = "Grand Wizard";}else{$addName = "Imperial Soldier";}
                                }
                            }
                            $itemDetails["specShipping"] = $specShipping;
                            if (!isset($this->itemNumArray[$itemNumId])) {$this->itemNumArray[$itemNumId] = 0;}

                            if (isset($assocDico["quant"])){
                                $quantOption = intval($assocDico["quant"]);
                            }
                            //correct actual amount
                            if (!$this->theoretical) {
                                if ($row["digital"]== 0 ){
                                    $artMaxAmount = $row["maxAmount"];
                                    $actualMaxAmount = $itemStock - $this->itemNumArray[$itemNumId]; if ($effect){$actualMaxAmount = $itemStock;}
                                    if ($artMaxAmount < $actualMaxAmount AND $artMaxAmount != 0){$actualMaxAmount = $artMaxAmount;}
                                    if ($actualMaxAmount > 99) {$actualMaxAmount = 99;}
                                    if ($quantOption > $actualMaxAmount){$quantOption = $actualMaxAmount;}
                                    else if ($quantOption < 1){$quantOption = 1;}
                                }
                                else {$quantOption = 1;}
                                if ($row["status"]!="active") {
                                    unset($this->inbasket[$x]);if ($this->sideBasket) {$_SESSION["basket"] = implode(",",$this->inbasket);} continue;
                                }
                            }

                            $itemMinPrice = $itemMinPrice * $quantOption;
                            $itemDetails["quant"] = $quantOption;

                            $this->itemNumArray[$itemNumId] += $quantOption;
                            if (!$this->theoretical){
                                if ($row["digital"]==0){
                                    if ($effect){
                                      if ($smartstock){
                                        $specsArray[$neatKey]["options"][$neatSelected]["stock"] = $itemStock - $quantOption;
                                        $nameSpeccer = json_encode($specsArray);
                                        $query = "UPDATE dsprods SET specifications = '$nameSpeccer' WHERE id = $shortitem";
                                        $conn->query($query);
                                      }
                                      else {
                                        $query = "UPDATE dsprods SET stock = stock - ".$quantOption." WHERE id = $shortitem";
                                        $conn->query($query);
                                      }
                                    }
                                    else {
                                      if ($itemStock - $this->itemNumArray[$itemNumId] < 0 OR $quantOption == 0){
                                          unset($inbasket[$x]);
                                          if ($sideBasket) {$_SESSION["basket"] = implode(",",$inbasket);}
                                          header("Location: /ds/basket?show=outOfStock" );
                                          continue;
                                      }
                                    }
                                }
                            }

                            if ($quantOption != 1){
                                $coolProdSpecs[] = "Quantity: ".$quantOption;
                            }
                            $price = $price * $quantOption;
                            $itemDetails["prodSpecs"] = $coolProdSpecs;
                        }
                        else {
                            $itemDetails["specShipping"] = [];
                            if (stripos($row["price"], ",")) {
                                $option = substr($value, strpos($value, ":")+1);
                                $option = intval($option) - 1;
                                $priceOptions = explode(",", $row["price"]);
                                $price = $priceOptions[$option];
                            }
                            else if ($row["price"]==0) {
                                if (stripos($value, "(")){
                                    $price = substr($value, stripos($value, "/")+1, -1);
                                    $addName = substr($value, stripos($value, "(")+1, stripos($value, "/") - stripos($value, "(")- 1);
                                }
                                else {
                                    $price = substr($value, strpos($value, "-")+1);
                                }
                            }
                            else {$price = $row["price"];}
                            $itemDetails["quant"] = 1;
                        }
                        if ($shortitem == 2) {$this->totalCredit += $price;}
                        if ($row["digital"]==0){$this->pureDigit = false;}
                        if ($shortitem == 2 OR $shortitem == 3){$itemMinPrice = $price;}
                        $itemDetails["minPrice"] = $itemMinPrice;
                        $itemDetails["addName"] = $addName;

                        if ($itemMinPrice > $price) {$price = $itemMinPrice;}
                        $this->totalPrice += $price;
                        $itemDetails["price"] = $price;

                        //discount codes
                        if ($this->codesMatter) {
                            $currentFullDCodeReduc = 0;
                            $eachCookieReduc = [];
                            foreach ($this->codeList as $codeName) {
                                if (preg_match("/[^-A-Za-z0-9,]/", $codeName)==1){
                                    setcookie("ds_codes", "", time() -3600, "/");
                                    $this->codesMatter = false;
                                    continue;
                                }
                                $query = "SELECT * FROM dscodes WHERE code = '$codeName'";
                                if ($firstrow = $conn->query($query)) {
                                    if (mysqli_num_rows($firstrow) == 0) {continue;}
                                    while ($cRow = $firstrow->fetch_assoc()) {
                                        $codeStatus = $cRow["status"];
                                        $codeMaxUses = $cRow["maxUses"];
                                        $codeUses = $cRow["uses"];
                                        $codeAffect = $cRow["affect"];
                                        $codeAmount = $cRow["amount"];
                                        $codeAlterMode = $cRow["alterMode"];
                                    }
                                    if ($codeStatus == 1) {
                                        if ($codeMaxUses - $codeUses > 0) {
                                            $codeAffect = explode(",", $codeAffect);
                                            if ($codeAffect[0]==1){
                                                unset($codeAffect[0]);
                                                if (in_array("ALL", $codeAffect) OR in_array($row["sellerId"], $codeAffect)) {
                                                    $codeReduction = $this->calcReducedPrice($itemDetails, $codeAlterMode, $codeAmount, $currentFullDCodeReduc);
                                                    $currentFullDCodeReduc += $codeReduction;
                                                    $this->fullDCodeReduction += $codeReduction;
                                                    $eachCookieReduc[$codeName] = $this->makeHuman($codeReduction);
                                                }
                                            }
                                            else if ($codeAffect[0]==0){
                                                unset($codeAffect[0]);
                                                if (!in_array("ALL", $codeAffect) AND !in_array($row["sellerId"], $codeAffect)) {
                                                    $codeReduction = $this->calcReducedPrice($itemDetails, $codeAlterMode, $codeAmount, $currentFullDCodeReduc);
                                                    $currentFullDCodeReduc += $codeReduction;
                                                    $this->fullDCodeReduction += $codeReduction;
                                                    $eachCookieReduc[] = $codeName.": ".$this->makeHuman($codeReduction);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $itemDetails["codeReducs"] = $eachCookieReduc;
                            $itemDetails["totalCodeReduc"] = $currentFullDCodeReduc;
                        }
                        $this->itemArray[] = $itemDetails;
                    }
                }
            }
        }
    }

    //foreign
    function possibleCountries() {
        if ($this->pureDigit){return;}
        foreach ($this->itemArray as $itemDetails){
            if ($itemDetails["row"]["shipping"] != null){
                $thisItemDeliverable = [];
                $chunks = array_chunk(preg_split('/(:|,)/', $itemDetails["row"]["shipping"]), 2);
                $assocDico = array_combine(array_column($chunks, 0), array_column($chunks, 1));
                if (isset($assocDico["GLO"])){
                    $thisItemDeliverable = $this->countries["GLO"];
                }
                else {
                    foreach ($assocDico as $deliverGroup => $uselessPrice){
                        if (strlen($deliverGroup)==2){
                            $thisItemDeliverable[$deliverGroup] = "";
                        }
                        else if (strlen($deliverGroup)==3) {
                            if (isset($this->countries[$deliverGroup])) {
                                foreach ($this->countries[$deliverGroup] as $countryName => $uselessPrice){
                                    $thisItemDeliverable[$countryName] = "";
                                }
                            }
                        }
                    }
                }
                $cunter = 0;
                foreach ($this->countries["GLO"] as $possCountry => $uselessName) {
                    if (!isset($thisItemDeliverable[$possCountry])){
                        $cunter++;
                        unset($this->deliverableCountries[$possCountry]);
                    }
                }
                if ($cunter > 22) {
                    $this->difficultShippingItems[$itemDetails["row"]["id"]] = 1;
                }
                else {
                    $this->difficultShippingItems[$itemDetails["row"]["id"]] = 0;
                }
            }
        }
    }
}

trait allDStools {
  //Misc. Functionality
  function clearImgUrl($image) {
      if (!str_contains($image, "://")) {
        return "/ds/images/".$image;
      }
      else {
        return $image;
      }
  }
  function makeHuman($ordiprice) {
    $price = "$".number_format($ordiprice/100, 2, ".", "'");
    $price = str_replace(".00", "", $price);
    $price = str_replace("$-", "-$", $price);
    return $price;
  }
  function linki($id, $link, $name = "item") {
    if ($link != ""){
        return "/ds/".$link;
    }
    return "/ds/".$id."/".str_replace(" ", "_", $name);
  }
  function detailsUL($specs, $codes = []){
    $fullUL = "<ul>";
    foreach ($specs as $addInfo){
        $fullUL .= "<li>".$this->placeSpecChar($addInfo)."</li>";
    }
    foreach ($codes as $addInfo){
        $fullUL .= "<li style='color:green'>".$addInfo."</li>";
    }
    return $fullUL."</ul>";
  }
  function detailsLine($specs, $name){
    $fullLine = $name." (";
    foreach ($specs as $addInfo) {
        $fullLine .= $this->placeSpecChar($addInfo).", ";
    }
    $fullLine = substr($fullLine, 0, strlen($fullLine)-2).")";
    if (count($specs)==0){$fullLine = substr($fullLine, 0, strlen($fullLine)-1);}
    return $fullLine;
  }

  //find the cost associated with a specific country code
  function fetchShippingCost($artShipping, $country) {
    if (gettype($artShipping)=="int") {return $artShipping;}
    else if (preg_match("/^[0-9]+$/", $artShipping)) {return intval($artShipping);}
    else if ($artShipping == ""){return 0;}

    $chunks = array_chunk(preg_split('/(:|,)/', $artShipping), 2);
    if (gettype($chunks) != "array") {return 0;}
    $assocDico = array_combine(array_column($chunks, 0), array_column($chunks, 1));

    $shippingCost = false;
    foreach ($assocDico as $key => $value) {
        if (strlen($key) == 3){
        //see if it's in a country array
            if (isset($this->countries[$key][$country])){
                $shippingCost = $value;
                break;
            }
        }
        else if (strlen($key) == 2){
        //single country
            if ($key==$country){
              $shippingCost = $value;
              break;
            }
        }
    }
    return $shippingCost;
  }
  //a basketed item's shipping cost
  function itemShipping($item, $country){
    //base item shipping
    if (($add = $this->fetchShippingCost($item["row"]["shipping"], $country))===false) {
      $this->go("checkout1");
    }
    $add *= $item["quant"];
    //spec shipping
    foreach ($item["specShipping"] as $specsShipping) {
      $add += $this->fetchShippingCost($specsShipping, $country); if ($add===false){$this->go("checkout1");}
    }
    return $add;
  }

  function calcStripeTax($calcTotal) {
    $stripeTax = $calcTotal*0.029;
    $stripeTax = round($stripeTax);
    $stripeTax = $stripeTax + 32;
    return $stripeTax;
  }
  function totalPrice($basketed, $totalShipping) {
    $totalPrice = $basketed->totalPrice - $basketed->fullDCodeReduction;
    $totalPrice += $totalShipping;
    return $totalPrice;
  }
}

?>
