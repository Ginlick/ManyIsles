<?php
//$itemNumArray, $conn; js funcs: showView, pruchase; basket Form


$itemStencil = <<<NABSDAI

        <div class="artContainer">
        <div class="incontainer">
        <a href="MEGALINK">
            <div class="imagCont">
                <div class="artSquare" onmouseover="showView(this.children[1], 'MEGAVIEWIMG');" onmouseout="showView(this.children[1], 'MEGATHUMBNAIL');">
                    <div class="hoverDiv"><span>View</span></div>
                    <img src="MEGATHUMBNAIL" alt="Thumbnail" class="linkim">
                </div>
            </div>
            <div class='titling'>MEGANAME <hr class="solid"> <span class="price">MEGAPRICE</span><br></div>
        </a>
            <button class="checkout homescreen" onclick="purchase(ITEMID)"><i class="fas fa-shopping-basket"></i><span> Basket</span></button>
        </div>
        </div>

NABSDAI;

require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/makeHuman.php");
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/alertStock.php");
function makeArtTab($row, $itemNumArray = [], $showNoStock = false){
    global $itemStencil, $conn;
      if ($row["status"] != "deleted"){
          $query = "SELECT status FROM partners WHERE id = ".$row["sellerId"];
          $clearPartner = true;
          if ($result = $conn->query($query)){
              if (mysqli_num_rows($result) == 0) { $clearPartner = false; }
              while ($nRow = $result->fetch_assoc()) {
                  if ($nRow["status"]!="active"){$clearPartner = false; }
              }
          }
          if ($clearPartner) {
              $articleId = $row["id"];
              $canBuy = true;
              $hasStock = false;

              if ($totalStock = hasAnyStock($row["specifications"], $row["stock"])){
                if (isset($itemNumArray[$articleId])){
                  if ($totalStock - $itemNumArray[$articleId] > 0) {
                    $hasStock = true;
                  }
                }
                else {
                  if ($totalStock != 0){$hasStock = true; }
                }
              }

              if ($hasStock or $showNoStock){
                if ($row["status"]=="paused" ) {$canBuy = false;}
                $titling = $row["name"];
                $thumbnail = clearImgUrl($row["image"]);
                $link = linki($row["id"], $row["link"], $row["shortname"]);
                $price = $row["price"];
                if (stripos($price, ",")){
                    $price = substr($price, 0, stripos($price, ","));
                }
                if ($price == 0) {$humPrice = "Special Price";}
                else {
                    if ($row["specifications"] == ""){if ($row["minPrice"] > $price) { $price = $row["minPrice"];}$humPrice = makeHuman($price);}
                    else {
                        $specsArray = json_decode($row["specifications"], true);
                        foreach ($specsArray as $specArray){
                            $price += intval($specArray["options"][0]["price"]);
                        }
                        if ($row["minPrice"] > $price) { $price = $row["minPrice"];}
                        $humPrice = makeHuman($price)." +";
                    }
                }

                if ($row["viewImgs"] != NULL){
                    $viewImgArray = explode (",", $row["viewImgs"]);
                    $altImg = clearImgUrl($viewImgArray[0]);
                } else {$altImg = $thumbnail;}

                $itemTab = $itemStencil;
                if ($hasStock == false){$canBuy = false;}
                if (!$canBuy){
                    $itemTab = str_replace('onclick="purchase(ITEMID)', 'onclick="createPopup(\'d:dsp;txt:Item cannot be purchased\');', $itemTab);
                    $itemTab = str_replace('checkout homescreen', "checkout homescreen grey", $itemTab);
                }
                $itemTab = str_replace("MEGALINK", $link, $itemTab);
                $itemTab = str_replace("MEGATHUMBNAIL", $thumbnail, $itemTab);
                $itemTab = str_replace("MEGAVIEWIMG", $altImg, $itemTab);
                $itemTab = str_replace("MEGANAME", $titling, $itemTab);
                $itemTab = str_replace("MEGAPRICE", $humPrice, $itemTab);
                $itemTab = str_replace("ITEMID", $articleId, $itemTab);
                if ($canBuy OR $showNoStock) {
                    echo $itemTab;
                }
              }
          }
    }
}
?>
