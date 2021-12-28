<?php
if (!function_exists("giveConn")){
    function giveConn() {
      $servername = "localhost";
      $username = "aufregendetage";
      $password = "vavache8810titigre";
      $dbname = "accounts";
      if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
        $servername = "localhost:3306";
        $username = "aufregendetage";
        $password = "vavache8810titigre";
        $dbname = "manyisle_accounts";
      }
      return new mysqli($servername, $username, $password, $dbname);
    }
}

$conn = giveConn();

if (isset($_COOKIE["loggedIn"])){
    if (preg_match("/^[0-9]+$/", $_COOKIE["loggedIn"])!==1){
        setcookie("loggedIn", "", time() -3600, "/");
        setcookie("loggedP", "", time() -3600, "/");
    }
}

?>
