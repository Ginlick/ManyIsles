<?php


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
$id = $_COOKIE["loggedIn"];
$image = "";
if (isset($_GET["img"])) {$image = $_GET["img"];}
if (isset($_GET["file"])) {$file = $_GET["file"];}
$imageToDl = $_GET["imgLink"];

if(!isset($_COOKIE["loggedIn"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");exit();}
if(!isset($_COOKIE["loggedP"])) {header("Location: Account.html?error=notSignedIn");setcookie("loggedIn", "", time() -3600, "/");exit();}

  $getNameRow = "SELECT uname FROM accountsTable WHERE id =". $_COOKIE["loggedIn"];
  $nameresult =  $conn->query($getNameRow);
  $name ="";
  while ($row = $nameresult->fetch_row()) {$name = sprintf ("%s", $row[0]);}
 if ($name == "") {setcookie("loggedIn", "", time() -3600, "/");header("Location: Account.html");exit();}

  $getTitleRow = "SELECT title FROM accountsTable WHERE id =". $_COOKIE["loggedIn"];
  $titleresult =  $conn->query($getTitleRow);
  $title ="";
  while ($row = $titleresult->fetch_row()) {$title = sprintf ("%s", $row[0]);}

  $getMailRow = "SELECT * FROM accountsTable WHERE id =". $_COOKIE["loggedIn"];
  $mailresult =  $conn->query($getMailRow);
  $mail ="";
  while ($row = $mailresult->fetch_assoc()) {$mail = sprintf ("%s", $row["email"]); $curpsw = $row["password"];}
  $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

    $realpath = realpath("downStuff.php");
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);

if ($image == "part"){
    $realpath = $realpath."/uploads/PartProf/".$imageToDl;
}
else if ($image == "prod"){
    $realpath = $realpath."/uploads/NewProd/".$imageToDl;
}
else if ($file == "prodFile"){
    $realpath = $realpath."/uploads/NewRFile/".$imageToDl;
}
else if ($file == "prodImg"){
    $realpath = $realpath."/uploads/OldProd/".$imageToDl;
}
else if ($file == "partImg"){
    $realpath = $realpath."/uploads/PartProf/".$imageToDl;
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($realpath).'"');
header('Expires: 0');
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Pragma: public');
header('Content-Length: ' . filesize($realpath));
readfile($realpath);
exit();

?>

