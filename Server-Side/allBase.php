<?php
//allBase
if (!trait_exists("allBase")){
  trait allBase {
    public $regArrayR = [
      "basic" => "/[^A-Za-z0-9_]/",
      "number" => "/[^0-9]/",
      "quotes" => "/[\"']/",
      "full" => "/[^A-Za-z0-9àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū_&\/,\(\)\.\-%:\? ]/",
      "cleanText" => "/^[^\"<>]+$/",
      "cleanText2" => "/^[^\"'<>]+$/",
      "wikiName" => "/^[A-Za-z0-9àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū',():\- ]{2,}$/",
      "tag" => "/[^A-Za-z0-9&]/",
      "dicWord" => "/[^A-Za-zàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū\-' ]/"
    ];

    function construct() {
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
      $this->conn = $conn;
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
      else {
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
    function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
