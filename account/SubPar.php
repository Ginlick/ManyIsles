<?php

if (preg_match("/[^A-Za-z0-9'\- ]/", $_POST["pname"])==1){header("Location: BePartner.php?why=wrongTitle");exit();}
if (preg_match("/[^A-Za-z0-9]/", $_POST['psw'])==1){header("Location: BePartner.php?why=noPSW");exit();}

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


$id = $_COOKIE["loggedIn"];
$pname = $_POST['pname'];
$jacob = $_POST['jacob'];
$psw = $_POST['psw'];

$status = "";

$jacob = str_replace('"', '', $jacob);
$jacob = str_replace('<', '', $jacob);

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $checkpsw = $row["password"];
    }
}
if (password_verify($psw, $checkpsw)!=1){header("Location: BePartner.php?why=noPSW");exit();}

$query = "SELECT * FROM partners WHERE account = '".$uname."'";
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_row()) {
      $status = $row[6];
      $checkpsw = $row[7];
    }
    }
if ($status != ""){header("Location: SignedIn.php");exit();}

$checkrr = "";
if ($firstrow = $conn->query("SELECT * FROM partners WHERE name =".$pname)) {
    while ($row = $firstrow->fetch_row()) {
      $checkrr = $row[6];
    }
    }
if ($checkrr != ""){header("Location: BePartner.php?why=present");exit();}

$status = "active";
$setimage = "Traveler.png";
$realpath = realpath("SubPar.php");
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
$realpath = $realpath."/uploads/PartProf/".$pname.".".$imageFileType;

$target_file = $realpath;
$uploadOk = 1;

if($imageFileType != "") {
      $check = getimagesize($_FILES["image"]["tmp_name"]);
      if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
      } else {
        echo "File is not an image.";
        $uploadOk = 0;
      }


    if (file_exists($target_file)) {
      echo "Sorry, file already exists.";
      $uploadOk = 0;
    }
    if ($_FILES["image"]["size"] > 250000) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
      echo "Sorry, only JPG, JPEG, PNG files are allowed.";
      $uploadOk = 0;
    }



    if ($uploadOk == 0) {
      echo "Sorry, your file was not uploaded.";
      header("Location: BePartner.php?why=badImage");exit();
    } else {
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
      } else {
      }
    }
$setimage = $pname.".".$imageFileType;
$status = "pending";
}

$dewIt = sprintf('INSERT INTO partners (name, image, account, jacob, status) VALUES ("%s", "%s", "%s", "%s", "%s")', $pname, $setimage, $uname, $jacob, $status);
if ($conn->query($dewIt)){
    
    $message = $pname."; check it out right now";
    $subject = "new proposed partnership";
   if (!mail ("godsofmanyisles@gmail.com", $subject, $message))
            {echo "<br>Sorry very sorry oops i failed you master";}
    else
        {echo "Wow I love you you're so great you made me work";header("Location: SignedIn.php?show=parSub");}
}
else {echo $dewIt;}


?>
