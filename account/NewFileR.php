<?php
if (preg_match("/[0-9]+/", $_POST['rId'])!=1){header("Location: Publish.php?x=2");exit();}
if (preg_match("/[a-z]/", $_POST['rType'])!=1){header("Location: Publish.php?x=1");exit();}

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
$rId = $_POST['rId'];
$type = $_POST['rType'];
$redirect = "Location: Product.php?id=".$rId."&t=".$type;

$status = "";

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
    while ($row = $firstrow->fetch_assoc()) {
        $pid = $row["id"];
        $pname = $row["name"];
        $status = $row["status"];
    }
    }
if ($status != "active"){header("Location: SignedIn.php");exit();}

$query = "SELECT * FROM products WHERE id =".$rId;
if ($type == "d"){$query = "SELECT * FROM diggies WHERE id =".$rId;}
else if ($type == "a"){$query = "SELECT * FROM art WHERE id =".$rId;}

if ($prodrow = $conn->query($query)){
while ($row = $prodrow->fetch_assoc()) {
      $prodName = $row["name"];
      $prodPart = $row["partner"];
      $orProdFile = $row["link"];
    }
}
if ($prodPart != $pname){exit();}
if (isset($_FILES["file"])){echo $_FILES["file"]["tmp_name"];}

if ($type != "d"){
    $realpath = realpath("Publish.php");
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);
    $imageFileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));
    $realpath = $realpath."/uploads/NewRFile/".$prodName.".".$imageFileType;

    $uploadOk = 1;

    $target_file = $realpath;
    $oldFile = "yes";
    if ($_FILES["file"]["size"] > 35000000) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
    }

    if ($type == "p"){
        if($imageFileType != "pdf") {
          echo "Not a pdf.";
          $uploadOk = 0;
        }
    }
    else if ($type == "a"){
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
          echo "Not an image.";
          $uploadOk = 0;
        }
    }

    if ($uploadOk == 0) {
      echo "Sorry, your file was not uploaded.";
      header($redirect."&why=ffail");
      exit();
    } else {
      if (file_exists ($target_file)){unlink($target_file);}
        echo $_FILES["file"]["name"]."<br>";
        echo $_FILES["file"]["size"]."<br>";
        echo $_FILES["file"]["tmp_name"]."<br>";
        echo $_FILES["file"]["error"]."<br>";
        //echo "<br>".$target_file;
      if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
        $message = $pname."'s new file";
        $subject = "New File for ".$rId;
        mail ("godsofmanyisles@gmail.com", $subject, $message);
        $query = sprintf('DELETE FROM newSub WHERE id=%s AND what="prodFile" AND type="%s"', $rId, $type);
        $conn->query($query);
        $query = sprintf('INSERT INTO newSub (what, type, id, file, partner) VALUES ("prodFile", "%s", "%s", "%s", "%s")', $type, $rId, $prodName.".".$imageFileType, $pid);
        $conn->query($query);
        echo "<br>".$query;
        header($redirect."&why=fsuccess");
      }
      else {echo "ffail";}//header($redirect."&why=fbadtitle");}
    }
}
else if ($type == "d"){
    $query = sprintf('UPDATE diggies SET link = "%s" WHERE id = %s', $_POST["link"], $rId);
    if ($conn->query($query)) {header($redirect."&why=fsuccess");}
}

?>
