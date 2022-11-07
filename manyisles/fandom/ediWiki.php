<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");
if (preg_match("/[^0-9]/", $_POST['wId'])==1){header("Location:/fandom/home");exit();}
if (preg_match("/[^0-9]/", $_POST['dom'])==1){header("Location:/fandom/home");exit();}
if (preg_match('/[:;{}"]/', $_POST['backgroundCol'])==1){header("Location:/fandom/home");exit();}
$backgroundImg = str_replace('"', '', $_POST['backgroundImg']);
if (!checkRegger("cleanText2", $_POST["dateB"])){header("Location:/fandom/home");exit();}
if (!checkRegger("cleanText2", $_POST["dateA"])){header("Location:/fandom/home");exit();}
if (!checkRegger("cleanText2", $_POST["banner"])){header("Location:/fandom/home");exit();}
$styles = ""; if (isset($_POST['style'])) {$styles = $_POST["style"]; if (!checkRegger("cleanText2", $_POST["style"])){header("Location:/fandom/home");exit();} }
$parentWiki = $_POST['wId'];
$domain = $_POST['dom'];
$backgroundCol = $_POST['backgroundCol'];

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("act", 0, $parentWiki, false, $domain);
$conn = $gen->dbconn;
if ($gen->power < 3){header("Location:$gen->artRootLink/$gen->parentWiki/home");exit();}


$query="SELECT id FROM wiki_settings WHERE id = '$gen->WSet'";
$result =  $conn->query($query);
if ($result->num_rows == 0){
    $query = "INSERT INTO wiki_settings (id, mods) VALUES ('$gen->WSet', '$gen->user')"; $conn->query($query);
}


$oldBanner = $gen->defaultBanner; $oldStyle = "Mystral";
$query = "SELECT * FROM wiki_settings WHERE id = '$gen->WSet'";
$firstrow = $conn->query($query);
if (mysqli_num_rows($firstrow) != 0){
    while ($row = $firstrow->fetch_assoc()) {
        $oldBanner = $row["defaultBanner"];
        $oldStyle = $row["styles"];
    }
}

if ($gen->domain == "mystral"){
    if ($styles == "current"){$styles = $oldStyle;}
    if (!in_array($styles, $gen->styles)){$styles = "Mystral";}
    if (!in_array($oldStyle, $gen->styles)){$oldStyle = "Mystral";}
}
else {
    $styles = $gen->defaultStyle;
}

$dateArray = [
    "B" => $_POST["dateB"],
    "A" => $_POST["dateA"],
];
$dateArray = json_encode($dateArray);


if ($oldBanner == $gen->styleDefaults[$oldStyle]["banner"]) {$oldBanner = $gen->styleDefaults[$styles]["banner"];}
if ($backgroundImg == null OR $backgroundImg == $gen->styleDefaults[$oldStyle]["backgroundImg"]){$backgroundImg = $gen->styleDefaults[$styles]["backgroundImg"];}    
if ($backgroundCol == null OR $backgroundCol == $gen->styleDefaults[$oldStyle]["backgroundImg"]){$backgroundCol = $gen->styleDefaults[$styles]["backgroundColor"];}    




if ($_POST["banner"] == "current"){
    $banner = $oldBanner;
}
else if ($_POST["banner"] == "default"){
    $banner = $gen->styleDefaults[$styles]["banner"];
}
else {
    $banner = $_POST["banner"];
}




$query = 'UPDATE wiki_settings SET dateName = \'artdateName\', defaultBanner = "artdefaultBanner", backgroundImg = "artbackgroundImg", backgroundColor = "artbackgroundColor", styles = "artStyles" WHERE id = "'.$gen->WSet.'"';

$query = str_replace("artdateName", $dateArray, $query);
$query = str_replace("artdefaultBanner", $banner, $query);
$query = str_replace("artbackgroundColor", $backgroundCol, $query);
$query = str_replace("artbackgroundImg", $backgroundImg, $query);
$query = str_replace("artStyles", $styles, $query);

echo $query;

if ($conn->query($query)){
    header("Location:".$gen->baseLink."wsettings?i=bigup&w=$parentWiki");
}
else {
    echo "Error.";
}


?>