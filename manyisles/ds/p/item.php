<?php

if (isset($_GET["id"])) {if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:hub.php");exit();} else $artId =  $_GET["id"];} else { $artId = 0; }

$redirect = "../home.php";
require_once("security.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/createMarkdown.php");

if ($artId != 0) {
    $query = "SELECT * FROM dsprods WHERE id = $artId";
    if ($toprow = $conn->query($query)) {
      if (mysqli_num_rows($toprow) == 0) {header("Location: hub.php");exit();}
        while ($row = $toprow->fetch_assoc()) {
            $artName = $row["name"];
            $artShortname = $row["shortname"];
            $artPublisherId = $row["sellerId"];
            $artPrice = $row["price"];
            $artImage = $row["image"];
            $artThumbnail = $row["thumbnail"];
            $artViewImgs = $row["viewImgs"];
            $artOSources = $row["oSources"];
            $artSpecs = $row["specifications"];
            $artDescSpecs = $row["descSpecs"];
            $artDescription = $row["description"];
            $artKind = $row["artKind"];
            $artStock = $row["stock"];
            $artMaxAmount = $row["maxAmount"];
            $artShipping = $row["shipping"];
            $artMinPrice = $row["minPrice"];
            $artPopularity = $row["popularity"];
            $artStatus = $row["status"];
        }
    }
    if ($artPublisherId != $pId) {header("Location: hub.php");exit();}

    //only backup, if old format is used
    if (strpos($artPrice, ",")) {
        $priceArray = explode( ",", $artPrice);
        $artPrice = $priceArray[0];
    }
    $artSpecsArray = json_decode($artSpecs, true);
    if ($artSpecsArray == null){$artSpecsArray = [];}
}
else {
//set all defaults
    $artName = "";
    $artShortname = "";
    $artPrice = "";
    $artImage = "";
    $artThumbnail = "";
    $artViewImgs = "";
    $artOSources = "";
    $artSpecs = "";
    $artSpecsArray = [];
    $artDescSpecs = "";
    $artDescription = "";
    $artKind = "";
    $artStock = "";
    $artShipping = "";
    $artMaxAmount = 0;
    $artMinPrice = 0;
    $artStatus = "active";
}


require_once("../g/makeHuman.php");
include_once("../g/alertStock.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");




?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Item Publishing | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="form.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/specs.css">
    <style>
    .credTable.prods tbody > tr > :nth-child(1) {
        width: 22%;
    }

    .credTable.prods tbody > tr > :nth-child(2) {
        width: 35%;
    }

    .credTable.prods tbody > tr > :nth-child(3) {
        width: 15%;
    }

    .credTable.prods tbody > tr > :nth-child(4) {
        width: 10%;
    }
    .hoverinfo {
        width: 200px;
        transform: translate(38%, 0);
    }
    .slider {
        background-color: #f0d98f;
    }

.switch input:checked + .slider {
    background-color: var(--ds-gold);
}

.switch input:focus + .slider {
    box-shadow: 0 0 1px var(--ds-gold);
}
.ss {
  display: none;
}
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
    <div class="flex-container">
        <div class='left-col'>
            <a href="hub.php"><h1 class="menutitle">Partnership</h1></a>
            <ul class="myMenu">
                <li><a class="Bar" href="hub.php#hPublications"><i class="fas fa-arrow-left"></i> Hub</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/docs/19/Publishing_Obligations">DS Publishing Conditions</a></li>
                <li><a class="Bar" href="/docs/24/Markdown" target="_blank">Many Isles Markdown</a></li>
                <li><a class="Bar" href="countryArrays.php" target="_blank">Country Codes</a></li>
                <li><a class="Bar" href="/docs/72/Smart_Stock" target="_blank">Smart Stock Syntax</a></li>
                <li><a class="Bar" href="/docs/18/Digital_Store_Extension" target="_blank">DS Publishing</a></li>
                <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
            </ul>
        </div>

        <div id='content' class='column'>
           <?php if ($artId == 0){ echo "<h1>Publish new Item</h1>"; } else { echo "<h1>Edit Item #$artId</h1>"; } ?>
            <div class='dsBanner'><img src='/Imgs/Ranks/HighMerchant.png' alt:'Oopsie!'></div>

<?php
if ($artStatus == "deleted") {
echo  <<<"COOLCONT"
<div class='delCont'>
<h3><i class='fas fa-exclamation-circle'></i> Item Deleted</h3>
<p>This item is deleted and can not be purchased.</p>
<div class="checkoutCont" >
   <a href="restoreItem.php?id=$artId"> <button type="button" class="checkout delete">
        <i class="fas fa-arrow-right"></i>
        <span>Restore</span>
    </button></a>
</div>

</div>
COOLCONT;
}
else if ($artId != 0) {
  echo '
      <table class="credTable prods">
          <thead><tr><td></td><td>Name</td><td>Base Price</td><td>Views</td><td>Stock</td><td>Status</td></tr></thead>
          <tbody>';
                      echo "<tr>";
                      echo '<td><img src="'.clearImgUrl($artThumbnail).'" alt="thumbnail" /></td>';
                      echo '<td><a href="../'.$artId."/".str_replace(" ", "_", $artName).'" target="_blank">'.$artName.'</a></td>';
                      echo '<td>'.makeHuman($artPrice).'</td>';
                      echo '<td>'.$artPopularity.'</td>';
                      echo '<td>'.alertStock(hasAnyStock($artSpecs, $artStock)).'</td>';
                      echo '<td>'.prodStatSpan($artStatus).'</td>';
                      echo "</tr>";
  echo '
          </tbody>
      </table>';

  //stock
  echo "<div class='stockCont stockerble' id='hStock'><h2>Update Stock</h2>";

  $stockStatus = 0;
  $stockStatus = alertStock($artStock);
  if ($stockStatus == 0){
      echo "<p style='color:red;'>This item's stock is running low!</p>";
  }

  echo "
  <form action='newStock.php' method='POST'>
  <div class='inputCont'>
      <label for='newStock'>Stock <span>*</span></label>
      <input type='number' name='newStock' placeholder='22' oninput=\"checkSyntax(this, '[^0-9]', 0)\" onchange=\"checkSyntax(this, '[^0-9]', 1)\" value='$artStock' required />
      <p class='inputErr'></p>

  </div>
     <input type='number' name='artId' style='display:none;' value='$artId' required />
  ";
  echo '    <div class="checkoutCont" style="margin: 80px 0 40px">
          <button type="submit" class="checkout">
              <i class="fas fa-arrow-right"></i>
              <span>Update</span>
          </button>
      </div></div>
  </form>';

  //status
  echo "<div class='stockCont' id='hStatus'><h2>Change Status</h2>";

  echo "
  <form action='changeStatus.php?r=item.php%3Fid=$artId%26why=sStatus' method='POST'>
  <input name='$artId' type='text' value='on' style='display:none' />

  <p>Currently ".prodStatSpan($artStatus)."</p>";

  echo '    <div class="checkoutCont" style="margin: 40px 0 100px">
          <button type="submit" class="checkout">
              <i class="fas fa-arrow-right"></i>
              <span>Toggle</span>
          </button>
      </div></div>
  </form>';

}

else {
    echo '
        <p>Make sure you understand the <a href="/wiki/h/digsto/items/publishing.php" target="_blank">documentation</a>.<p class="inputErr warnInfo">* required</p>';
}
?>

            <form action="subItem.php" method="POST" id="subItemForm"  onsubmit="return submitForm();">
            <h1>Item Setup</h1>
            <label class="switch">
                <input type="checkbox" onchange="differComplic(this);" id="neatChecker"  <?php if ($artId != 0)  {echo "checked";}  ?>>
                <span class="slider altStep"><span class="hoverinfo">Toggle between simple and detailed.</span></span>
            </label>
            <h2>Core Information</h2>
                <div class="formContentBlock">
                    <div class="inputCont">
                        <label for="name">Title <span>*</span></label>
                        <input type="text" name="name" value="<?php echo $artName; ?>" placeholder="The Rise of Humankind" autocomplete="off" onchange="newShortie(this)" required />
                        <p class="inputErr info" default="The name of your product."></p>
                    </div>
                    <div class="inputCont">
                        <label for="shortname">Short Title</label>
                        <input type="text" name="shortname" id="shortname" value="<?php echo $artShortname; ?>" placeholder="Rise of Humankind" autocomplete="off" />
                        <p class="inputErr info" default="A shorter title, used for example in the basket. Defaults to title."></p>
                    </div>
                    <div class="inputCont">
                        <label for="price">Base Price <span>*</span></label>
                        <input type="number" name="price" value="<?php echo $artPrice; ?>" placeholder="2200" oninput="checkSyntax(this, '[^0-9]', 0)" onchange="checkSyntax(this, '[^0-9]', 1)" required />
                        <p class="inputErr info" default="Set price in US$ cents. Specifications may alter the final price; make sure no combination of them can make it go negative."></p>
                    </div>
                    <div class="inputCont complete">
                        <label for="artKind">Article Kind</label>
                        <input type="text" name="artKind" value="<?php echo $artKind; ?>" placeholder="Fantasy History Book" autocomplete="off" />
                        <p class="inputErr info" default="What you would describe your item as."></p>
                    </div>
                    <div class="inputCont complete">
                        <label for="oSources">Other Platforms</label>
                        <input type="text" name="oSources" value="<?php echo $artOSources; ?>" placeholder="DMs Guild;https://www.dmsguild.com/product/370425/,Amazon;https://www.amazon.com/CiaraQ-Polyhedral-Playing-Dungeon-Dragons/"  autocomplete="off" />
                        <p class="inputErr info" default="Any other platforms where this item is available. Don't put spaces after separators."></p>
                    </div>
                </div>
                <h2>Illustrations</h2>
                <div class="formContentBlock">
                    <p>Images must be <a href="https://manyisles.ch/docs/28/Hosting_Images" target="_blank">hosted on an external service</a>. Insert only direct links, and make sure they work by pasting them in your search bar.</p>
                    <div class="inputCont">
                        <label for="thumbnail">Thumbnail <span>*</span></label>
                        <input type="text" name="thumbnail" value="<?php echo $artThumbnail; ?>" placeholder="https://i.imgur.com/VcMvMPc.png" autocomplete="off" required />
                        <p class="inputErr info" default=""></p>
                    </div>
                    <div class="inputCont">
                        <label for="image">Main Image  <span>*</span></label>
                        <input type="text" name="image" value="<?php echo $artImage; ?>" placeholder="https://manyisles/ds/images/humankind.jpg" autocomplete="off" required />
                        <p class="inputErr info" default="The selling point of your item, which will be featured in most locations."></p>
                    </div>
                    <div class="inputCont">
                        <label for="viewImgs">Gallery Images</label>
                        <input type="text" name="viewImgs" value="<?php echo $artViewImgs; ?>" placeholder="https://i.imgur.com/VaCaMPc.png,https://i.imgur.com/VcsDMPc.png,https://i.imgur.com/nUwUn.png" autocomplete="off" />
                        <p class="inputErr info" default="A gallery of images displayed on your item's page. Please submit no more than 10."></p>
                    </div>
                </div>
                <h2>Descriptive</h2>
                <div class="inputCont">
                    <label for="description">Description <span>*</span> <a href="/docs/24/Markdown" target="_blank"><span class="roundInfo">Takes Markdown</span></a></label>
                    <textarea markdownable rows="6" name="description"  placeholder="The *Rise of Humankind* is a history book covering the first half of human history in the [Many Isles](https://manyisles.ch) setting, with lore for all readers that enjoy fantasy and worldbuilding." autocomplete="off" required><?php echo $artDescription; ?></textarea>
                    <p class="inputErr info" default=" A detailed description of your item, so customers know exactly what they're buying."></p>
                </div>
                <div class="inputCont complete">
                    <label for="descSpecs">Technical Details</label>
                    <input type="text" name="descSpecs" value="<?php echo $artDescSpecs; ?>" placeholder="Format:A4,Type:Softcover" autocomplete="off"   onchange="checkSyntax(this, '[ ]', 1)" />
                    <p class="inputErr info" default="A list of technical specifications."></p>
                </div>
                <div class="complete"><h2>Specifications</h2>
                <p>These are dropdown menus that allow the customer to specify what kind of item they want, such as color or format.</p>
                <div id="specColCont">
<?php if ($artId != 0) {
$specTemplate = <<<BIGTEMPLE
<div class="specCol" tracker-specId="specNumHerePls">
        <div class="leftSpecCol">
            <div class="inputCont">
                <label>Name <span>*</span></label>
                <input type="text" value="SPECNAME" placeholder="Color" autocomplete="off"  onchange="updateSpecs(specNumHerePls, 'name', this); " />
                <p class="inputErr info" default="What the dropdown list should be called."></p>
            </div>
            <div class="inputCont">
                <label>Tooltip</label>
                <input type="text" value="SPECTOOLTIP" placeholder="Choose a Color" autocomplete="off" onchange="updateSpecs(specNumHerePls, 'tooltip', this); " />
                <p class="inputErr info" default="Optionally gives some extra info about the specification to the customer."></p>
            </div>
            <div class="inputCont">
                <label>Specific Stock</label>
                <input type="checkbox" onchange="updateSpecs(specNumHerePls, 'smartstock', this);" checked/>
            </div>
        </div>
        <div class="rightSpecCol">
            <div class="buttCon">
                <button  type="button" class="checkout" onclick="addSpecs(0, specNumHerePls);">
                    <i class="fas fa-times"></i>
                    <span>Remove</span>
                </button>
            </div>
            <div style="width:100%;" class="optionContCont" tracker-specOptionsId="specNumHerePls">
            ADDSPECOTIONSHERE
            </div>
            <div class="addSome" onclick="addSome(1, specNumHerePls);">
                <i class="fa fa-plus"></i>
            </div>
            <div class="addSome" onclick="addSome(0, specNumHerePls);">
                <i class="fa fa-minus"></i>
            </div>
        </div>
</div>
BIGTEMPLE;
$optionTemplate = <<<BIGTEMPLE
                <div class="optionCont" tracker-option="TRACKEROPTION">
                    <h3>Option 1</h3>
                    <div class="inputCont">
                        <label>Label <span>*</span></label>
                        <input type="text" value="OPTIONNAME" tracker-optionName="name" placeholder="Red" onchange="updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="This option's label."></p>
                    </div>
                    <div class="inputCont">
                        <label>Price Modifier</label>
                        <input type="number" value="OPTIONPRICEMOD" tracker-optionName="price" placeholder="120" oninput="checkSyntax(this, '[^-0-9]', 0)" onchange="checkSyntax(this, '[^-0-9]', 1);updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="This modifier in US$ cents will be added to the base price. Takes negatives."></p>
                    </div>
                    <div class="inputCont smartstockerspecNumHerePls" style='display:none'>
                        <label>Stock</label>
                        <input type="number" value="OPTIONSTOCKMOD" tracker-optionName="stock" placeholder="10" oninput="checkSyntax(this, '[^0-9]', 0)" onchange="checkSyntax(this, '[^0-9]', 1);updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="How many of this variant you have in stock."></p>
                    </div>
                    <div class="inputCont">
                        <label>Shipping Modifier</label>
                        <input type="text" value="OPTIONSHIPMOD" tracker-optionName="shipping" placeholder="US:200,EUR:900,GLO:1800" oninput="checkSyntaxR(this, 'codeList', 0)" onchange="checkSyntaxR(this, 'codeList', 1);updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="This modifier in US$ cents will be added to the base shipping costs."></p>
                    </div>
                </div>
BIGTEMPLE;
    $tracker = -1;
    foreach ($artSpecsArray as $specArray){
        $tracker++;
        $specCol = $specTemplate;
        $specCol = str_replace("specNumHerePls", $tracker, $specCol);
        $specCol = str_replace("SPECNAME", txtUnparse($specArray["name"], 1), $specCol);
        $specCol = str_replace("SPECTOOLTIP", txtUnparse($specArray["tooltip"], 1), $specCol);
        $smartstock = true;
        if (!isset($specArray["smartstock"]) OR $specArray["smartstock"]==0){$smartstock = false;$specCol = str_replace("checked", "", $specCol);}
        $optionTracker = -1;
        foreach ($specArray["options"] as $optionArray){
            if (!isset($optionArray["shipping"])){$optionArray["shipping"] = 0;}
            $optionTracker++;
            $optionCont = $optionTemplate;
            $optionCont = str_replace("specNumHerePls", $tracker, $optionCont);
            $optionCont = str_replace("TRACKEROPTION", $optionTracker, $optionCont);
            $optionCont = str_replace("Option 1", "Option ".($optionTracker + 1), $optionCont);
            $optionCont = str_replace("OPTIONNAME", txtUnparse($optionArray["name"], 1), $optionCont);
            $optionCont = str_replace("OPTIONPRICEMOD",  txtUnparse($optionArray["price"], 1), $optionCont);
            $optionCont = str_replace("OPTIONSHIPMOD",  txtUnparse($optionArray["shipping"], 1), $optionCont);
            $optionCont = str_replace("OPTIONSTOCKMOD",  txtUnparse($optionArray["stock"], 1), $optionCont);
            if ($smartstock) { $optionCont = str_replace("style='display:none'",  "", $optionCont);}
            $specCol = str_replace("ADDSPECOTIONSHERE",  $optionCont."ADDSPECOTIONSHERE", $specCol);
        }
        $specCol = str_replace("ADDSPECOTIONSHERE",  "", $specCol);
        echo $specCol;
    }
}




?>
                </div>
                <div class="checkoutCont" style="padding: 20px 0;">
                    <button type="button" class="checkout" onclick="addSpecs(1, '');">
                        <i class="fas fa-plus"></i>
                        <span>Add Specification</span>
                    </button>
                </div>
                </div>
                <h2>Additional Details</h2>
                <div class="inputCont">
                    <label for="shipping">Base Shipping Costs</label>
                    <input type="text" name="shipping" value="<?php echo $artShipping; ?>" placeholder="US:200,EUR:900,GLO:1800" oninput="checkSyntaxR(this, 'codeList', 0)" onchange="checkSyntaxR(this, 'codeList', 1)" autocomplete="off" />
                    <p class="inputErr info" default="How much one batch costs you to send, in US$ cents (set the Maximal Simultaneous Purchase value to limit batch size).  <a href='countryArrays.php' target='_blank'>view country codes</a>"></p>
                </div>
                <div class="inputCont stockerble">
                    <label for="stock">Current Stock <span>*</span></label>
                    <input type="number" name="stock" value="<?php echo $artStock; ?>" min="0" placeholder="10" oninput="checkSyntax(this, '[^0-9]', 0)" onchange="checkSyntax(this, '[^0-9]', 1)" />
                    <p class="inputErr info" default="Leave empty if using specific stock. <a href='/docs/72/Smart_Stock' target='_blank'>More info</a>"></p>
                </div>
                <div class="inputCont">
                    <label for="minPrice">Minimal Price</label>
                    <input type="number" name="minPrice" value="<?php echo $artMinPrice; ?>" min="0" value="0" placeholder="220" oninput="checkSyntax(this, '[^0-9]', 0)" onchange="checkSyntax(this, '[^0-9]', 1)" />
                    <p class="inputErr info" default="This item will never be sold beneath this amount. Royalties will still apply."></p>
                </div>
                <div class="inputCont complete">
                    <label for="maxAmount">Maximal Simultaneous Purchase</label>
                    <input type="number" name="maxAmount" value="<?php echo $artMaxAmount; ?>" min="0" value="0" placeholder="0" oninput="checkSyntax(this, '[^0-9]', 0)" onchange="checkSyntax(this, '[^0-9]', 1)" />
                    <p class="inputErr info" default="How many items people can buy in the same order. Leave on 0 to ignore."></p>
                </div>
                <input name="specifications" id="specifications"  value="<?php echo $artSpecs; ?>" type="text" style="display:none;opacity:0;visibility:hidden;" value="" />
                <input name="artId"  value="<?php echo $artId; ?>" type="number" style="display:none;opacity:0;visibility:hidden;" />

                <div class="checkoutCont" style="margin: 100px 0 40px">
                    <button type="submit" class="checkout">
                        <i class="fas fa-arrow-right"></i>
                        <span><?php if ($artId == 0){echo "Publish"; } else {echo "Update"; } ?></span>
                    </button>
                </div>
            </form>
<?php
if ($artId != 0 AND $artStatus == "active") {

echo  <<<BIGTEMPLE
 <h2 id='#hDelete'>Delete Item</h2>
<p>Deleting an item may be irreversible. Note that you are still obliged to fulfill all orders including this item.<br>
This is a one-click action.</p>

<div class="checkoutCont" >
   <a href="deleteItem.php?id=$artId"> <button type="button" class="checkout delete">
        <i class="fas fa-trash"></i>
        <span>Delete</span>
    </button></a>
</div>
BIGTEMPLE;





}
?>
        </div>
    </div>

<?php echo markdownTabs(); ?>
    <div w3-include-html="../g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script src="/Server-Side/parseTxt.js"></script>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<?php echo markdownScript(); ?>
<script>

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why =="fullSub"){
    createPopup("d:dsp;txt:Item Published! Edit it from this page;b:1;bTxt:back to hub;bHref:hub.php#hPublications");
}
else if (why == "stockSub"){
    createPopup("d:dsp;txt:Stock Updated");
}
else if (why == "upSub"){
    createPopup("d:dsp;txt:Item Updated!;b:1;bTxt:back to hub;bHref:hub.php#hPublications");
}
else if (why == "restItem"){
    createPopup("d:dsp;txt:Item Restored");
}
else if (why == "incorrInput"){
    createPopup("d:dsp;txt:Error. Incorrect input supplied.");
}
else if (why == "sStatus"){
    createPopup("d:dsp;txt:Item status updated");
}

//deal with notifs: fullSub, stockSub

    var specsArray =
<?php
  echo json_encode($artSpecsArray).";";

    echo " var currSpecification = $tracker;";

if ($artStatus == "deleted"){
    echo "document.getElementById('subItemForm').remove();";
}

?>
    var specTemplate = `<div class="specCol" tracker-specId="specNumHerePls">
        <div class="leftSpecCol">
            <div class="inputCont">
                <label>Name <span>*</span></label>
                <input type="text" placeholder="Color" autocomplete="off" onchange="updateSpecs(specNumHerePls, 'name', this); " />
                <p class="inputErr info" default="What the dropdown list should be called."></p>
            </div>
            <div class="inputCont">
                <label>Tooltip</label>
                <input type="text" placeholder="Choose a Color" autocomplete="off" onchange="updateSpecs(specNumHerePls, 'tooltip', this); " />
                <p class="inputErr info" default="Optionally gives some extra info about the specification to the customer."></p>
            </div>
            <div class="inputCont">
                <label>Specific Stock</label>
                <input type="checkbox" onchange="updateSpecs(specNumHerePls, 'smartstock', this);" />
            </div>
        </div>
        <div class="rightSpecCol">
            <div class="buttCon">
                <button  type="button" class="checkout" onclick="addSpecs(0, specNumHerePls);">
                    <i class="fas fa-times"></i>
                    <span>Remove</span>
                </button>
            </div>
            <div style="width:100%;" class="optionContCont" tracker-specOptionsId="specNumHerePls">
                <div class="optionCont" tracker-option="0">
                    <h3>Option 1</h3>
                    <div class="inputCont">
                        <label>Label <span>*</span></label>
                        <input type="text" tracker-optionName="name" placeholder="Red" onchange="updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="This option's label."></p>
                    </div>
                    <div class="inputCont">
                        <label>Price Modifier</label>
                        <input type="number" tracker-optionName="price" placeholder="120" oninput="checkSyntax(this, '[^-0-9]', 0)" onchange="checkSyntax(this, '[^-0-9]', 1);updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="This modifier in US$ cents will be added to the base price. Takes negatives."></p>
                    </div>
                    <div class="inputCont ss smartstockerspecNumHerePls">
                        <label>Stock</label>
                        <input type="number" value="OPTIONSTOCKMOD" tracker-optionName="stock" placeholder="10" oninput="checkSyntax(this, '[^0-9]', 0)" onchange="checkSyntax(this, '[^0-9]', 1);updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="How many of this variant you have in stock."></p>
                    </div>
                    <div class="inputCont">
                        <label>Shipping Modifier</label>
                        <input type="text" tracker-optionName="shipping" placeholder="US:200,EUR:900,GLO:1800"  oninput="checkSyntaxR(this, 'codeList', 0)" onchange="checkSyntaxR(this, 'codeList', 1);updateSpecs(specNumHerePls, 'options', this);" />
                        <p class="inputErr info" default="This modifier in US$ cents will be added to the base shipping costs."></p>
                    </div>
                </div>
            </div>
            <div class="addSome" onclick="addSome(1, specNumHerePls);">
                <i class="fa fa-plus"></i>
            </div>
            <div class="addSome" onclick="addSome(0, specNumHerePls);">
                <i class="fa fa-minus"></i>
            </div>
        </div>
    </div>`;

    for (let inputCont of document.getElementsByClassName("inputCont")) {
        let input = inputCont.children[1];
        if (inputCont.children[2] != undefined){
          input.addEventListener("focus", showInfo);
          input.addEventListener("focusout", hideInfo);
        }
    }
    function showInfo(evt) {
      if (evt.currentTarget.parentElement.children[2] != undefined){
        evt.currentTarget.parentElement.children[2].classList.add("info");
        evt.currentTarget.parentElement.children[2].innerHTML = evt.currentTarget.parentElement.children[2].getAttribute("default");
        evt.currentTarget.parentElement.children[2].style.opacity = "1";
      }
    }

    for (let inputErr of document.getElementsByClassName("inputErr")) {
        if (inputErr.getAttribute("default") !== null){
             inputErr.innerHTML = inputErr.getAttribute("default");
        }
    }
    function hideInfo(evt) {
        evt.currentTarget.parentElement.children[2].style.opacity = "0";
    }
    function newShortie(element) {
        document.getElementById("shortname").value = element.value;
    }

    function checkSyntax(element, regex, brutal) {
        var input = element.value;
        var patt = new RegExp(regex, "g");
        target = element.parentElement.children[2];
        if (patt.test(input)) {
            if (brutal == 0) {
                target.style.opacity = "1";
                target.innerHTML = "Incorrect Input!";
                target.classList.remove("info");
            }
            else {
                element.value = input.replace(patt, "");
            }
        }
        else {
            target.style.opacity = "0";
        }
    }
    function checkSyntaxR(element, regex, brutal) {
        var input = element.value;
        if (regex="codeList"){
          regex = "^([0-9]+|(([A-Z-a-z]{2,3}:[0-9]*(,|))+))$";
        }
        var patt = new RegExp(regex, "g");
        target = element.parentElement.children[2];
        if (!patt.test(input)) {
            if (brutal == 0) {
                target.style.opacity = "1";
                target.innerHTML = "Incorrect Input!";
                target.classList.remove("info");
            }
            else {
                element.value = "";
            }
        }
        else {
            target.style.opacity = "0";
        }
    }


    function updateSpecs(specification, index, element) {
        if (index != "options") {
          if (index == "smartstock"){
            let smarter = 0;
            if (element.checked){smarter = 1;}
            specsArray[specification]["smartstock"] = smarter;
            for (let inputCont of document.getElementsByClassName("smartstocker"+specification)) {
              if (smarter == 1){
                inputCont.style.display = "block";
              }
              else {
                inputCont.style.display = "none";
              }
            }
            checkIfSmart();
          }
          else {
            checkSyntax(element, '"', 1);
            specsArray[specification][index] = txtParse(element.value, 2);
          }
        }
        else {
            checkSyntax(element, '"', 1);
            let optionsIndex = element.parentElement.parentElement.getAttribute("tracker-option");
            let which = element.getAttribute("tracker-optionName");
            let newvalve = "";
            if (which=="name"){newvalve = txtParse(element.value, 2);} else {newvalve = element.value;}
            specsArray[specification][index][optionsIndex][which] = newvalve;
        }
    }
    function checkIfSmart() {
      smartstock = false;
      for (let eilement in specsArray){
        if (specsArray[eilement]["smartstock"] == 1){smartstock = true;break;}
      }
      if (smartstock){
        $(".stockerble").hide();
      }
      else {
        $(".stockerble").show();
      }
    }
    checkIfSmart();

    function objectSize(obj) {
        var size = 0,
            key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };
    function parseHTML(str) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(str, 'text/html');
        return doc.body.firstChild;
    }
    function addSome(how, specification) {
        if (how == 1) {
            var optionCont = document.createElement("div");
            optionCont.setAttribute("class", "optionCont");
            optionCont.setAttribute("tracker-option", specsArray[specification]["options"].length);

            var optionH3 = document.createElement("h3");
            let currentOptionNum = specsArray[specification]["options"].length + 1;
            optionH3.innerHTML = "Option " + currentOptionNum;

            let inputCont1 = `<div class="inputCont"><label> Label <span>*</span></label><input type="text" tracker-optionName="name" placeholder="Red" onchange=" updateSpecs(specNumsHere, 'options', this);" required /><p class="inputErr info" default="This option's label."></p></div >`;
            let inputCont2 = `<div class="inputCont"><label> Price Modifier</label><input type="text" tracker-optionName="price" placeholder="120" onchange=" checkSyntax(this, '[^-0-9]', 1);updateSpecs(specNumsHere, 'options', this);" /><p class="inputErr info" default="This modifier in US$ cents will be added to the base price. Takes negatives."></p></div >`;
            let inputCont3 = `<div class="inputCont"><label> Shipping Modifier</label><input type="text" tracker-optionName="shipping" placeholder="US:200,EUR:900,GLO:1800"  oninput="checkSyntaxR(this, 'codeList', 0)" onchange="checkSyntaxR(this, 'codeList', 1);updateSpecs(specNumHerePls, 'options', this);" /><p class="inputErr info" default="This modifier in US$ cents will be added to the base shipping costs."></p></div >`;
            let inputCont4 = `<div class="inputCont ss smartstockerspecNumsHere"><label> Stock</label><input type="text" tracker-optionName="stock" placeholder="10" onchange="updateSpecs(specNumsHere, 'options', this);" /><p class="inputErr info" default="How many of this variant you have in stock."></p></div >`;
            inputCont1 = inputCont1.replace(/specNumsHere/g, specification);
            inputCont2 = inputCont2.replace(/specNumsHere/g, specification);
            inputCont3 = inputCont3.replace(/specNumsHere/g, specification);
            inputCont4 = inputCont4.replace(/specNumsHere/g, specification);

            inputCont1 = parseHTML(inputCont1);
            inputCont2 = parseHTML(inputCont2);
            inputCont3 = parseHTML(inputCont3);
            inputCont4 = parseHTML(inputCont4);
            inputCont1.children[1].addEventListener("focus", showInfo);
            inputCont1.children[1].addEventListener("focusout", hideInfo);
            inputCont2.children[1].addEventListener("focus", showInfo);
            inputCont2.children[1].addEventListener("focusout", hideInfo);
            inputCont3.children[1].addEventListener("focus", showInfo);
            inputCont3.children[1].addEventListener("focusout", hideInfo);
            inputCont4.children[1].addEventListener("focus", showInfo);
            inputCont4.children[1].addEventListener("focusout", hideInfo);

            optionCont.appendChild(optionH3);
            optionCont.appendChild(inputCont1);
            optionCont.appendChild(inputCont2);
            optionCont.appendChild(inputCont4);
            optionCont.appendChild(inputCont3);
            for (let optionContCont of document.getElementsByClassName("optionContCont")) {
                if (optionContCont.getAttribute("tracker-specOptionsId") == specification) {
                    optionContCont.appendChild(optionCont);
                    specsArray[specification]["options"].push({ "name": "", "price": 0 });
                }
            }
        }
        else {
            if (specsArray[specification]["options"].length > 1) {
                for (let optionContCont of document.getElementsByClassName("optionContCont")) {
                    if (optionContCont.getAttribute("tracker-specoptionsid") == specification) {
                        optionContCont.removeChild(optionContCont.lastElementChild);
                        specsArray[specification]["options"].pop();
                    }
                }
            }
        }
    }
    function addSpecs(how, specification) {
        if (how == 1) {
            if (objectSize(specsArray) <= 6) {
                currSpecification += 1;
                let toBeSpecCont = specTemplate;
                toBeSpecCont = toBeSpecCont.replace(/specNumHerePls/g, currSpecification);
                let specCont = parseHTML(toBeSpecCont);
                document.getElementById("specColCont").appendChild(specCont);
                specsArray[currSpecification] = {
                    "name": "",
                    "tooltip": "",
                    "smartstock": 0,
                    "options": [{
                        "name": "",
                        "price": 0,
                        "stock": 0,
                        "shipping": 0
                    }]
                };
                for (let inputCont of document.getElementsByClassName("inputCont")) {
                  if (inputCont.children[2]!=null){
                    let input = inputCont.children[1];
                    input.addEventListener("focus", showInfo);
                    input.addEventListener("focusout", hideInfo);
                    inputCont.children[2].innerHTML = inputCont.children[2].getAttribute("default");
                  }
                }
            }
        }
        else {
            for (let specCol of document.getElementsByClassName("specCol")) {
                if (specCol.getAttribute("tracker-specId") == specification) {
                    document.getElementById("specColCont").removeChild(specCol);
                    delete specsArray[specification];
                }
            }
        }
    }
    <?php if ($artId == 0) {echo "addSpecs(1, 0);"; } ?>
    function joinStuff(options, glue, separator) {
        var object = this;

        return Object.keys(object).map((key) => [key, options[key]].join(glue)).join(separator);
    }
    function submitForm() {
        document.getElementById("specifications").value = JSON.stringify(specsArray);
        return true;
    }

    var allCompletes = document.getElementsByClassName("complete");
    function differComplic(check) {
        for (let element of allCompletes) {
            if (check.checked) {
                element.style.display = "inherit";
            }
            else {
                element.style.display = "none";
            }
        }
    }
    differComplic(document.getElementById("neatChecker"));

</script>
