<?php
if (isset($_GET["id"])){if (preg_match("/[0-9]{1,}/", $_GET["id"])!=1){header("Location: /ds/store");exit();} else {$artId = $_GET["id"];} } else {header("Location: /ds/store");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/dsEngine.php");
$ds = new dsEngine; $conn = $ds->conn; $basketed = $ds->basketed;

$digital = false;
$query = "SELECT * FROM dsprods WHERE id = $artId";
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()){
        $artName = $row["name"];
        $artShortName = $row["shortname"];
        $artPublisher = $row["seller"];
        $artPublisherId = $row["sellerId"];
        $artPrice = $row["price"];
        $artImage = $row["image"];
        $artViewImgs = $row["viewImgs"];
        $artRegdate = $row["reg_date"];
        $artOSources = $row["oSources"];
        $artSpecs = $row["specifications"];
        $artDescSpecs = $row["descSpecs"];
        $artDescription = $row["description"];
        $artKind = $row["artKind"];
        $artShipping = $row["shipping"];
        $artStock = $ds->hasAnyStock($artSpecs, $row["stock"]);
        $artMaxAmount = $row["maxAmount"];
        $artMinPrice = $row["minPrice"];
        $artStatus = $row["status"];
        if ($row["digital"]==1){$digital = true;}
        if ($row["link"] != null){header("Location: ".$row["link"]);exit();}
        $artLink = "$artId/".str_replace(" ", "_", $artName);
        if ($artStatus == "deleted"){header("Location: /ds/store?why=itemDeleted");exit();}
    }
}
$query = "SELECT name, user FROM partners WHERE id = $artPublisherId";
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()){
        $artPublisher = $row["name"];
        $artPublisherAccId= $row["user"];
    }
}

if ($artPublisher == "Pantheon"){$artPublisher = "the Pantheon";}
$date_array = date_parse($artRegdate);
$artPubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];

//only backup, if old format is used
if (strpos($artPrice, ",")) {
    $priceArray = explode( ",", $artPrice);
    $artPrice = $priceArray[0];
}

//images
$artImgsArray = [];
if (!empty($artViewImgs)) {
    $artImgsArray[0] = $artImage;
    $nArtImgsArray = explode(",", $artViewImgs);
    $artImgsArray = array_merge($artImgsArray, $nArtImgsArray);
}

$artSpecsArray = json_decode($artSpecs, true);

require($_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php");
$Parsedown = new parser();

foreach ($artImgsArray as &$artImg) {
    $artImg = $ds->clearImgUrl($artImg);
}

$query = "UPDATE dsprods SET popularity = popularity + 1 WHERE id = $artId";
$conn->query($query);

//shipping availability
$countries = $ds->countries;
$clientCountry = null; $clientExplicitCountry = "";
$address = $ds->fetchAddress();
if ($address["exists"]){$clientCountry = $address["country"];$clientExplicitCountry = $countries["GLO"][$clientCountry];}

$thisItemDeliverable = [];
if ($artShipping!=null){
    $shippingDifficulty = 0;

    $chunks = array_chunk(preg_split('/(:|,)/', $artShipping), 2);
    $assocDico = array_combine(array_column($chunks, 0), array_column($chunks, 1));
    if (isset($assocDico["GLO"])){
        $shippingDifficulty = 0;
        $deliversAddress = true;
        $thisItemDeliverable =  array_keys($countries["GLO"]);
    }
    else {
        foreach ($assocDico as $deliverGroup => $uselessPrice){
            if (strlen($deliverGroup)==2){
                $thisItemDeliverable[] = $deliverGroup;
            }
            else if (strlen($deliverGroup)==3) {
                if (isset($countries[$deliverGroup])) {
                    foreach ($countries[$deliverGroup] as $countryName => $uselessPrice){
                        $thisItemDeliverable[] = $countryName;
                    }
                }
            }
        }
        $cunter = 0;
        if ($clientCountry != null AND in_array($clientCountry, $thisItemDeliverable)){
            $deliversAddress = true;
        }
        else if ($clientCountry != null) {
            $deliversAddress = false;
        }
        foreach ($countries["GLO"] as $possCountry => $uselessPrice) {
            if (in_array($possCountry, $thisItemDeliverable)){
                $cunter++;
            }
        }
        if (count($thisItemDeliverable) == 0){
            $shippingDifficulty = 3;
        }
        else {
            if ($cunter > 42) {
                $shippingDifficulty = 0;
            }
            else if ($cunter > 10) {
                $shippingDifficulty = 1;
            }
            else {
                $shippingDifficulty = 2;
            }
        }
    }
}
else {
    $shippingDifficulty = 0;
    $deliversAddress = true;
    $thisItemDeliverable = array_keys($countries["GLO"]);
}

?>


<!DOCTYPE html>
<html>
<head>
    <title><?php echo $artShortName;?> | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
        <?php
      echo $ds->giveHead();
     ?>
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
</head>
<body>
  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl = "true"></div>

    <div class="flex-container">
        <div class='left-col'>
            <a href="/ds/store"><h1 class="menutitle">Digital Store</h1></a>
            <ul class="myMenu">
                <li><a class="Bar" href="/ds/store">Browse</a></li>
            </ul>
            <?php
                echo $ds->sideBasket();
            ?>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
            </ul>
        </div>

        <div class='column'>
            <div class="crumbs"><a href="/ds/store">Store</a> - Item</div>
            <div class="flexer">
                <section class="imageShower">
                    <div class="squareCont">
                        <div class="square slideshow-container">

<?php

if (!empty($artImgsArray)){
    for ($index = 0; $index < count($artImgsArray); $index++) {
        $bigIndex = $index + 1;
        echo '
            <div class="mySlides fade">
                <div class="numbertext">'.$bigIndex.' / '.count($artImgsArray).'</div>
                <img src="'.$artImgsArray[$index].'">
            </div>
        ';
    }
    echo '
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    ';
}
else {
    echo '
        <div class="mySlides fade">
            <img src="'.$artImage.'">
        </div>
    ';
}

?>

                        </div>
                    </div>
                </section>

                <section class="rightOvertails">
                    <div class="overtail">
                        <h1><?php echo $artName; ?></h1>
                        <em><?php echo $artKind; ?></em>
                        <p>
                            By <?php echo '<a href="/ds/p/partner?id='.$artPublisherId.'" target="_blank">'.$artPublisher.'</a>'; ?><br />
                            Published <?php echo $artPubdate; ?>
                        </p>
<?php
    if (!$digital){
        if (isset($deliversAddress)) {
            if ($deliversAddress){
                if ($clientCountry != null){$message = "Ships to $clientExplicitCountry"; } else {$message = "Global shipping";}
                echo "<p class='iShipping green'><i class='fas fa-check'></i> $message <span class='fakelink' onclick=\"newpop('countryList');\">view list</span></p>";
            }
            else {
                echo "<p class='iShipping red'><i class='fas fa-times'></i> Does not ship to $clientExplicitCountry <span class='fakelink' onclick=\"newpop('countryList');\">view list</span></p>";
            }
        }
        else if ($shippingDifficulty == 0){
            echo  "<p class='iShipping green'><i class='fas fa-check'></i> Good shipping coverage <span class='fakelink' onclick=\"newpop('countryList');\">view list</span></p>";
        }
        else if ($shippingDifficulty == 1){
            echo "<p class='iShipping orange'><i class='fas fa-ban'></i> Restricted shipping <span class='fakelink' onclick=\"newpop('countryList');\">view list</span></p>";
        }
        else if ($shippingDifficulty == 2){
            echo "<p class='iShipping red'><i class='fas fa-ban'></i> Limited shipping <span class='fakelink' onclick=\"newpop('countryList');\">view list</span></p>";
        }
        else {
            echo "<p class='iShipping red'><i class='fas fa-times'></i> No shipping</p>";
        }
    }
?>

                    </div>
                    <div class="overtail iPrice">
                        $<span id="price"><?php echo $ds->makeHuman($artPrice); ?></span>
                    </div>

<?php
// all specifications
echo '<div class="overtail iOvertails">';

if ($artSpecsArray != null AND sizeof($artSpecsArray) != 0) {
    foreach ($artSpecsArray as $specArray){
        echo "<h5>".$ds->placeSpecChar($specArray['name'], 0);
        if (isset($specArray['tooltip']) AND $specArray['tooltip'] != ""){
            echo " <i class='fas fa-info-circle altStep'><span class='hoverinfo'>".$ds->placeSpecChar($specArray['tooltip'], 1)."</span></i>";
        }
        echo "</h5>";
        $selectBlock = '<select onchange="updateStuff(this);" selecter-name="'.$ds->placeSpecChar($specArray["name"], 1).'">';
        $currIndex = 0;
        foreach ($specArray["options"] as $option){
            $selectBlock .= '<option value="'.$currIndex.'" price-modifier="'.$option["price"].'">'.$ds->placeSpecChar($option["name"], 1).'</option>';
            $currIndex++;
        }
        echo $selectBlock.'</select>';

    }
}


//QUANTITY
if (isset($basketed->itemNumArray[$artId])){$basketedNum = $basketed->itemNumArray[$artId];} else {$basketedNum = 0;}
$artStock = $artStock - $basketedNum;
if ($artMaxAmount < $artStock AND $artMaxAmount != 0){$actualMaxAmount = $artMaxAmount;} else {$actualMaxAmount = $artStock;}
if ($actualMaxAmount > 99) {$actualMaxAmount = 99;}
if ($artStock == 0){$startValue = 0;}else {$startValue = 1;}
if ($artMaxAmount != 1){
    echo '
        <h5>Quantity</h5>
        <input type="number" value="'.$startValue.'" max="'.$actualMaxAmount.'" min="'.$startValue.'" onchange="checkMax(this);" id="inputQuant" />';
}
else {
    echo '<input type="number" value="1" style="display:none" id="inputQuant" />';
}
echo "</div>";

//external sources
if ($artOSources != ""){
    $chunks = array_chunk(preg_split('/(;|,)/', $artOSources), 2);
    if (count(array_column($chunks, 0)) == count(array_column($chunks, 1))) {
        $assocSources = array_combine(array_column($chunks, 0), array_column($chunks, 1));

        echo '<div class="overtail">Also Available On';
        $selectBlock = '<ul>';
        foreach ($assocSources as $key => $value) { $selectBlock = $selectBlock.'<li><a href="'.$value.'" target="_blank">'.$key.'</a></li>';}
        echo $selectBlock.'</ul></div>';
    }
}


?>
                    <div class="overtail" style="display:flex;justify-content:center;">
            <?php
if ($artStock == 0 OR $artStatus == "paused") {
                echo <<<"HEREME"
                        <div class="checkoutBox">
                            <button class="checkout" style='background-color:#a0a0a0'  onclick="createPopup('d:dsp;txt:Cannot be purchased');">
                                <i class="fas fa-shopping-basket"></i>
                                <span>Basket</span>
                            </button>
                            <button class="checkout"  style='background-color:#a0a0a0' onclick="createPopup('d:dsp;txt:Cannot be purchased');">
                                <i class="fas fa-arrow-right"></i>
                                <span>Basket</span>
                            </button>

HEREME;
            }
else {
            echo <<<"HEREME"
                        <div class="checkoutBox">
                            <button class="checkout" onclick="basket('item.php?id=$artId');">
                                <i class="fas fa-shopping-basket"></i>
                                <span>Basket</span>
                            </button>
                            <button class="checkout" onclick="basket('');">
                                <i class="fas fa-arrow-right"></i>
                                <span>Basket</span>
                            </button>

HEREME;
}

if (isset($_COOKIE["loggedIn"]) AND $_COOKIE["loggedIn"] == $artPublisherAccId ) {
    echo <<<"butonge"
                           <a href="/ds/p/item.php?id=$artId"><button class="checkout">
                                <i class="fas fa-arrow-right"></i>
                                <span>Edit</span>
                            </button></a>
butonge;
}

if ($artStatus == "paused") {echo '<p class="warning blue">this article is paused and cannot currently be purchased</p>';}
echo '<p class="warning red" id="warningNone" style="display: none">out of stock!<br>please check back later</p>';
                       echo "  </div>";

?>
                    </div>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://manyisles.ch/ds/<?php echo $artLink; ?>" target="_blank" class="fa-brands fa-facebook"></a>
                    <a href="http://www.reddit.com/submit?title=Check out <?php echo $artName; ?> on the Many Isles!&url=https://manyisles.ch/ds/<?php echo $artLink; ?>" target="_blank" class="fa-brands fa-reddit"></a>
                    <a href="https://twitter.com/intent/tweet?text=Check out the awesome <?php echo $artName; ?> on the Many Isles!%0A&url=https://manyisles.ch/ds/<?php echo $artLink; ?>&hashtags=manyisles,dnd" target="_blank" class="fa-brands fa-twitter"></a>
                    <a href="http://pinterest.com/pin/create/button/?url=https://manyisles.ch/ds/<?php echo $artLink; ?>&media=<?php echo $ds->clearImgUrl($artImage); ?>&description=Check out the awesome <?php echo $artName; ?> on the Many Isles!" target="_blank" class="fa-brands fa-pinterest"></a>
                    <a class="fa fa-link fancyjump" onclick="navigator.clipboard.writeText('https://<?php echo $_SERVER["HTTP_HOST"]."/ds/".$artLink; ?> ');createPopup('d:poet;txt:Link copied!');"></a>

                </section>
            </div>

            <section class="details">
                <div>
                    <p>
                        <?php echo $Parsedown->parse($artDescription, 1); ?>
                    </p>
<?php
if ($artDescSpecs != ""){
    echo "<h2>Specifications</h2>";
    $chunks = array_chunk(preg_split('/(:|,)/', $artDescSpecs), 2);
    $assocDescSpecs = array_combine(array_column($chunks, 0), array_column($chunks, 1));
    echo "<ul class='specs'>";
    foreach ($assocDescSpecs as $key => $value) {
        echo "<li><b>$key:</b> $value</li>";
    }
    echo "</ul>";
}
?>
                </div>
                <div>
                </div>
            </section>


        </div>
    </div>
    <div w3-include-html="/ds/g/GFooter.html" w3-create-newEl="true"></div>

    <div id="modal" class="modal" onclick="newpop('ded')">
    </div>

    <div id="countryList" class="modCol">
        <div class="modContent">
            <img src="/Imgs/PopTrade.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
            <h1>Ships To</h1>
            <p>
                This item can be shipped to:
            </p>
            <ul>
<?php
foreach ($thisItemDeliverable as $shippableCountry) {
    echo "<li>".$countries["GLO"][$shippableCountry]."</li>";
}
?>
</ul>
        </div>
    </div>

    <form id="basket" action="/ds/basket.php" method="POST" enctype="multipart/form-data" style="display:none;visibility:hidden;">
        <input style="display:none" name="basketing" id="basketing" value="<?php echo $artId; ?>" />
        <input style="display:none" name="orderDetails" id="orderDetails" value="" />
        <input style="display:none" name="goTo" id="goTo" value="nope" />
    </form>

</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>
    var quantEx = true;
    var orderDetails = {<?php
foreach ($artSpecsArray as $specArray){
echo '"'.$specArray["name"].'" : 0, ';
}
?>};
    var itemSpecs = <?php
echo json_encode($artSpecsArray);
?>;
    var itemNum = <?php
      echo json_encode($basketed->itemNumArray);
     ?>;
    var basePrice =<?php
    echo $artPrice;
?>;
    var minPrice =<?php
    echo $artMinPrice;
?>;
    var priceMods = {<?php
foreach ($artSpecsArray as $specArray){
    echo '"'.$specArray["name"].'": '.$specArray["options"][0]["price"].', ';
}
?>};

  var smartstock = false;
  for (let spec in itemSpecs) {
    if (itemSpecs[spec]["smartstock"]==1){smartstock = true;}
  }
    function makeHuman(ordiprice) {
        ordiprice = String(ordiprice);
        price = ordiprice.substr(0, ordiprice.length-2) + "." + ordiprice.substr(ordiprice.length-2, ordiprice.length);
        price = price.replace(".00", "");
        price = price.replace(" ", "");
        return price;
    }

    function updateStuff(selecter){
        let name = selecter.getAttribute("selecter-name");
        let number = selecter.options[selecter.selectedIndex].value;
        orderDetails[name] = number;
        checkMax(document.getElementById("inputQuant"));
        if (selecter.options[selecter.selectedIndex].hasAttribute("price-modifier")){
            priceModifier = parseInt(selecter.options[selecter.selectedIndex].getAttribute("price-modifier"));
            priceMods[name] = priceModifier;
            updatePrice();
        }
    }
    function updatePrice() {
        let currentPrice = basePrice;
        for (let key in priceMods){
            currentPrice += priceMods[key];
        }
        if (currentPrice < minPrice) {currentPrice = minPrice;}
        document.getElementById("price").innerHTML = makeHuman(currentPrice);
    }
    updatePrice();
    function checkMax(element) {
      if (smartstock) {
        stock = 0;
        for (let choice in orderDetails){
          let choiceName = choice;
          let choiceValue = orderDetails[choice];
          for (let spec in itemSpecs){
            if (itemSpecs[spec]["name"] == choiceName){
              if(itemSpecs[spec]["smartstock"]==1){
                stock = itemSpecs[spec]["options"][choiceValue]["stock"];
                if (itemNum["<?php echo $artId."_";?>"+choiceValue]!=undefined){
                  stock = stock - itemNum["<?php echo $artId."_";?>"+choiceValue];
                  console.log(itemNum["<?php echo $artId."_";?>"+choiceValue]);
                }
              }
            }
          }
        }
      }
      else {
        stock = <?php echo $actualMaxAmount; ?>
      }
      if (parseInt(element.value) >= stock) {
          element.value = stock;
      }
      else if (element.value <= 0) {
          element.value = 1;
      }
      if (stock == 0){
        document.getElementById("warningNone").style.display = "block";
      }
      else {
        document.getElementById("warningNone").style.display = "none";
      }
    }
    checkMax(document.getElementById("inputQuant").value);

    function basket(returnTo) {
        subArray = [];
        for (let specif in orderDetails) {
            subArray.push(specif + ":" + orderDetails[specif]);
        }
        if (quantEx) {
            subArray.push("quant:" + document.getElementById("inputQuant").value);
        }
        document.getElementById("orderDetails").value = subArray.join();
        if (returnTo != "") { document.getElementById("goTo").value = returnTo; }

        document.getElementById("basket").submit();
    }


    var slideIndex = 1;
    showSlides(slideIndex);

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        if (n > slides.length) { slideIndex = 1 }
        if (n < 1) { slideIndex = slides.length }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slides[slideIndex - 1].style.display = "block";
    }

</script>
