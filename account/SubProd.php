<?php

if (preg_match("/[^A-Za-z0-9\&\'\- ]{2,}/", $_POST['nname'])==1){header("Location: PubProd.php?x=1");exit();}
if (preg_match("/[^a-z]/", $_POST['type'])==1){header("Location: PubProd.php?x=3");exit();}
if (preg_match("/[^a-z]/", $_POST['categories'])==1){header("Location: PubProd.php?x=4");exit();}
if (preg_match("/[^0-2]/", $_POST['gamesys'])==1){header("Location: PubProd.php?x=5");exit();}
if (preg_match("/[^0-1]/", $_POST['supportProd'])==1){header("Location: PubProd.php?x=6");exit();}
//if (preg_match("/[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/=]*)/", $_POST['link'])==1){header("Location: PubProd.php?x=5");exit();}


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
$type=$_POST['type'];
$categories=$_POST['categories'];
$link = $_POST['link'];
if ($categories == ""){$categories = "g";}
$gamesys = $_POST['gamesys'];
$supportProd = $_POST['supportProd'];
$smartCategories = str_replace('"', '', $_POST['smartCategories']);
$smartCategories = str_replace('<', '', $smartCategories);

$njacob = str_replace('"', '', $njacob);
$njacob = str_replace('<', '', $njacob);

$status = "";

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $title = $row["title"];
      $curpsw = $row["password"];
      $email = $row["email"];
    }
    }
 $cpsw = openssl_decrypt ( $_COOKIE["loggedP"], "aes-256-ctr", "Ga22Y/", 0, "12gah589ds8efj5a");
  if (password_verify($cpsw, $curpsw)!=1){header("Location: Account.html?error=notSignedIn");setcookie("loggedP", "", time() -3600, "/");setcookie("loggedIn", "", time() -3600, "/");exit();}

$query = "SELECT * FROM partners WHERE account = '".$uname."'";
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        $pid = $row["id"];
        $pname = $row["name"];
        $status = $row["status"];
    }
    }
if ($status != "active"){header("Location: SignedIn.php");exit();}

$checkrr = "";
if ($firstrow = $conn->query("SELECT * FROM products WHERE name =".$nname)) {
    while ($row = $firstrow->fetch_row()) {
      $checkrr = $row[0];
    }
    }
if ($checkrr != ""){header("Location: PubProd.php?why=present");exit();}




if ($type != "d"){
    $realpath = realpath("Publish.php");
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);
    $FileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));
    $realpath = $realpath."/uploads/NewProd/".$nname."file.".$FileType;
    echo $_FILES["file"]["size"]."<br>";
    $uploadOk = 1;
    $target_file = $realpath;

    $oldFile = "yes";
    if (file_exists($target_file)) {
    echo "oldfile present";
      $oldFile = "no";
      $uploadOk = 0;
    }
    if ($_FILES["file"]["size"] > 35000000) {
      echo "Sorry, your file is too large.";
      $uploadOk = 2;
    }

    if ($type == "m"){
          if($FileType != "pdf") {
          echo "Not a pdf.";
          $uploadOk =3;
        }
    }
    else {
          if($FileType != "jpg" && $FileType != "png" && $FileType != "jpeg") {
          echo "Not an image.";
          $uploadOk =3;
        }
    }

    if ($uploadOk != 1) {
      echo "Sorry, your file was not uploaded.".$uploadOk."<br>";
      header("Location: PubProd.php?why=badFile");
      exit();
    } else {
      if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
      } else {echo "nayyy";      header("Location: PubProd.php?why=badFile");
      exit();}
    }
    $link = $nname."file.".$FileType;
}
else {
    if ($link == ""){header("Location: PubProd.php?why=badFile");exit();}
}


$realpath = realpath("Publish.php");
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
$realpath = $realpath."/uploads/NewProd/".$nname.".".$imageFileType;
$uploadOk = 1;
$ConstImageName = $nname.".".$imageFileType;

$target_file = $realpath;

      $check = getimagesize($_FILES["image"]["tmp_name"]);
      if($check !== false) {
        $uploadOk = 1;
      } else {
        echo "File is not an image.";
        $uploadOk = 0;
      }
    $oldFile = "yes";
    if (file_exists($target_file)) {
      $oldFile = "no";
      $uploadOk = 0;
    }
    if ($_FILES["image"]["size"] > 350000) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
      echo "Sorry, only JPG, JPEG, PNG files are allowed.";
      $uploadOk = 0;
    }
    if ($uploadOk == 0) {
      echo "Sorry, your image was not uploaded.";
      header("Location: PubProd.php?why=badImage");exit();
    } else {
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The image ". basename( $_FILES["image"]["name"]). " has been uploaded.";
      }
      else {
        header("Location: PubProd.php?why=badImage");exit();
      }
}

$baseCommand= 'INSERT INTO prodSub (name, image, partner, type, categs, tiers, jacob, link, email, categories, support, gsystem) VALUES ("%Name", "%Image", "%Partner", "%XAUUHSY", "%PROALAS", "g", "%Jacob", "%Link", "%MEGEMAIL", "%smartCategories", %supportProd, %gamesys);';
$baseCommand=str_replace("%Name", $nname, $baseCommand);
$baseCommand=str_replace("%Image", $ConstImageName, $baseCommand);
$baseCommand=str_replace("%Partner", $pid, $baseCommand);
$baseCommand=str_replace("%XAUUHSY", $type, $baseCommand);
$baseCommand=str_replace("%PROALAS", $categories, $baseCommand);
$baseCommand=str_replace("%Jacob", $njacob, $baseCommand);
$baseCommand=str_replace("%Link", $link, $baseCommand);
$baseCommand=str_replace("%MEGEMAIL", $email, $baseCommand);
$baseCommand=str_replace("%smartCategories", $smartCategories, $baseCommand);
$baseCommand=str_replace("%supportProd", $supportProd, $baseCommand);
$baseCommand=str_replace("%gamesys", $gamesys, $baseCommand);
echo $baseCommand;

if ($conn->query($baseCommand)){
    header("Location:Publish.php?why=prodPubbed");
}

?>
