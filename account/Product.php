<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();
$dl->partner();
$filing = new smolengine();

$prodId = 0;
$writingNew = true;
if (isset($_GET['id']) AND $_GET['id'] != 0){
  $prodId = substr(preg_replace("/[^0-9]/", "", $_GET['id']), 0, 20);
  if ($dl->checkOwner($prodId, $dl->partId)) {
    $writingNew = false;
  }
  else {
    $prodId = 0;
  }
}

$proName = "";
$proSName = "";
$proDesc = "";
$proGenre = 1;
$proSubgenre = "";
$proKeywords = "";
$proSupport = 1;
$proFormat = "";
$proLink = "";
$proTier = 0;
$proExternal = 0;
$proGsystem = 0;
$prodPop = 0; $prodDl = 0;
$proStatus = "active";
$proImg = "/IndexImgs/GMTips.png";

if (!$writingNew){
  $query = "SELECT * FROM products WHERE id = $prodId";
  if ($toprow = $dl->dlconn->query($query)) {
    while ($row = $toprow->fetch_assoc()) {
      $proName = $row["name"];
      $proSName = $row["shortName"];
      $proImg = $dl->clearmage($row["image"]);
      $proDesc = $row["description"];
      $proGenre = $row["genre"];
      $proSubgenre = $row["subgenre"];
      $proKeywords = $row["categories"];
      $proSupport = $row["support"];
      $proLink = $dl->fileclear($row["link"], $proGenre, true);
      $proTier = $row["tier"];
      $proStatus = $row["status"];
      $prodPop = $row["popularity"];
      $prodDl = $row["downloads"];
      $more = json_decode($row["more"], true);
      if(isset($more["indirect"]) AND $more["indirect"] == 1){$proExternal = 1;}
      if(isset($more["gsystem"])){$proGsystem = $more["gsystem"];}
      if(isset($more["format"])){$proFormat = $more["format"];}
    }
  }
}
if ($proStatus=="deleted"){$dl->go("Publish", "p");}


?>
<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
    <?php echo $dl->styles("p"); ?>
    <title>Product Publishing | Partnership</title>
<style>
.fieldCont {
  width:60%;
  display:inline-block;
  text-align:left;
  padding:9px;
  height: 250px;
}
.field input, input[type=checkbox] {
  width: auto;
  margin: 10px 20px;
  display:
}
.checker{
  text-align: center;
  padding: 10px 0;
}
.checker label {
  width: auto;
}
.field .input {
  width: 100%;
  display: block;
}
</style>
</head>
<body>
  <?php
      echo $dl->giveGlobs();
  ?>

  <div class="flex-container">
      <div class='left-col'>
          <h1 class="menutitle">Partnership</h1>
          <ul class="myMenu">
              <li><a class="Bar" href="Publish"><i class="fas fa-arrow-left"></i> Main Page</a></li>
          </ul>
          <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
          <ul class="myMenu bottomFAQ">
            <li><a class="Bar" href="/docs/24/Markdown" target="_blank">Many Isles Markdown</a></li>
            <li><a class="Bar" href="/docs/62/Support_Payments" target="_blank">Support Payments</a></li>
            <li><a class="Bar" href="/docs/14/Publishing_Guide" target="_blank">Publishing Guidelines</a></li>
            <li><a class="Bar" href="/docs/60/Publishing_Terms" target="_blank">Publishing Terms</a></li>
            <li><a class="Bar" href="/docs/4/Partnerships" target="_blank">Partnership Program</a></li>
          </ul>
      </div>

      <div class='column'>
        <?php
        echo $dl->giveAccTab();
         ?>

      <?php
        if (!$writingNew) {
          echo " <h1>Edit Product</h1>
          <p>Your awesome product. <a href='".$dl->url($prodId, $proSName)."' target='_blank'>View in library</a><br>
          Total Views: $prodPop<br>
          Total Downloads: $prodDl
          </p>";
        }
        else {
          echo "<h1>Publish a Product</h1>";
        }

       ?>

      <form onsubmit="sendForm(this);return false;"  action = "SubProd.php" method="POST" enctype="multipart/form-data" class="stanForm">
        <div class="contentBlock">
          <h3>Main Info</h3>
          <section class="duel">
            <section class="imageShower">
              <div class="squareCont">
                  <div class="square">
                    <div class="mySlides fade">
                        <img src="<?php echo $proImg; ?>" class="file-upload-image">
                    </div>
                  </div>
                  <div class="overlay content">
                      <span class="viewOverlay"><i class="fas fa-arrow-up"></i></span>
                      .png or .jpg<br>max 250kb
                      <input type="file" onchange="readURL2(this);" id="image" value="null" name = "image" accept=".png, .jpg"/>
                  </div>
              </div>
            </section>
            <div class="inputCont sideText">
              <div class="inputCont">
                  <label for="pname">Title <span>*</span> </label>
                  <input type ="text" name="pname"  placeholder="A Great Creation" value="<?php echo $proName; ?>" required />
                  <p class="inputErr info" default="The name of your product."></p>
              </div>
              <div class="inputCont">
                  <label for="pname">Short Title <span>*</span> </label>
                  <input type ="text" name="spname"  placeholder="A Creation" value="<?php echo $proSName; ?>" required />
                  <p class="inputErr info" default="A shorter title, used on thumbnails and suchlike."></p>
              </div>
            </div>
          </section>
          <div class="inputCont">
              <label for="pname">Description <span>*</span> <a href="/wiki/h/fandom/markdown.html" target="_blank"><span class="roundInfo">Takes Markdown</span></a></label>
              <textarea rows="6" name="description"  placeholder="Use this great creation to create new [monsters](https://monsters.com)." required><?php echo $proDesc; ?></textarea>
              <p class="inputErr info" default="A nice, vivid description of your product. Max 220 words."></p>
          </div>
        </div>
        <div class="contentBlock">
          <h3>Meta</h3>
          <div style="width:40%;float:left; text-align: left;"
              <label for="genre">Choose a type:</label>
              <select id="genre" name="genre" onchange="typValue(this.value)">
                <?php
                  $text =  '
                  <option value="1">Module</option>
                  <option value="2">Tool</option>
                  <option value="3">Art</option>
                  <option value="4">Audio</option>
                          ';
                    $text = str_replace('value="'.$proGenre.'"', 'value="'.$proGenre.'" selected', $text);
                    echo $text;
                 ?>
              </select>
          </div>
          <div class="fieldCont" style="">
              <div id="field1" class="field">
                <span class="input"><input type="checkbox" onclick = "catValue('c');" subg="c">classes</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('r');" subg="r">races</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('u');" subg="u">rules</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('a');" subg="a">adventures</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('l');" subg="l">lore</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('d');" subg="d">DM stuff</input></span>
              </div>
              <div id="field2" style="display:none;" class="field">
                <span class="input"><input type="checkbox" onclick = "catValue('h');" subg="h">homebrewing</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('r');" subg="r">generator</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('i');" subg="i">index/list</input></span>
              </div>
              <div id="field3" style="display:none;" class="field">
                <span class="input"><input type="checkbox" onclick = "catValue('v');" subg="v">visual</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('m');" subg="m">cartography</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('n');" subg="n">dungeons</input></span>
              </div>
              <div id="field4" style="display:none;" class="field">
                <span class="input"><input type="checkbox" onclick = "catValue('a');" subg="v">ambient music</input></span>
                <span class="input"><input type="checkbox" onclick = "catValue('p');" subg="m">active music</input></span>
              </div>
          </div>
          <input type="text" style="display:none" id="subgenre" name="subgenre" value="<?php echo $proSubgenre; ?>"/>

          <div class="inputCont" id="mSpecificMeta">
              <label for="pname">Game System</label>
              <select id="gamesys" name="gamesys">
                <option value="0">Any / Other</option>
                <option value="2">5e</option>
                <option value="1">5eS</option>
              </select>
          </div>
        </div>
        <div class="contentBlock">
          <h3>Further Specifications</h3>
          <div class="inputCont">
              <label for="keywords">Search Keywords</label>
              <input type ="text" name="keywords"  placeholder="monsters,magic,forest" value="<?php echo $proKeywords; ?>" />
              <p class="inputErr info" default="Additional keywords (besides the title) for searches."></p>
          </div>
          <div class="inputCont">
              <label for="supportProd">Suggest and Receive Support Payments. <a href='/docs/62/Support_Payments' target='_blank'>More info</a></label>
              <select id="supportProd" name="supportProd">
                <?php
                  $text =  '
                  <option value="1">On</option>
                  <option value="0">Off</option>
                          ';
                    $text = str_replace('value="'.$proSupport.'"', 'value="'.$proSupport.'" selected', $text);
                    echo $text;
                 ?>
              </select>
              <p class="inputErr info" default="Turn off for lower-effort products."></p>
          </div>
          <div class="inputCont">
              <label for="format">Product Format</label>
              <input type ="text" name="format"  placeholder="PDF" value="<?php echo $proFormat; ?>" />
              <p class="inputErr info" default="The product's format."></p>
          </div>

          <div class="inputCont" <?php if ($dl->ppower < 1) {echo "style='display:none'";} ?>>
              <label for="tier">Tier <span class="roundInfo gold">Premium Extension</span></label>
              <select name="tier" name="tier">
                <?php
                  $text =  '
                    <option value="0">Free</option>
                    <option value="1">Tier 1 (Imperial Soldier)</option>
                    <option value="2">Tier 2 (Grand Wizard)</option>
                    <option value="3">Tier 3 (Legendar)</option>
                          ';
                    $text = str_replace('value="'.$proTier.'"', 'value="'.$proTier.'" selected', $text);
                    echo $text;
                 ?>
              </select>
          </div>
        </div>

          <div class="contentBlock" style="min-height: 200px">
          <h3>File</h3>
          <div class="inputCont checker" id="externalizer">
              <input type="checkbox"  name="external" id="externalized" onchange="updateFiler()" <?php if ($proExternal == 1){echo "checked";} ?> />
              <label for="external">External File</label>
          </div>

          <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
          <div class="file-upload">
            <div class="image-upload-wrap">
              <input type="hidden" name="MAX_FILE_SIZE" value="35000000" />
              <input class="file-upload-input" type='file' onchange="readURL(this);" name="file" id="file-upload-input" accept="application/pdf" />
              <div class="drag-text">
                <p id="drag-textr">Drag and drop a file, or click to upload (max 30 MB)</p>
              </div>
            </div>
            <div class="file-upload-content">
              <p class="image-title">Uploaded File</p>
              <div class="image-title-wrap">
                <button type="button" onclick="removeUpload()" class="remove-image">Remove </button>
              </div>
            </div>
          </div>
          <div class="inputCont" id="linkInput" style="display:none">
              <label for="link">Product Link <span>*</span> </label>
              <input type ="text" name="link"  placeholder="https://mysite/files/product.pdf" value="<?php echo $proLink; ?>" id="proLink" />
              <p class="inputErr info" default="A direct url to the product. <a href='/docs/28/Hosting%20Images%20Online' target='_blank'>More info</a>"></p>
          </div>
          <?php
            if ($writingNew){ $proLink = "Not yet uploaded"; }
              echo "<p  id='thisfile'>Current File: <a href='$proLink' target='_link'>$proLink</a></p>";
           ?>
        </div>

        <input type="text" name="prodId" style="display: none" value="<?php echo $prodId; ?>" />
        <div class="file-uploading-content" style="display:none;">
          <p><i class="fas fa-spinner fa-spin"></i> Uploading</p>
        </div>
        <button class="nowSubmitButton"><i class="fas fa-arrow-right"></i> Submit</button>
      </form>


          <div class="contentBlock"<?php if ($writingNew){ echo "style='display:none'"; } ?> >
            <h1>Manage Product Status</h1>
            <p><b>Active</b> products are normally visible in the digital library, <b>paused</b> ones aren't.
              <br>Current status: <span class="statusinfo"><?php echo $proStatus; ?></span>
            </p>
            <button onclick="toggleStatus()"><i class="fas fa-arrow-right"></i> Toggle Status</button>
            <h3>Delete</h3>
            <p>Alternatively, you can permanently delete this product.<br><b>This is a single-click action.</b></p>
            <a href="DelProd?id=<?php echo $prodId; ?>"><button><i class="fas fa-trash"></i> Delete</button></a>
          </div>

  </div>
</div>
<?php
echo $dl->giveFooter();
?>
</body>
</html>
<?php
  echo $dl->scripts("p");
 ?>
<script>
var genre = 1;
var subgenre = "";
var external =  false;
var requArray = <?php echo json_encode($filing->fileRequs); ?>;

function readURL2(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {

      $('.file-upload-image').attr('src', e.target.result);

    };

    reader.readAsDataURL(input.files[0]);

  }
}
function readURL(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {
      $('.image-upload-wrap').hide();

      $('.file-upload-content').show();

      $('.image-title').html(input.files[0].name);
    };

    reader.readAsDataURL(input.files[0]);

  } else {
    removeUpload();
  }
}

function removeUpload() {
  $('.file-upload-input').replaceWith($('.file-upload-input').clone());
  $('.file-upload-content').hide();
  $('.image-upload-wrap').show();
}
$('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
});
function imagePrompt() {
    document.getElementById("fInfo").style.display = "block";
    document.getElementById("fInfo").style.color = "green";
    document.getElementById("fInfo").innerHTML = "Uploading... <br> do not close this tab!";
}
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "delfail"){
  createPopup("d:pub;txt:Error deleting your product.");
}

function giveAccept(weirds) {
  var retursn = "";
  for (let wird of weirds){
    retursn += "."+wird+", ";
  }
  return retursn;
}

function updateFiler() {
  external = document.getElementById("externalized").checked;
  genre= document.getElementById("genre").value;
  subgenre= document.getElementById("subgenre").value;

  if (!external && (genre != 2)) {
    $('.file-upload').show();
    if (genre == 4) {
      document.getElementById("file-upload-input").setAttribute("accept", giveAccept(requArray["bigAudio"]["types"]));
      $("#drag-textr").html("Drag and drop a file. Max " + formatBytes(requArray["bigAudio"]["size"], 0));
    }
    else if (genre == 3) {
      document.getElementById("file-upload-input").setAttribute("accept", giveAccept(requArray["dlArt"]["types"]));
      $("#drag-textr").html("Drag and drop a file. Max " + formatBytes(requArray["dlArt"]["size"], 0));
    }
    else {
      document.getElementById("file-upload-input").setAttribute("accept", giveAccept(requArray["dlPdf"]["types"]));
      $("#drag-textr").html("Drag and drop a file. Max " + formatBytes(requArray["dlPdf"]["size"], 0));
    }
    document.getElementById("linkInput").style.display="none";
    removeUpload();
  }
  else {
    $('.file-upload').hide();
    document.getElementById("linkInput").style.display="inline-block";
    removeUpload();
  }
  if (genre == 0){
      document.getElementById("mSpecificMeta").style.display="block";
  }
  else {
      document.getElementById("mSpecificMeta").style.display="none";
  }
  if (genre != 2) {
    $("#externalizer").show();
  }
  else {
    $("#externalizer").hide();
  }

  $(".field").hide();
  $("#field"+genre).show();
  $("#field"+genre+">span>input").prop("checked", false);

  for (let option of document.getElementById("field"+genre).children){
    if (subgenre.includes(option.firstElementChild.getAttribute("subg"))){
      option.firstElementChild.checked = true;
    }
    else {
      option.checked = false;
    }
  }
}
updateFiler();

function typValue(value) {
  document.getElementById('subgenre').value="";
  subgenre = "";
  updateFiler();
}

function catValue(clicked) {
    if (subgenre.includes(clicked)){
        subgenre = subgenre.replace(clicked, "");
        document.getElementById('subgenre').value = subgenre;
    }
    else {
        subgenre = subgenre.concat(clicked);
        document.getElementById('subgenre').value = subgenre;
    }
}

function sendForm(form) {
    var fileName = "unknown";
    if (document.getElementById("file-upload-input").files.length != 0){
      fileName = document.getElementById("file-upload-input").files[0].name;
    }
    else {
      fileName = document.getElementById("proLink").value;
    }

    $('.nowSubmitButton').hide();
    $('.file-uploading-content').show();
    $('#uploaded-image-title').html("Uploading files");

    getFile = encodeURI("SubProd.php");
    var xhttp = new XMLHttpRequest();
    var formData = new FormData(form);
    xhttp.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {
          console.log(this.responseText);

          if (this.responseText.includes("fileFail")) {
            createPopup("d:pub;txt:Error uploading file.");
          }
          else if (this.responseText.includes("too large")) {
            createPopup("d:pub;txt:Error. Image too large.");
          }
          else if (this.responseText.includes("imgFail")) {
            createPopup("d:pub;txt:Error uploading image.");
          }
          else if (this.responseText.includes("success")){
            <?php if ($writingNew) {echo "window.location.href = 'Publish?i=pcreated';"; } ?>
            createPopup("d:pub;txt:Product updated.");
            document.getElementById("thisfile").innerHTML = "Current File: " + fileName;
          }
          else {
            createPopup("d:poet;txt:Error. Could not submit data.");
          }

          if (this.responseText.includes("external")){
            document.getElementById("externalized").checked = true;
            updateFiler();
          }
          else {
            document.getElementById("externalized").checked = false;
            updateFiler();
          }

          $('.file-uploading-content').hide();
          $('.nowSubmitButton').show();
        }
        else if (this.readyState == 4) {
          $('.file-uploading-content').hide();
          $('.nowSubmitButton').show();
            createPopup("d:poet;txt:Error. Could not submit data.");
        }
    };
    xhttp.open("POST", getFile, true);
    xhttp.send(formData);

  return false;
}
function toggleStatus() {
    getFile = encodeURI("TogProd.php?id=<?php echo $prodId; ?>");
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {
          console.log(this.responseText);

          if (this.responseText.includes("active") || this.responseText.includes("paused")) {
            createPopup("d:pub;txt:Status toggled.");
          }
          else {
              createPopup("d:poet;txt:Error. Could not toggle status.");
          }

          if (this.responseText.includes("paused")) {$('.statusinfo').html("paused");}
          else if  (this.responseText.includes("active")) {$('.statusinfo').html("active");}
        }
        else if (this.readyState == 4) {
            createPopup("d:poet;txt:Error. Could not toggle status.");
        }
    };
    xhttp.open("POST", getFile, true);
    xhttp.send();
  }
</script>
