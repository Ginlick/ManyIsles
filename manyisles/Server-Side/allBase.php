<?php
//allBase
if (!trait_exists("allBase")){
  trait allBase {
    public $regArrayR = [
      "basic" => "/[^A-Za-z0-9_]/",
      "account" => "/[A-Za-z0-9 ]{2,}/",
      "number" => "/[^0-9]/",
      "quotes" => "/[\"']/",
      "full" => "/[^A-Za-z0-9àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū_&\/,\(\)\.\-%:\? ]/",
      "cleanText" => "/^[^\"<>]+$/",
      "cleanText2" => "/^[^\"'<>]+$/",
      "wikiName" => "/^[A-Za-z0-9àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū',():\- ]{2,}$/",
      "tag" => "/[^A-Za-z0-9&]/",
      "dicWord" => "/[^A-Za-zàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū\-'%_ ]/"
    ];
    private $serverInfo = [];

    function construct() {
      $this->conn = $this->addConn("accounts");
      $this->checkServerInfo();
    }
    function checkServerInfo() {
      if ($this->serverInfo == []){
        $tofile = dirname(dirname($_SERVER['DOCUMENT_ROOT']))."/keys/server-info.json";
        if (!file_exists($tofile)) {
          throw new ErrorException("Missing essential file: server-info");
        }
        $newjson = file_get_contents($tofile);
        $newjson = json_decode($newjson, true);
        if (!isset($newjson["email"]) OR !isset($newjson["mysql"])){
          throw new ErrorException("Incomplete server-info file");
        }
        $this->serverInfo = $newjson;
      }
    }
    function giveServerInfo($key) {
      if (isset($this->serverInfo[$key])){
        return $this->serverInfo[$key];
      }
    }

    function purate($input, $regex = "basic") {
      //for links
      $input = str_replace(" ", "_", $input);
      return preg_replace($this->regArrayR[$regex], "", $input);
    }
    function purify($input, $regex = "basic") {
      //for sql cleaning & more
      return preg_replace($this->regArrayR[$regex], "", $input);
    }
    function replaceSpecChar($input, $level = 1) {
      $input = str_replace("'", "%single_quote%", $input);
      if ($level > 0){
        $input = str_replace('"', "%double_quote%", $input);
        if ($level > 1) {
            $input = str_replace(":", '%colon%', $input);
            $input = str_replace(";", '%pcolon%', $input);
            $input = str_replace("-", '%hyphon%', $input);
            $input = str_replace(",", '%comma%', $input);
            $input = str_replace("[", '%sqbrak_left%', $input);
            $input = str_replace("]", '%sqbrak_right%', $input);
            if ($level > 2){
              $input = str_replace("'", '', $input);
            }
        }
      }
      return $input;
    }
    function placeSpecChar($input, $level = 1) {
      if ($input == ""){return "";}
      if ($level < 1){
        $input = str_replace('%double_quote%', '', $input);
        $input = str_replace("%single_quote%", "", $input);
      }
      if ($level < 2){
        $input = str_replace("%single_quote%", "'", $input);
        $input = str_replace("%double_quote%", '"', $input);
      }
      else { //level >= 2
        $input = str_replace("%single_quote%", "'", $input);
        $input = str_replace("%double_quote%", '"', $input);
        $input = str_replace("%colon%", ':', $input);
        $input = str_replace("%pcolon%", ';', $input);
        $input = str_replace("%hyphon%", '-', $input);
        $input = str_replace("%comma%", ',', $input);
        $input = str_replace("%sqbrak_left%", '[', $input);
        $input = str_replace("%sqbrak_right%", ']', $input);
        $input = str_replace("%qbrak_left%", '{', $input);
        $input = str_replace("%qbrak_right%", '}', $input);
      }
      return $input;
    }
    function explodeA($str, $separator = ", ", $reg = null) {
      if ($str == "") {return [];}
      $arr = explode($separator, $str);
      return $arr;
    }
    function generateRandomString($length = 10, $charIndex = 0) {
      $characterList = ['0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', "abcdefghijkmnopqrstuvwxyz0123456789"];
      $characters = $characterList[$charIndex];
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }
    function isEmpty(array $array) {
        $empty = true;
        array_walk_recursive($array, function ($leaf) use (&$empty) {
            if ($leaf === [] || $leaf === '') {
                return false;
            }
            $empty = false;
        });
        return $empty;
    }
    function killCache() {
      header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
      header("Pragma: no-cache"); //HTTP 1.0
      header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
    function go($place) {
      $this->killCache();
      echo "<script>window.location.replace('$place');</script>";
      exit;
    }

    //mysql connections
    function addConn($dbname) {
      $this->checkServerInfo();
      $inf = $this->serverInfo["mysql"];
      $dbinf = $inf["databases"][$dbname];
      $uname = $inf["username"];
      $psw = $inf["password"];
      $servername;

      if (gettype($dbinf)=="string"){
        $servername = $dbinf;
      }
      else {
        $servername = $dbinf["servername"];
        if (isset($dbinf["username"])){$uname = $dbinf["username"];}
        if (isset($dbinf["password"])){$psw = $dbinf["password"];}
      }

      if ($newconn = new mysqli($inf["servername"], $uname, $psw, $servername)){
        return $newconn;
      }
      return false;
    }
    //engines
    function addMailer() {
      require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/mailer.php");
      $this->checkServerInfo();
      return new mailer($this->serverInfo["email"]);
    }
  }
}
if (!class_exists("useBase")){
  class useBase {
    use allBase;
  }
}

//error handling
require_once($_SERVER["DOCUMENT_ROOT"]."/Server-Side/errorHandler.php");
?>
