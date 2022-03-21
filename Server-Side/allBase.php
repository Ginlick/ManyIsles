<?php
if (!trait_exists("allBase")){
  trait allBase {
    public $regArrayR = [
      "basic" => "/[^A-Za-z0-9_]/",
      "quotes" => "/[\"']/",
      "full" => "/[^A-Za-z0-9_&\/,\(\)\.\-% ]/"
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
    function replaceSpecChar($input) {
      $input = str_replace("'", "%single_quote%", $input);
      $input = str_replace('"', "%double_quote%", $input);
      return $input;
    }
    function placeSpecChar($input) {
      $input = str_replace("%single_quote%", "'", $input);
      $input = str_replace( "%double_quote%", '"', $input);
      return $input;
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
    function killCache() {
      header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
      header("Pragma: no-cache"); //HTTP 1.0
      header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
    function go($place) {
      $this->killCache();
      echo "<script>window.location.replace('$dom$place');</script>";
      exit;
    }
  }
}


?>
