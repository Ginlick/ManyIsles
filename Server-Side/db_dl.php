<?php
  $servername = "localhost";
  $username = "aufregendetage";
  $password = "vavache8810titigre";
  $dbname = "dl";
  if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
      $servername = "localhost:3306";
      $username = "trader";
      $password = "4Be8dc%6";
      $dbname = "manyisle_dl";
  }
  $dlconn =  new mysqli($servername, $username, $password, $dbname);
?>
