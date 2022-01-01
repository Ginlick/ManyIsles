<?php
//requires inbasket, $conn

require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/makeHuman.php");
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/countries.php");

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
    public $inbasket = [];
    public $conn = null;
    public $codesExist = true;


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


    function __construct($conn, $inbasket, $sideBasket = false, $theoretical = false, $codesMatter = false, $type = "items") {
        global $codeCookieReplacement, $countries;
        $this->conn = $conn;
        $this->inbasket = $inbasket;
        $this->sideBasket = $sideBasket;
        $this->theoretical = $theoretical;
        $this->codesMatter = $codesMatter;
        $this->countries = $countries;
        $this->deliverableCountries = $this->countries["GLO"];
        $this->type = $type;

        if ($this->type == "subs"){
            if (gettype($this->inbasket) != "array"){
                $this->inbasket = json_decode($this->inbasket, true);
            }
        }
        else {
            if (gettype($this->inbasket) != "array"){
                $this->inbasket = explode(",", $this->inbasket);
            }
        }
        if (gettype($this->inbasket) != "array"){$this->inbasket = [];}

        if (isset($codeCookieReplacement)){
            $this->codeList = $codeCookieReplacement;
        }
        else if (isset($_COOKIE["ds_codes"])) {
            $this->codeList = $_COOKIE["ds_codes"];
        }
        else {
            $this->codesMatter = false;
        }

        if ($this->codesMatter){
            if (preg_match("/[^-A-Za-z0-9,]/", $this->codeList)==1){
                setcookie("ds_codes", "", time() -3600, "/");
                $this->codesMatter = false;
            }
            $this->codeList = explode(",", $this->codeList);
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
                        if ($row["stock"] == 0 AND $row["digital"]==0 AND !$this->theoretical){$killThis = true; }
                        if (!isset($this->itemNumArray[$shortitem])) {$this->itemNumArray[$shortitem] = 0;}
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
                            }

                            //check quantity / stock
                            if (isset($assocDico["quant"])){
                                $quantOption = intval($assocDico["quant"]);
                            }

                            if (!$this->theoretical) {
                                if ($row["digital"]== 0 ){
                                    $artMaxAmount = $row["maxAmount"];
                                    $actualMaxAmount = $row["stock"] - $this->itemNumArray[$shortitem];
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

                            $this->itemNumArray[$shortitem] += $quantOption;
                            if (!$this->theoretical){
                                if ($row["digital"]==0){
                                    if ($row["stock"] - $this->itemNumArray[$shortitem] < 0 OR $quantOption == 0){
                                        unset($inbasket[$x]);
                                        if ($sideBasket) {$_SESSION["basket"] = implode(",",$inbasket);}
                                        header("Location: ".$_SERVER['REQUEST_URI'] . "?".http_build_query( array_merge( $_GET, array( 'show' => 'outOfStock' ) ) ) );
                                        continue;
                                    }
                                }
                            }

                            //parse specifications
                            $specShipping = 0;
                            foreach($specsArray as $specArray){
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
                                    $specShipping += intval($specArray["options"][$selected]["shipping"]);
                                }

                                $price += $specArray["options"][$selected]["price"];
                                $coolProdSpecs[] = $option.": ".$specArray["options"][$selected]["name"];

                                if ($shortitem == 1){
                                    if ($selected == 2){$addName = "Legendar";}else if($selected==1){$addName = "Grand Wizard";}else{$addName = "Imperial Soldier";}
                                }
                            }
                            $itemDetails["specShipping"] = $specShipping;

                            if ($quantOption != 1){
                                $coolProdSpecs[] = "Quantity: ".$quantOption;
                            }

                            $price = $price * $quantOption;
                            $itemDetails["prodSpecs"] = $coolProdSpecs;
                        }
                        else {
                            $itemDetails["specShipping"] = 0;
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
                                                    $eachCookieReduc[] = $codeName.": ".makeHuman($codeReduction);
                                                }
                                            }
                                            else if ($codeAffect[0]==0){
                                                unset($codeAffect[0]);
                                                if (!in_array("ALL", $codeAffect) AND !in_array($row["sellerId"], $codeAffect)) {
                                                    $codeReduction = $this->calcReducedPrice($itemDetails, $codeAlterMode, $codeAmount, $currentFullDCodeReduc);
                                                    $currentFullDCodeReduc += $codeReduction;
                                                    $this->fullDCodeReduction += $codeReduction;
                                                    $eachCookieReduc[] = $codeName.": ".makeHuman($codeReduction);
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



?>
