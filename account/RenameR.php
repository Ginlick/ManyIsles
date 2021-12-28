<?php
if (preg_match("/[^A-Za-z0-9\&\'\- ]{2,}/", $_POST['rId'])==1){header("Location: Publish.php");exit();}
if (preg_match("/[^A-Za-z\'\.\:\;\,\!\?\-() ]{2,}/", $_POST['rType'])==1){header("Location: Publish.php");exit();}
$header = "Location: Product.php?id=".$_POST['rId']."&t=".$_POST['rType'];



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
if(!isset($_COOKIE["loggedIn"])){header("Location: Account.html?error=signingIn");exit();}
if(!isset($_COOKIE["loggedP"])){header("Location: Account.html?error=signingIn");exit();}


$id = $_COOKIE["loggedIn"];
$nname = $_POST['nname'];
$njacob = $_POST['njacob'];
$rId = $_POST['rId'];
$type = $_POST['rType'];
if ($type == "m"){$longtype = "products";}
else if ($type == "d"){$longtype = "diggies";}
else if ($type == "a"){$longtype = "art";}
$status = "";

$nname = str_replace('"', '', $nname);
$nname = str_replace('<', '', $nname);
$njacob = str_replace('"', '', $njacob);
$njacob = str_replace('<', '', $njacob);

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $title = $row["title"];
      $curpsw = $row["password"];
    }
    }
 $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query = "SELECT * FROM partners WHERE account = '".$uname."'";
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_row()) {
        $pid = $row[0];
        $pname = $row[1];
        $status = $row[6];
    }
    }
if ($status != "active"){header("Location: SignedIn.php");exit();}

$query="SELECT * FROM products WHERE id =".$rId;
if ($type == "d"){$query = str_replace("products", "diggies", $query);}
if ($type == "a"){$query = str_replace("products", "art", $query);}
if ($prodrow = $conn->query($query)){
while ($row = $prodrow->fetch_row()) {
      $prodName = $row[1];
      $prodPart = $row[3];
    }
}
if ($prodPart != $pname){header("Location: Publish.php");exit();}
$oldName = $prodName;
if ($nname != ""){$prodName = $nname;}

$checkrr = "";
$query = "SELECT * FROM products WHERE name =".$nname;
if ($type == "d"){$query = str_replace("products", "diggies", $query);}
if ($type == "a"){$query = str_replace("products", "art", $query);}
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_row()) {
      $checkrr = $row[0];
    }
    }
if ($checkrr != ""){header($header."&why=present");exit();}



if (isset($_FILES["image"])){
$realpath = realpath("Publish.php");
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$deletepath = $realpath."/uploads/";
$imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
$realpath = $realpath."/uploads/OldProd/".$prodName.".".$imageFileType;

$uploadOk = 1;

$target_file = $realpath;

if ($njacob != "") {$insertation = 'UPDATE '.$longtype.' SET jacob = "'.$njacob.'"'." WHERE id =".$rId;$conn->query($insertation);}
if ($nname != "") {$insertation = 'UPDATE '.$longtype.' SET name = "'.$nname.'"'." WHERE id =".$rId;$conn->query($insertation);}

if ($nname != ""){
    $query = "DELETE FROM newSub WHERE type ='".$type."' AND id =".$rId;
    $conn->query($query);
    $delte1a = $deletepath."/NewRFile/".$oldName.".png";
    $delte1b = $deletepath."/NewRFile/".$oldName.".jpg";
    $delte1c = $deletepath."/NewRFile/".$oldName.".jpeg";
    $delte1d = $deletepath."/NewRFile/".$oldName.".pdf";
    $delte2a = $deletepath."/OldProd/".$oldName.".png";
    $delte2b = $deletepath."/OldProd/".$oldName.".jpg";
    $delte2c = $deletepath."/OldProd/".$oldName.".jpeg";
    unlink($delte1a);
    unlink($delte1b);
    unlink($delte1c);
    unlink($delte1d);
    unlink($delte2a);
    unlink($delte2b);
    unlink($delte2c);
}


if($imageFileType != "") {
      $check = getimagesize($_FILES["image"]["tmp_name"]);
      if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
      } else {
        echo "File is not an image.";
        $uploadOk = 0;
      }
    $oldFile = "yes";
    if (file_exists($target_file)) {
      unlink($target_file);
    }
    if ($_FILES["image"]["size"] > 300000) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
      echo "Sorry, only JPG, JPEG, PNG files are allowed.";
      $uploadOk = 0;
    }
    if ($uploadOk == 0) {
      echo "Sorry, your file was not uploaded.";
      header($header."&why=badImage");exit();
    } else {
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
        $message = $pname."'s new product thumbnail";
        $subject = "New Image for ".$rId;
       mail ("godsofmanyisles@gmail.com", $subject, $message);
        $query = sprintf('INSERT INTO newSub (what, type, id, file, partner) VALUES ("prodImg", "%s", "%s", "%s", "%s")', $type, $rId, $prodName.".".$imageFileType, $pid);
        $conn->query($query);

      } 
    }
    }
}

header($header."&why=npSuccess");

?>
