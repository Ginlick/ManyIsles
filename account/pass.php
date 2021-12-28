<?php

if (preg_match("/[^A-Za-z0-9]/", $_GET["what"])==1){header("Location: admin.php");exit();}

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
$what = $_GET["what"];
$todoid = $_GET["id"];

if ($id != 11 AND $id != 14 AND $id != 26 AND $id != 36) {
    header("Location: /account/SignedIn.php");exit();
}

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

$query = "SELECT id FROM partners WHERE account = '".$name."'";
    $result = $conn->query($query);
while ($row=$result->fetch_assoc()){
    $userpid = $row["id"];
}
if ($id == 14){$userpid = 0;}

if ($what == "part"){
    $query = "SELECT name, account, image FROM partners WHERE id = ".$todoid;
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $parname = $row["name"];
        $image = $row["image"];
        $paraccount = $row["account"];
      }
    }
    $query = "SELECT title, email FROM accountsTable WHERE uname = '".$paraccount."'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        if ($row["id"]==$id AND $id != 14){header("Location: admin.php?y=1");exit();}
        $partitle = $row["title"];
        $parmail = $row["email"];
      }
    }
}
else if ($what == "prod"){
    $query = "SELECT * FROM prodSub WHERE id = '".$todoid."'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $name = $row["name"];
        $image = $row["image"];
        $partnerId = $row["partner"];
        $type = $row["type"];
        $categs = $row["categs"];
        $tiers = $row["tiers"];
        $jacob = $row["jacob"];
        $link = $row["link"];
        $parmail = $row["email"];
        $smartCategories = $row["categories"];
        $supportProd = $row["support"];
        $gsystem = $row["gsystem"];
      }
    }
        //if ($partnerId==$userpid){header("Location: admin.php?y=1");exit();}
        $query = "SELECT name, account FROM partners WHERE id = ".$partnerId;
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
            $partner = $row["name"];
            $partnerAccount = $row["account"];
        }
      $db = "products";
      if ($type == "d"){$db = "diggies";}
      else if ($type == "a"){$db = "art";}
    }
}
else if ($what == "prodImg"){
    $query = "SELECT * FROM newSub WHERE ud=".$todoid;
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $type = $row["type"];
        $file = $row["file"];
        $partner = $row["partner"];
        $prodLink = $row["id"];
      }
    }
      if ($partner==$userpid){header("Location: admin.php?y=1");exit();}
      $db = "products";
      if ($type == "d"){$db = "diggies";}
      else if ($type == "a"){$db = "art";}
    $query = "SELECT image FROM ".$db." WHERE id = '".$todoid."'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $image = $row["image"];
      }
    }
}
else if ($what == "prodFile"){
    $query = "SELECT * FROM newSub WHERE ud=".$todoid;
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $type = $row["type"];
        $file = $row["file"];
        $partner = $row["partner"];
        $prodLink = $row["id"];
      }
    }
      if ($partner==$userpid AND $id != 14 AND $id != 11){header("Location: admin.php?y=1");exit();}
      $db = "products";
      if ($type == "d"){$db = "diggies";}
      else if ($type == "a"){$db = "art";}
    $query = "SELECT link FROM ".$db." WHERE id=".$prodLink;
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $oldFile = $row["link"];
      }
    }
}
if ($what == "partImg"){
    $query = "SELECT * FROM newSub WHERE ud=".$todoid;
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $type = $row["type"];
        $file = $row["file"];
        $partner = $row["partner"];
        $prodLink = $row["id"];
      }
    }
    if ($partner==$userpid){header("Location: admin.php?y=1");exit();}
    $query = "SELECT image FROM partners WHERE id = ".$partner;
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $image = $row["image"];
      }
    }
}


$realpath = realpath("pass.php");
$realpath = dirname($realpath);
$realpath = dirname($realpath);
$realpath = dirname($realpath);
if ($what == "part"){
$realpath = $realpath."/uploads/PartProf/".$image;
}
else if ($what == "prod"){
    if ($type != "d"){
        $filepath = $realpath."/uploads/NewProd/".$link;
    }
$realpath = $realpath."/uploads/NewProd/".$image;
}
else if ($what == "prodImg"){
$realpath = $realpath."/uploads/OldProd/".$file;
}
else if ($what == "prodFile"){
$realpath = $realpath."/uploads/NewRFile/".$file;
}
else if ($what == "partImg"){
$realpath = $realpath."/uploads/PartProf/".$file;
}

$target_file = realpath("pass.php");
$target_file = dirname($target_file);
$target_file = dirname($target_file);
if ($what == "part"){
    $target_file = $target_file."/dl/PartIm/".$image;
}
else if ($what == "prod"){
        if ($type == "m"){
            $target_file2 = $target_file."/dl/Friiz/".$link;
        }
        else if ($type == "a"){
            $target_file2 = $target_file."/dl/Art/".$link;
        }
    $target_file = $target_file."/IndexImgs/".$image;
}
else if ($what == "prodImg"){
    $to_delete = $target_file."/IndexImgs/".$image;
    $target_file = $target_file."/IndexImgs/".$file;
    unlink($to_delete);
}
else if ($what == "prodFile"){
    $target_file =  $target_file."/dl/Friiz/";
    if ($type == "a"){$target_file = str_replace("Friiz", "Art", $target_file); }
    $to_delete = $target_file.$oldFile;
    $target_file = $target_file.$file;
    unlink($to_delete);
}
else if ($what == "partImg"){
    $target_file = $target_file."/dl/PartIm/";
    $to_delete = $target_file.$image;
    $target_file = $target_file.$file;
    unlink($to_delete);
}

$to = $parmail;
if ($what == "part"){
    $subject = "Welcome, Trader!";
    $message = <<<MYGREATMAIL
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <img src="http://manyisles.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
        <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Welcome, PARTNERSHIP!</h1>
        <p style="
            text-align: center;
            font-size: calc(8px + 0.9vw);
            color: black;
            border-bottom: 1.3px solid #ddd;
            padding:10px;
    ">
            By creating a companionship with the Many Isles, you've joined a thriving community. Present us your greatest achievements!
        </p>
        <div style="width:20%;float:left;display:block;position:relative">
            <img src="http://manyisles.ch/dl/PartIm/Pantheon.png" alt="Hello There!" style="width:100%;display:block;border-radius:15px;padding:9px;" />
        </div>
        <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;">Share your Stuff</h2>
        <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
            We have a great digital library that has collected thousands of views on many products, and hundreds of downloads. This customer base is now open to you!<br />
            Also join the Trader institution on our <a href="https://discord.gg/XwZWJxE">discord</a>, where you can make your ideas heard!
        </p>
        <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
        <div style="width:20%;float:left;display:block;position:relative">
            <img src="http://manyisles.ch/IndexImgs/Exotic.png" alt="Hello There!" style="        width: 100%;
            display: block;
            border-radius: 15px;
            padding: 9px;
    " />
        </div>
        <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;">Great Tools</h2>
        <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
            Thanks to your contributions, the Many Isles exist. As a return, we throw open all tools for you, such as the <a href="https://drive.google.com/drive/folders/1ngOo5Gfe7k-gxWBZourBSUWQunAxiGm6?usp=sharing">Merchant's Wagon</a>.
        </p>
        <img src="http://manyisles.ch/Imgs/Bar2.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
        <div style="width:20%;float:left;display:block;position:relative">
            <img src="http://manyisles.ch/IndexImgs/Spellcraft.png" alt="Hello There!" style="        width: 100%;
            display: block;
            border-radius: 15px;
            padding: 9px;
    " />
        </div>
        <h2 style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 2.5vw);color:#911414;margin-bottom:0px;">Be Responsible</h2>
        <p style="width:80%;float:left;position:relative;text-align:center;font-size:calc(8px + 0.9vw);color:black;margin-top:5px;margin-bottom:5px;">
            Although we grant you as a creator a special place among us, please follow the rules. Make sure you read through our <a href="https://manyisles.ch/wiki/h/publishing/pubguide.html">publication guide</a>, and that you agree with the <a href="https://docs.google.com/document/d/1Q1CqPuaHVOM2Bz9GsZQ9S9QvrRZmyMFVo6_Iu7fq2K8/edit?usp=sharing">Trader's Agreement</a>.
        </p>

    </body>
    </html>
MYGREATMAIL;
    $message = str_replace("PARTNERSHIP", $parname, $message);
}
else if ($what == "prod"){
   $subject = "Product Published";
    $message = <<<MYGREATMAIL
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <img src="http://manyisles.ch/Imgs/PopTrade.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
    <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">NICEPRODUCT was Published</h1>
    <p style="
        text-align: center;
        font-size: calc(8px + 0.9vw);
        color: black;
        padding:10px;
">
        The Homeland Institute of Trade accepted your product, and it is now publicly visible in the <a href="https://manyisles.ch/dl/Goods.php">digital library</a>. Thanks for your participation in the Many Isles!
    </p>
</body>
</html>
MYGREATMAIL;
    $message = str_replace("NICEPRODUCT", $name, $message);
}
$headers = "From: pantheon@manyisles.ch" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

echo $realpath;
echo $target_file;

if (rename($realpath, $target_file)) {
    if ($what == "prod") {
        if ($type != "d"){rename($filepath, $target_file2);}
        if ($type=="m"){
            $dewIt = sprintf('INSERT INTO %s (name, image, partner, type, tiers, jacob, link, categories, support, gsystem) VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", %s, %s)', $db, $name, $image, $partner, $categs, "g", $jacob, $link, $smartCategories, $supportProd, $gsystem);
        }
        else {
            $dewIt = sprintf('INSERT INTO %s (name, image, partner, type, tiers, jacob, link) VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s")', $db, $name, $image, $partner, $categs, "g", $jacob, $link);
        }
        if ($conn->query($dewIt)){
            $dewItAgain = "DELETE FROM prodSub WHERE id = ".$todoid;
            $conn->query($dewItAgain);
            echo $dewItAgain;
            mail ($to, $subject, $message, $headers);
            header("Location: admin.php");
            exit();
        }
        else {echo $dewIt;}
    }
    else if ($what == "part"){
        $query = "UPDATE partners SET status = 'active' WHERE id =".$todoid;
        $conn->query($query);
        $query = "UPDATE accountsTable SET title = 'Trader' WHERE uname = '".$paraccount."'";
        if ($partitle == "Adventurer" or $partitle == "Poet") {$conn->query($query);}
        mail ($to, $subject, $message, $headers);
        header("Location: admin.php");
        exit();
    }
    else if ($what == "prodImg"){
        $query = "DELETE FROM newSub WHERE ud=".$todoid;
        $conn->query($query);
        echo "<br>".$query;
        $query = "UPDATE ".$db.' SET image = "'.$file.'" WHERE id='.$prodLink;
        $conn->query($query);
        echo "<br>".$query;
        header("Location: admin.php");
        exit();
    }
    else if ($what == "prodFile"){
        $query = "DELETE FROM newSub WHERE ud=".$todoid;
        $conn->query($query);
        echo "<br>".$query;
        $query = "UPDATE ".$db.' SET link = "'.$file.'" WHERE id='.$prodLink;
        $conn->query($query);
        echo "<br>".$query;
        header("Location: admin.php");
        exit();
    }
    else if ($what == "partImg"){
        $query = "DELETE FROM newSub WHERE ud=".$todoid;
        $conn->query($query);
        echo "<br>".$query;
        $query = 'UPDATE partners SET image = "'.$file.'" WHERE id='.$prodLink;
        $conn->query($query);
        echo "<br>".$query;
        header("Location: admin.php");
        exit();
    }
}
else {echo "oof";}


?>
