<?php
if (preg_match("/[^A-Za-z0-9'\- ]/", $_POST["nname"])==1){header("Location: BePartner.php?why=wrongTitle");exit();}
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
if(!isset($_COOKIE["loggedP"])){header("Location: Account.html?error=signingIn");exit();}


$id = $_COOKIE["loggedIn"];
$nname = $_POST['nname'];
$njacob = $_POST['njacob'];
$psw = $_POST['psw'];

$njacob = str_replace('"', '', $njacob);
$njacob = str_replace('<', '', $njacob);

$status = "";
$query = "SELECT * FROM accountsTable WHERE id = ".$id;
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $uname = $row["uname"];
      $checkpsw = $row["password"];
    }
    }
if (password_verify($psw, $checkpsw)!=1){header("Location: Publish.php?why=noPSW");exit();}
if ($njacob == $uname) {$njacob = "";}

$query = "SELECT * FROM partners WHERE account = '".$uname."'";
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        $pid = $row["id"];
        $pname = $row["name"];
        $status = $row["status"];
    }
    }
if ($status != "active"){header("Location: BePartner.php");exit();}

$checkrr = "";
if ($firstrow = $conn->query("SELECT * FROM partners WHERE name =".$nname)) {
    while ($row = $firstrow->fetch_row()) {
      $checkrr = $row[6];
    }
    }
if ($checkrr != ""){header("Location: Publish.php?why=present");exit();}



if ($njacob != "") {$insertation = 'UPDATE partners SET jacob = "'.$njacob.'"'." WHERE id =".$pid;$conn->query($insertation);}
if ($nname != "") {$insertation = 'UPDATE partners SET name = "'.$nname.'"'." WHERE id =".$pid;$conn->query($insertation);
    $query = 'SELECT id FROM products WHERE partner = "'.$pname.'"';
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        renameIt($row["id"], "products");
    }
    }
    $query = 'SELECT id FROM diggies WHERE partner = "'.$pname.'"';
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        renameIt($row["id"], "diggies");
    }
    }
    $query = 'SELECT id FROM art WHERE partner = "'.$pname.'"';
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
        renameIt($row["id"], "art");
    }
    }
}
function renameIt($id, $db){
    global $nname, $conn;
    $query = "UPDATE ".$db.' SET partner ="'.$nname.'" WHERE id = '.$id;
    $conn->query($query);
    echo $query."<br>";
}


if (isset($_FILES["image"])){
$realpath = realpath("Publish.php");
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
$realpath = $realpath."/uploads/PartProf/".$pname.".".$imageFileType;

$uploadOk = 1;
$target_file = $realpath;

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
      header($header."&why=badImage");exit();
    } else {
       unlink($target_file);
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $query = sprintf('DELETE FROM newSub WHERE what ="partImg" AND id="%s"', $pid);
        $conn->query($query);
        $query = sprintf('INSERT INTO newSub (what, type, id, file, partner) VALUES ("partImg", "z", "%s", "%s", "%s")', $pid, $pname.".".$imageFileType, $pid);
        $conn->query($query);

      } 
    }
    }
}




if ($oldFile == "no"){header("Location: Publish.php?why=duplicate");}else{header("Location: Publish.php?why=npSuccess");}
?>
