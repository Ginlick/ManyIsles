<?php
if (preg_match("/[A-Za-z0-9 ]{2,}/", $_POST['uname'])!=1 or preg_match("/[A-Za-z0-9]{1,}/", $_POST['psw'])!=1){header("Location: Account.html");exit();}


$servername = "localhost:3306";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "manyisle_accounts";

if ($_SERVER['REMOTE_ADDR']=="::1"){
$servername = "localhost";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "accounts";
}


$conn = new mysqli($servername, $username, $password, $dbname);
require("../Server-Side/encryptData.php");
$storedPassword = openssl_encrypt ($_POST['psw'], $method, $key, 0, $iv);

if (preg_match("/[A-Za-z0-9 ]{2,}/", $_POST['uname'])==1 and preg_match("/[A-Za-z0-9]{1,}/", $_POST['psw'])==1){
$appendixF = "?why=signingIn";
$appendixS = "?why=success";
if ($_POST['returnId']!="Nope"){
    $appendixF = $appendixF."&id=".$_POST['returnId'];
    $appendixS = $appendixS."&id=".$_POST['returnId'];
}
if ($_POST['returnQuery']!="Nope"){
    $appendixF = $appendixF."&query=".$_POST['returnQuery'];
    $appendixS = $appendixS."&query=".$_POST['returnQuery'];
}
if ($_POST['returnCategory']!="Nope" && $_POST['returnTo'] != "View.php"){
    $appendixF = $appendixF."&category=".$_POST['returnCategory'];
    $appendixS = $appendixS."&category=".$_POST['returnCategory'];
}
if ($_POST['returnType']!="Nope" && $_POST['returnTo'] != "View.php"){
    $appendixF = $appendixF."&type=".$_POST['returnType'];
    $appendixS = $appendixS."&type=".$_POST['returnType'];
}
if ($_POST['returnTo'] == "View.php") {
    $type = $_POST['returnType'];
    if ($type == "module"){$type = "m";}
    else if ($type == "diggie"){$type = "d";}
    else if ($type == "art"){$type = "a";}
    $appendixF = $appendixF."&t=".$type;
    $appendixS = $appendixS."&t=".$type;
}
echo "Location: /dl/".$_POST['returnTo'].$appendixS."<br>";

if(!isset($_COOKIE["loggedIn"])) {
   echo "1";
   if ($userrow = $conn->query(sprintf("SELECT * FROM accountsTable WHERE uname='%s';", $_POST['uname']))) {
   echo "2";
     if ($userrow->num_rows == 1) {
       echo "3";
            while ($row = $userrow->fetch_assoc()) {
               echo "4";
              if (password_verify($_POST['psw'], $row["password"])==1) {
                $id = $row["id"];
                setcookie("loggedIn", $id, time()+604800, "/");
                setcookie("loggedP", $storedPassword, time()+604800, "/");
                echo "success";
                header("Location: /dl/".$_POST['returnTo'].$appendixS);
              }
             else {header("Location: /dl/".$_POST['returnTo'].$appendixF);}
            }
     }
   else {header("Location: /dl/".$_POST['returnTo'].$appendixF);}    
   }
  else {header("Location: /dl/".$_POST['returnTo'].$appendixF);}
}
else {header("Location: /dl/".$_POST['returnTo'].$appendixS);}
}
else {header("Location: /dl/".$_POST['returnTo'].$appendixF);}
?>
