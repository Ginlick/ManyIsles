<?php
if (isset($_POST["artId"])) {if (preg_match("/^[0-9]*$/", $_POST["artId"])!=1){header("Location:hub.php");exit();} else $artId =  $_POST["artId"];} else { header("Location:hub.php");exit(); }

$redirect = "";
$checkArtId = true;
require_once("security.php");

$artName = inputChecker($_POST["name"], "/[\"]/", true);
$artPrice = inputChecker($_POST["price"], "/^[0-9]*$/", false);
$artImage = inputChecker($_POST["image"], "/[\"]/", true);
$artThumbnail = inputChecker($_POST["thumbnail"], "/[\"]/", true);
$artDescription = $_POST["description"];
$artDescSpecs = $_POST["descSpecs"];
$artShortname = inputChecker($_POST["shortname"], "/[\"]/", true);
$artViewImgs = inputChecker($_POST["viewImgs"], "/[\"]/", true);
$artOSources = inputChecker($_POST["oSources"], "/[\"]/", true);
$artSpecifications = inputChecker($_POST["specifications"], "/[\']/", true);
$artKind = inputChecker($_POST["artKind"], "/[\"]/", true);
if ($_POST["shipping"] == "") {$artShipping = ""; } else {$artShipping = inputChecker($_POST["shipping"], "/^([0-9]+|(([A-Z-a-z]{2,3}:[0-9]*(,|))+))$/", false);}
$artStock = inputChecker($_POST["stock"], "/^[0-9]*$/", false);
$artMaxAmount = inputChecker($_POST["maxAmount"], "/^[0-9]*$/", false);
$artMinPrice = inputChecker($_POST["minPrice"], "/^[0-9]*$/", false);

$artDescription = str_replace('"', '%double_quote%', $artDescription);
$artDescSpecs = str_replace('"', '%double_quote%', $artDescSpecs);


//preparing
$artSpecsArray = json_decode($artSpecifications, true);
//echo $artSpecifications;

$counter = 0;
foreach ($artSpecsArray as $key => $specArray){
    $counter++;
    if (!isset($specArray["name"]) OR !isset($specArray["options"])){
        unset($artSpecsArray[$key]);continue;
    }
    else if ($specArray["name"] == "" OR $specArray["options"] == ""){
        unset($artSpecsArray[$key]);continue;
    }
    if ($specArray["name"] == "") {
        $specArray["name"] = "Select $counter";
    }
    if (!isset($specArray["smartstock"])){$specArray["smartstock"] = 0;}
    $oCounter = 0;
    foreach ($specArray["options"] as $key2 => $optionArray){
        $oCounter++;
        if ($optionArray["name"] == "") {
            $optionArray["name"] ="Option $oCounter";
        }
        if ($optionArray["price"] == "") {
            $optionArray["price"] = 0;
        }
        if (!isset($optionArray["shipping"]) OR $optionArray["shipping"] == "") {
            $optionArray["shipping"] = 0;
        }
        if (!isset($optionArray["stock"]) OR $optionArray["stock"] == "") {
            $optionArray["stock"] = 0;
        }
        $specArray["options"][$key2] = $optionArray;
    }
    $artSpecsArray[$key] = $specArray;
}


$artSpecifications = json_encode($artSpecsArray);


$artName = substr($artName, 0, 100);
$artShortname = substr($artShortname, 0, 22);
if ($artPrice > 19999){$artPrice = 19999;}else if ($artPrice < 0){$artPrice = 0;}
if ($artMinPrice > 19999){$artMinPrice = 19999;}else if ($artMinPrice > 0 OR $artMinPrice == null                                                  ){$artMinPrice = 0;}
if ($artMaxAmount > 99){$artMaxAmount = 99;}else if ($artMaxAmount < 0){$artMaxAmount = 0;}
if ($artStock > 100){$artStock = 100;}

if ($artShortname == "") {
    $artShortname = $artName;
}

//effectuating
if ($artId == 0) {
    $query = 'INSERT INTO dsprods (name, seller, price, image, thumbnail, description, shortname, viewImgs, oSources, specifications, artKind, shipping, sellerId, descSpecs, stock, maxAmount, minPrice) VALUES ("artName", "artSeller", artPrice, "artImage", "artThumbnail", "artDescription", "artShortname", "artViewImgs", "artOSources", \'artSpecifications\', "artArtKind", "artShipping", artSellerId, "artDescSpecs", artStock, artMaxAmount, artMinPrice); ';
    $query = str_replace("artSellerId", $pId, $query);
    $query = str_replace("artSeller", $artSeller, $query);
}
else {
    $query = 'UPDATE dsprods SET name = "artName", price = artPrice, image = "artImage", thumbnail = "artThumbnail", description = "artDescription", shortname = "artShortname", viewImgs = "artViewImgs", oSources = "artOSources", specifications = \'artSpecifications\', artKind = "artArtKind", shipping = "artShipping", descSpecs = "artDescSpecs", stock = artStock, maxAmount = artMaxAmount, minPrice = artMinPrice WHERE id = artId';
    $query = str_replace("artId", $artId, $query);
}

$query = str_replace("artName", $artName, $query);
$query = str_replace("artPrice", $artPrice, $query);
$query = str_replace("artImage", $artImage, $query);
$query = str_replace("artThumbnail", $artThumbnail, $query);
$query = str_replace("artDescription", $artDescription, $query);
$query = str_replace("artShortname", $artShortname, $query);
$query = str_replace("artViewImgs", $artViewImgs, $query);
$query = str_replace("artOSources", $artOSources, $query);
$query = str_replace("artSpecifications", $artSpecifications, $query);
$query = str_replace("artShipping", $artShipping, $query);
$query = str_replace("artDescSpecs", $artDescSpecs, $query);
$query = str_replace("artStock", $artStock, $query);
$query = str_replace("artArtKind", $artKind, $query);
$query = str_replace("artMaxAmount", $artMaxAmount, $query);
$query = str_replace("artMinPrice", $artMinPrice, $query);

   echo $query;

if ($result = $conn->query($query)){
    if ($artId != 0){
        header("Location:item.php?why=upSub&id=".$artId);exit();
        $error = "Could not redirect";
    }
    else {
        $query = "SELECT id FROM dsprods ORDER BY id DESC LIMIT 0, 1";
        if ($firstrow = $conn->query($query)) {
            while ($row = $firstrow->fetch_assoc()) {
                header("Location:item.php?why=fullSub&id=".$row["id"]);exit();
            }
        }
    }
}
else {
    $error = $conn->error;
}



?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Error | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
    <div class="flex-container">
        <div class='left-col'>
            <a href="hub.php"><h1 class="menutitle">Partnership</h1></a>
            <ul class="myMenu">
                <li><a class="Bar" href="hub.php"><i class="fas fa-arrow-left"></i> Hub</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/wiki/h/publishing/ds.html" target="_blank">DS Publishing</a></li>
            </ul>
        </div>

        <div id='content' class='column'>
            <h1>Error Publishing Item </h1>
            <div class='dsBanner'><img src='/Imgs/Oops.png' alt:'Oopsie!'></div>
            <p>We are terribly sorry, but it appears your item could not be published. Feel free to report this problem to the Pantheon.
            <br><br><b>Error:</b><br><?php echo $error; ?>
            </p>
            <div class="checkoutBox" style="margin-bottom:0;" onclick="location.reload();">
                <button class="checkout" type="submit">
                    <i class="fas fa-redo"></i>
                    <span>Retry</span>
                </button>
            </div>
        </div>
    </div>


    <div w3-include-html="../g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>

</script>
