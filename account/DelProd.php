<?php
if(!isset( $_GET['id'])){header("Location: Publish.php");exit();}
$returnURL = "Location: Product.php?id=".$_GET['id']."&t=".$_GET['type'];
$returnFailed = $returnURL."&why=delFail";

if(!isset( $_GET['psw'])){header($returnFailed);exit();}
if (preg_match("/^[0-9]{1,}$/", $_GET['id'])!=1){header($returnURL);exit();}
if (preg_match("/[A-Za-z0-9]{1,}/", $_GET['psw'])!=1){header($returnFailed);exit();}
if(!isset($_COOKIE["loggedIn"])){header("Location: Account.html?error=notSignedIn");exit();}
if(!isset($_COOKIE["loggedP"])){header("Location: Account.html?error=notSignedIn");exit();}

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
$type = $_GET["type"];
$uname = "";

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
if($_GET['psw'] != $cpsw){header($returnFailed);exit();}

$query = "SELECT * FROM partners WHERE account = '".$uname."'";
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $pid = $row["id"];
      $pname = $row["name"];
      $pimage = $row["image"];
      $pjacob = $row["jacob"];
      $status = $row["status"];
    }
    }

if ($status != "active"){header("Location: BePartner.php");exit();}


$query = 'SELECT * FROM products WHERE id = '.$_GET['id'];
if ($type == "d"){$query = str_replace("products", "diggies", $query);}
if ($type == "a"){$query = str_replace("products", "art", $query);}
    if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $rimage = $row["image"];
      $rlink = $row["link"];
      $partner = $row["partner"];
    }
    }
if ($partner != $pname){header("Location: Publish.php");exit();}

$query = "DELETE FROM products WHERE id =".$_GET['id'];
if ($type == "d"){$query = str_replace("products", "diggies", $query);}
if ($type == "a"){$query = str_replace("products", "art", $query);}
$conn->query($query);

$realpath = realpath("admin.php");
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$delete1 = $realpath."/dl/Friiz/".$rlink;
$delete2 = $realpath."/IndexImgs/".$rimage;
unlink($delete1);
if ($type != "d"){unlink($delete2);}


header("Location: Publish.php");
$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
<p>If you see this, something's wrong.</p>
</body>
</html>