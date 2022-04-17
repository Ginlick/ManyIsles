<?php

require_once("g/dicEngine.php");
$dic = new dicEngine("Translate");
$sl = 1; $tl = 2; $s = "";
if (isset($_GET["sl"])) {$sl = $dic->purify($_GET["sl"], "number");}
if (isset($_GET["tl"])) {$tl = $dic->purify($_GET["tl"], "number");}
if (isset($_GET["s"])) {$s = $dic->purify($_GET["s"], "dicWord");}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Translate | Dictionary</title>
    <?php echo $dic->giveStyles(); ?>
</head>
<style>
  .translateCont {
    border: none;
    background-color: inherit;
  }
  .translateBlock {
    background-color: var(--diltou-basecd);
    border-radius: 22px;
    box-shadow: var(--diltou-lines) 0px 1px 2px 2px;
  }
  .wTopBar {
    border-bottom: 1px solid var(--diltou-lines);
    width: 100%;
    min-height: 20px;
  }
  .wInputContsCont {
    display: flex;
  }
  .wInputContsCont.tall {
    min-height: 120px;
  }
  .wInputCont {
    width: 50%;
    padding: 15px;
  }
  .wInputCont p {
    padding: 0; margin: 0;
  }
  .wInputCont p:hover {
    color: var(--diltou-theme);
  }
  .wInputCont.left {
    border-right: 1px solid var(--diltou-lines);
  }
  .wInput {
    background-color: inherit;
    border: none;
    outline: none;
    padding: 0; margin: 0;
  }
  .wResultCont ul {
    list-style-type: none;
    margin: 0; padding: 0;
  }
  .furthers {
    color: var(--diltou-lines);
  }

  .langCont {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    padding: 10px 0;
    display: none;
  }
  .langBlock {
    min-width: 190px;
    padding: 8px 11px;
  }
  .langBlock:hover {
    background-color: var(--col-lightwite);
  }
</style>
<body>
    <?php echo $dic->giveTopnav(); ?>
    <div class="flex-container">
        <div class='left-col'>
            <?php echo $dic->giveLeftcol(); ?>
        </div>

        <div class='column'>
          <div class="columnCont ">
            <?php echo $dic->giveSignPrompt(); ?>
            <section class="translateCont wordCont">
              <div class="translateBlock">
                <div class="wTopBar">
                  <div class="wInputContsCont">
                    <div class="wInputCont">
                      <p id="sl" onclick="toggleLangBar('sl')"><?php echo $dic->allLangs[$sl]; ?></p>
                    </div>
                    <div class="wInputCont">
                      <p id="tl" onclick="toggleLangBar('tl')"><?php echo $dic->allLangs[$tl]; ?></p>
                    </div>
                  </div>
                </div>
                <div class="wTopBar langCont" id ="langCont">
                  <?php
                    foreach ($dic->allLangs as $id => $langname){
                      echo "<div class='langBlock' langNum='".$id."' onclick=switchLang(this)>".$langname."</div>";
                    }
                  ?>
                </div>
                <div class="wInputContsCont tall">
                  <div class="wInputCont left">
                    <div class="findWords">
                      <input type="text" id="wInput" class="wInput" onfocus="suggestNow(this, 1)" oninput="suggestNow(this, 1);translates();" onfocusout="gKillSugg('suggestions')" value="<?php echo $s; ?>" />
                      <div id="suggestions" class="suggestions"></div>
                    </div>
                  </div>
                  <div class="wInputCont wResultCont" id="wResult">
                  </div>
                </div>

              </div>
           </section>
          </div>
        </div>
    </div>
    <?php echo $dic->giveFooter(); ?>

</div>



</body>
</html>
<?php echo $dic->giveScripts(); ?>
<script>
language = <?php echo $sl; ?>;
targetLanguage = <?php echo $tl; ?>;
var languageMode = 'sl';
var wInput = document.getElementById("wInput");
var wResult = document.getElementById("wResult");
var langCont = document.getElementById("langCont");

function toggleLangBar(mode) {
  languageMode = mode;
  if (langCont.style.display == "flex") {
    langCont.style.display = "none";
  }
  else {
    langCont.style.display = "flex";
  }
}
function switchLang(e) {
  newLang = e.getAttribute("langNum");
  if (languageMode == "sl"){
    language = newLang;
  }
  else {
    targetLanguage = newLang;
  }
  document.getElementById(languageMode).innerHTML = e.innerHTML;
  toggleLangBar(languageMode);
  translates();
}




function newWord(e) {
  word = e.srcElement.innerHTML;
  wInput.value=word;
  translates();
}
function translates() {
  toTrans = wInput.value;
  if (toTrans != "") {
    wResult.innerHTML = "";
    window.setTimeout(function () {
      if (wResult.innerHTML == ""){
        wResult.innerHTML = '<ul><li><i class="fas fa-spinner fa-spin"></i> Translating</i></li></ul>';
      }
    }, 222);
    var newRelativePathQuery = window.location.pathname + '?' + "sl="+language+"&tl="+targetLanguage+"&s="+toTrans;
    history.pushState(null, '', newRelativePathQuery);
    query = "/dic/load/translations?dics="+toTrans+"&dicl="+language+"&targetl="+targetLanguage;

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        wResult.innerHTML = xhttp.responseText;
      }
    };
    xhttp.open("GET", query);
    xhttp.send();
  }
}

translates();
</script>
