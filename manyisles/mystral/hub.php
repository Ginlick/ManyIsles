<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 0, true, "mystral");
$conn = $gen->conn;

$barLinks = <<<BARRR
            <p class='navLink a1' onclick='switchDis("links");' id='sidlinks'>Manage Auto-Links</p>
BARRR;
$barImg = <<<BARRR
            <p class='navLink a1' onclick='switchDis("img");' id='sidimg'>Images</p>
BARRR;

$imageStencil = <<<NABSDAI
<div class="imageContainer" id="IMAGESRC2" name="IMAGENAME">
<div class="sidenav">
  <div class="shown fancyjump" style="top:10px" onclick="copyLink('IMAGESRC')"><i class="fas fa-link"></i></div>
  <div class="trans" style="top:75px" onclick="renameImage('IMAGESRC2')"><i class="fas fa-pen"></i></div>
  <div class="trans" style="top:140px" onclick="deleteImage('IMAGESRC2')"><i class="fas fa-trash"></i></div>
</div>
<div load-image="IMAGESRC"></div>
<div class="titleCont">
<h3 id="titleIMAGESRC2">IMAGENAME</h3>
</div>
</div>
NABSDAI;
$images = <<<BARRR
<div class="allImages">
ALLIMAGESHERE
</div>
BARRR;

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->domainName; ?></title>
<style>
#imgUploaderCont {
  --fpi-bg: #9ab6bf;
  --fpi-contrast: black;
  --fpi-accent: #61b3dd;
  width: 60%;
  margin: auto;
}
    .content {
        background-color: var(--doc-base-color);
    }
    .hubImg {
        width: 30%;
        max-width: 600px;
        margin: 50px 0;
    }
    .colrTab {
        display: none;
    }
.noterSelect {
  width: clamp(80px, 30%, 200px);
  margin: 14px auto;
  color: var(--doc-ahover-color);
  border-color: var(--doc-ahover-color);
}
.tierCont {
    display:flex;
    padding: calc(10px + 1vw) 0;
    max-width: 1200px;
    margin: auto;
}
.tierblock {
    width:28%;
    min-height:100%;
    border:1px solid var(--wiki-color-a);
    margin: 0 2.66%;
    box-shadow: 0 4px 22px 0 rgba(0, 0, 0, 0.2), 0 6px 22px 0 rgba(0, 0, 0, 0.19);
    border-radius:5px;
    text-align:left;
    display:inline-block;
    padding-bottom: 15px;
}

.tierblock h2 {
    text-align: center;
}
.tierblock .priceInfo {
    font-size: var(--all-fonts-base);
}
        .tierblock .img {
            width: 70%;margin:auto;
            padding: 20px 0;
        }
        .img img {
            width:100%;
        }
        .tierblock p {
            margin: 5px 0 10px;
            font-size: min(1vw, 15px);
        }
        .tierblock ul {
            padding-left:18px;
            list-style-type: disc;
        }
        .tierblock li {
            padding: 5px 0;
            font-size: min(calc(1.29vw + 2px), 18px);
            text-align: left;
        }
.homescreen {
    margin-bottom: 10px;
}
.tierblock.subscribed {
    border-color: var(--ds-gold);

}
.tierblock.subscribed .homescreen {
    background-color: var(--ds-gold) !important;
}
.homescreen.two {
    height: 90%;
    font-size: inherit;
}
.mellow {color: white;} table {text-align: left;margin-top: 80px;}
h1 {
    padding: 30px 0 10px;
}

.remove-image {
    width: 40%;
    margin: 0 auto;
    color: #fff;
    background: #cd4535;
    border: none;
    padding: 0.2vw;
    border-radius: 4px;
    border-bottom: 0.7vw solid #b02818;
    transition: all .2s ease;
    outline: none;
    text-transform: uppercase;
    font-weight: 700;
    font-size: 1.3vw;
}
    .remove-image:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }
   .remove-image:active {
        border: 0;
        transition: all .2s ease;
    }

.allImages {
    max-width: 1700px;
    margin: auto;
}
.imageContainer {
    width: 33%;
    max-width: 300px;
    display: inline-block;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    margin: 10px;
    vertical-align: top;
    min-height: 220px;
}
.imageContainer img {
    width: 100%;
    border-radius: 5px;
}
.titleCont {
    position: absolute;
    bottom: 0;
    width: 100%;
    background: rgb(0,0,0);
    background: linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%);
    word-break: keep-all;
    pointer-events: none;
}
.sidenav div {
    position: absolute; /* Position them relative to the browser window */
    transition: 0.3s; /* Add transition on hover */
    padding: 15px; /* 15px padding */
    width: 60px; /* Set a specific width */
    font-size: 20px; /* Increase font size */
    border-radius: 5px;
    background-color: var(--gen-color-mellowite);
    color: black;
}
.sidenav div:hover {
    background-color: var(--wiki-color-ahover);
}
.sidenav .trans {
  right: -100%; /* Position them outside of the screen */

}
.sidenav .shown {
    right: 10px;
}

.imageContainer:hover .sidenav .trans {
  right: 10px;
}
.filterBar {
    margin: 0 auto 30px;
    width: 50%;
}

/*anti-cache (v2.3.6)*/
.starterCont {
    padding: 22px 0;
}
.starterCont .wikiButton {
    margin-top: 40px;
    display: inline-block;
}
</style>
</head>
<body>
    <?php
        echo $gen->giveTopBar();
    ?>
    <div class="content">
        <div class="coll">
            <a href=" /home"><p class='navLink a1'><i class="fas fa-arrow-left"></i> Home</p></a>
            <p class='navLink a1' onclick='switchDis("home");' id='sidhome'>Notebooks</p>
            <p class='navLink a1' onclick='switchDis("sub");' id='sidsub'>Subscriptions</p>
            <?php
                if ($gen->signedIn){
                    echo $barImg;
                }
                if (count($gen->autoLinkArr) != 0){
                    echo $barLinks;
                }
            ?>
        </div>
        <div class="colr center">
          <?php echo $gen->userMod->signPrompt($gen->artLink); ?>
            <div id="home" class="colrTab">
            <img class="hubImg" src="<?php echo $gen->baseImage; ?>"/>
            <?php
                echo "<p>Mystral allows you to create private notebooks, <a href='/fandom/Karte-Caedras/2/home' target='_blank'>fandom</a>-like collections of notes. It's easy to use, flexible, and looks awesome.</p>";
                if ($gen->signedIn){
                    echo $gen->giveNotebooks();
                }
                else {
                  echo $gen->giveUnsigner();
                }
            ?>
            </div>
            <div id="sub" class="colrTab">
                <?php
                    echo $gen->giveSubs();
                ?>
            </div>
            <div id="links" class="colrTab" style="display: block">
                <h1>Manage Auto-Links</h1>
                <p>Auto-linking creates link markup for you when you write certain keywords. Save new keywords when creating a link.</p>

                    <?php
                        if (count($gen->autoLinkArr) != 0) {
                            echo '                <table>
                                <thead><tr><td>Keyword</td><td>URL</td><td>Wiki</td><td></td></tr></thead>
                                <tbody>';
                            foreach ($gen->autoLinkArr as $autWiki => $wikiLinkArr){
                              foreach ($wikiLinkArr as $autoLinkBlock) {
                                echo "<tr><td>".$autoLinkBlock["name"]."</td><td>".$autoLinkBlock["href"]."</td><td>".$autWiki."</td><td><a class='mellow' href='killAutKeyw.php?id=".$autoLinkBlock["name"]."'><i class='fas fa-trash'></i> Delete</a></td>";
                              }
                            }
                            echo '                    </tbody>
                            </table>';
                        }
                        else {
                            echo "<p style='margin-top:35px'>You currently don't have any saved keywords.</p>";
                        }
                    ?>
            </div>
            <div id="img" class="colrTab">
                <h1>Images</h1>
                <p>Host images and integrate them easily into your notes.</p>

            <?php

                echo '
                <p id="imagesLeftP"></p>
                <div id="imgUploaderCont"></div>
                <input type="text" class="filterBar" placeholder="Filter images..." oninput="filter(this)"></input>
                ';

                $query = "SELECT * FROM images WHERE user = $gen->user ORDER BY id DESC";
                $allimgs = "";
                if ($firstrow = $gen->dbconn->query($query)) {
                    if ($firstrow->num_rows != 0){
                        while ($row = $firstrow->fetch_assoc()) {
                            $insert = str_replace("IMAGESRC2", $row["name"], $imageStencil);
                            $insert = str_replace("IMAGESRC", $gen->files->clearmage($row["name"], "/wikimgs/myst/"), $insert);
                            $insert = str_replace("IMAGENAME", $row["title"], $insert);
                            $allimgs .= $insert;
                        }
                    }
                }
                echo str_replace("ALLIMAGESHERE", $allimgs, $images);
            ?>
            </div>
        </div>

        <div id="mod1" class="modCol">
            <div class="modContent smol">
                <h1>Rename Image</h1>
                <form onsubmit="return actRename();">
                    <input type="text" placeholder="Image Name" id="imgSInput" pattern="[A-Za-z0-9. ]{2,}"></input>
                    <input type="text" id="imgIdNput" style="display:none"></input>
                    <p><span class="typeTab tiny" onclick="removePops()">esc</span> close</p>
                    <div class="bottButtCon">
                        <button class="wikiButton">Rename</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $gen->giveFooter("doc"); ?>
    </div>
</body>
</html>
<?php
    echo $gen->giveScripts();
    echo $gen->giveDocScript();
?>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>
    var imagesLeft = <?php $left = $gen->mystData["images"] - $gen->domainSpecs["totalImages"]; echo $left; ?>;
    if (!switched){switchDis("home");}
    var urlParams = new URLSearchParams(window.location.search);
    var why = urlParams.get('i');
    if (why == "noArts"){
        createPopup("d:poet;txt:You have too many notes.");
        switchDis("sub");

    }
    else if (why == "noPage"){
        createPopup("d:poet;txt:You have too many pages. Do some filicides.");
        switchDis("sub");
    }
    else if (why == "killd"){
        createPopup("d:poet;txt:Keyword deleted");
        switchDis("links");
    }
    else if (why == "grpdeleted"){
        createPopup("d:poet;txt:Notebook deleted");
    }
    else if (why == "nexist"){
        createPopup("d:poet;txt:Page not found.");
    }

//anti-Cache (2.3.6)
var urlParams = new URLSearchParams(window.location.search);
var view = urlParams.get("view");
if (view != null){
    switchDis(view);
}
function switchDis(which) {
    for (let cont of document.getElementsByClassName("colrTab")) {
        cont.style.display = "none";
    }
    for (let cont of document.getElementsByClassName("navLink")) {
        cont.classList.remove("selected");
    }
    var tab = document.getElementById(which);
    var naver = document.getElementById("sid" + which);
    if (tab != null){tab.style.display = "block";}
    if (naver != null){naver.classList.add("selected");}
}

//fpi
addCss("/Server-Side/src/fileportal/fpi-builder.js", "js");

returnF = function (r){
  for (let file of r["files"]) {
    $('.allImages').prepend(file["galleryHTML"]); //somehow convert imgURL to the gallery image thing
  }
  imageCount(-r["files"].length);
  getIndexImgs();
}
function fpi_launcher() {
  fpiBuilder = new fpi_builder(221, returnF);
  fpiBuilder.createPortal(document.getElementById("imgUploaderCont"), "broad");
  imageCount(0);
};


//images
var namee;
function deleteImage(name) {
  let postDel = function (){
    document.getElementById(namee).remove();
    imageCount(1);
  }
  fpiBuilder.deleteImage(name, postDel);
  namee = name;
}

function imageCount(n) {
    imagesLeft += n;
    $("#imagesLeftP").hide();
    if (5 > imagesLeft && imagesLeft > 0){$("#imagesLeftP").show(); $("#imagesLeftP").html("You can upload another " + imagesLeft + " images.");$("#imagesLeftP").css("color", "");}
    else if (imagesLeft < 0){$("#imagesLeftP").show();$("#imagesLeftP").html("You have "+ Math.abs(imagesLeft) +" too many images. Either delete some, or buy a better subscription.<br>If you fail to do so, we may arbitrarily delete some of your images.");$("#imagesLeftP").css("color", "var(--col-red)");}

    if (imagesLeft < 1){
      $('.image-upload-wrap').hide();
      $('.file-upload-content').show();
      $('.image-title').html("Your image count is full. Buy a bigger subscription.");
    }
    else if (imagesLeft + n > 0){
      fpiBuilder.createPortal(document.getElementById("imgUploaderCont"), "broad");
    }
    //else {fpiBuilder.removeUpload($("#imgUploaderCont"));}

    if (document.getElementsByClassName("imageContainer").length > 1){$('.filterBar').show();}
    else {$('.filterBar').hide();}
}

function copyLink(text) {
    navigator.clipboard.writeText(text);
    createPopup('d:poet;txt:Link copied!');
}

function renameImage(img) {
    document.getElementById("imgSInput").value = document.getElementById("title"+img).innerHTML;
    setTimeout(function() { $('#imgSInput').focus(); $('#imgSInput').select(); }, 50);
    $("#imgIdNput").val(img);
    $(".modal").show();
    $("#mod1").show();
}

function actRename() {
    id = $("#imgIdNput").val();
    value = $("#imgSInput").val();

    getFile = encodeURI("renameImage.php");
    var xhttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("name", id);
    formData.append("value", value);
    xhttp.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("title"+id).innerHTML = value;
            document.getElementById(id).setAttribute("name", value);
        }
        else if (this.readyState == 4) {
            createPopup("d:poet;txt:Error. Image could not be renamed.");
        }
    };
    xhttp.open("POST", getFile, true);
    xhttp.send(formData);

    newpop();
    return false;
}
function filter(input) {
    value = input.value;
    for (let block of document.getElementsByClassName("imageContainer")) {
        if (block.getAttribute("name").toLowerCase().includes(value.toLowerCase())){
            block.style.display = "inline-block";
        }
        else {
            block.style.display = "none";
        }
    }
}
</script>
