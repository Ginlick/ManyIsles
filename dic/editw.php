<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/createMarkdown.php");
$dic = new dicEngine();
$dic->checkCredentials(true);

$wordInfo = null; $writingNew = false; $wordId = 0;
if (!isset($_GET["dicw"])){
  $language = $dic->purify($_GET["lang"], "number");
  if (!isset($dic->allLangs[$language])){$dic->go("home?i=error");}
  $wordWord = "New Word";
  $dic->language = $language; $dic->curPage = $dic->allLangs[$language];
  $writingNew = true;
}
else {
  $wordInfo = $dic->wordInfo;
  $language = $wordInfo["lang"];
  $wordWord = "Edit ".$wordInfo["word"]." Word";
  $wordId = $wordInfo["id"];
}


$wordType = "";

$langDropdown = "<label for='language'>Language</label><select name='language' onchange='upInputLang(this)'>";
foreach ($dic->allLangs as $id => $lang){
  $langDropdown .= "<option value='$id'>$lang</option>";
}
$langDropdown .= "</select>";
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $wordWord; ?> | <?php echo $dic->curPage; ?> Dictionary</title>
    <?php echo $dic->giveStyles(); ?>
</head>
<style>
.wordForm {
  padding: 40px 10px;
  text-align: left;
}
.wordLabel {
  font-size: var(--all-fonts-smol);
  margin: 10px 0 5px;
}
.group {
  margin: 15px 0;
  padding: 0 0 0 17px;
  border-left: 1px solid var(--diltou-lines);
}
.addSome {
    font-size: 1.4em;
    padding: 5px;
    border-radius: 10px;
    display: inline-block;
    color: var(--diltou-lighttext);
}

    .addSome :hover {
        color: var(--diltou-theme);
    }
    .roundInfo {
      font-size: .8em;
    }
    .bottButtCon {
      text-align :center;
    }
</style>
<body>
    <?php echo $dic->giveTopnav(); ?>
    <div class="flex-container">
        <div class='left-col'>
            <?php echo $dic->giveLeftcol(); ?>
        </div>

        <div class='column'>
          <div class="columnCont">
            <?php echo $dic->giveSignPrompt(); ?>
            <h1><?php echo $wordWord; ?></h1>
            <?php if (!$writingNew){echo $dic->giveWordTab($wordInfo); echo "<h2>Set Information</h2>";} ?>
            <p>Use ctrl+shift+w to insert a word id.</p>
            <form method="POST" class="wordForm" id="formToStartAt" action="subEditw.php">
              <label for="word" class="wordLabel">Word</label>
              <input type="text" name="word" id="word" placeholder="tree"/>

              <h3>Details</h3>
              <div adder="notes" uselevel="1" id="give4notes">
                <div class="giveAddSomes"></div>
              </div>

              <h3>Specifications</h3>
              <div adder="specifications" uselevel="1" id="give4specifications">
                <div class="giveAddSomes"></div>
              </div>


              <h3>Translations</h3>
              <div adder="translations" uselevel="1" id="give4translations">
                <div class="giveAddSomes"></div>
              </div>

              <!--
              specifications groups with: wordType, conjugation, definitions (definition proper, examples (sentences, language), synonyms, antonyms)
              translation groups with: language, translations

              for words, languages: have automatic dropdowns suggesting inputs
             -->
             <input type="text" style="display:none;opacity:0;" name="actualForm" id="actualForm"/>
             <input type="text" style="display:none;opacity:0;" name="wordId" value="<?php echo $wordId;?>"/>
             <input type="text" style="display:none;opacity:0;" name="wordLang" value="<?php echo $language;?>"/>
             <div class="bottButtCon"><button onclick="submitForm();" type="button">Submit</button><a href="<?php echo "/dic/word/".$wordId; ?>"><button type="button">Cancel</button></a></div>
            </form>

            <div style="color: var(--diltou-lines);" onclick="showJson(this)">show json</div>


        </div>
      </div>
    </div>
    <?php echo $dic->giveFooter(); ?>
    <?php echo markdownTabs(); ?>
    <div id="markdown-mods-word" class="modCol">
        <div class="modContent smol">
            <h1>Insert Word Id</h1>
            <div class="nmodBody">
              <div class="findWords">
                <input placeholder="Search for a word..."  id="coolwordinput" onfocus="suggestNow(this, 2)" oninput="suggestNow(this, 2)" onfocusout="gKillSugg('suggestions')" notmarkdownable />
                <div id="suggestions" class="suggestions"></div>
              </div>
              <p><span class="typeTab tiny" onclick="newpop()">esc</span> close</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php echo $dic->giveScripts(); ?>
<?php echo markdownScript(true); ?>
<script>
const groups = {
  "translations" : '<div class="group translations"><div class="langDrpdwn" selectedLang="1"></div> <label for="words" class="wordLabel">Words</label> <input type="text" name="words" value="insert(words)" placeholder="22,35"/> </div>',
  "examples" : '<div class="group examples"><div class="langDrpdwn"></div><label for="sentence" class="wordLabel">Sentence</label><input type="text" name="sentence" value="insert(sentence)" placeholder="I saw a tree yesterday."/></div>',
  "definitions" : ' <div class="group definitions"><label for="definition" class="wordLabel">Definition <span class="roundInfo">Takes Markdown</span></label><input type="text" name="definition" value="insert(definition)" placeholder="a woody perennial plant, typically having a single stem or trunk growing to a considerable height and bearing lateral branches at some distance from the ground." markdownable/><label for="synonyms" class="wordLabel">Synonyms</label><input type="text" name="synonyms" value="insert(synonyms)" placeholder="22,35"/><label for="antonyms" class="wordLabel">Antonyms</label><input type="text" name="antonyms" value="insert(antonyms)" placeholder="567"/><h3>Examples</h3><div adder="examples" uniqueid><div class="giveAddSomes"></div></div></div></div></div>',
  "specifications" : '<div class="group specifications"><label for="wordtype" class="wordLabel">Word Type</label><input type="text" name="wordtype" value="insert(wordtype)" placeholder="noun"/><label for="conjugation" class="wordLabel">Conjugation</label><input type="text" name="conjugation" value="insert(conjugation)" placeholder="m, pl. trees"/><h3>Definitions</h3><div adder="definitions" uniqueid><div class="giveAddSomes"></div></div></div>',
  "notes": '<div class="group notes"><label for="style" class="wordLabel">Style / Language Level</label> <input type="text" name="style" value="insert(style)" placeholder="standard"/><label for="phonetic" class="wordLabel">Phonetic Pronounciation</label> <input type="text" name="phonetic" value="insert(phonetic)" placeholder="/triː/"/><label for="time" class="wordLabel">Usage Timespan</label> <input type="text" name="time" value="insert(time)" placeholder="from 1500 AD onwards"/><label for="etymology" class="wordLabel">Etymology</label> <input type="text" name="etymology" value="insert(etymology)" placeholder="Old English trēow, trēo : from a Germanic variant of an Indo-European root shared by Greek doru ‘wood, spear’, drus ‘oak’."/><label for="usage" class="wordLabel">Usage Context</label> <input type="text" name="usage" value="insert(usage)" placeholder="used commonly"/><label for="misc" class="wordLabel">Miscellaneous Information</label> <input type="text" name="misc" value="insert(misc)" placeholder="a very nice word"/>'
}
var collapsibleoptions = ["synonyms", "antonyms", "words"]; //commma-collapsible arrays
var wordJSON = <?php echo json_encode($wordInfo); ?>;
var writingNew = <?php if ($writingNew) {echo "true";}else {echo "false";}?>;

function genWordJSON() {
  var newJSON = {};
  newJSON["word"] = document.getElementById("word").value;

  var usable = document.getElementsByTagName("*");
  for (let use of usable){
    if (use.getAttribute("adder") == undefined){continue;}
    if (use.getAttribute("uselevel") !== "1"){continue;}
    genWordDeeper(newJSON, use);
  }
  return newJSON;
}
function genWordDeeper(parentObject, use) {
  var bigArray = [];
  for (let child of use.children) {//children of adder div
    let useArray = {};
    for (let smolchild of child.children) {//children of actual groups
      if (smolchild.tagName == "INPUT" || smolchild.tagName == "SELECT"){
        let key = smolchild.getAttribute("name"); let value = smolchild.value;
        if (collapsibleoptions.indexOf(key)+1) {
          value = value.split(",");
        }
        useArray[key] = value;
      }
      else if (smolchild.getAttribute("adder")!=undefined){
        genWordDeeper(useArray, smolchild);
      }
    }
    if (Object.keys(useArray).length !== 0){
      bigArray.push(useArray);
    }
  }
  adder = use.getAttribute("adder");
  if (adder=="notes"){
    bigArray = bigArray[0];
  }
  parentObject[adder] = bigArray;
}

function showJson(elmnt) {
  elmnt.innerHTML = JSON.stringify(genWordJSON());
}

function addLangDrpdwns() {
  var furtheradds = document.getElementsByClassName("langDrpdwn");
  while (furtheradds.length > 0) {
    let adda = furtheradds[0];
    var stringD = `<?php echo $langDropdown; ?>`;
    let selectedLang = adda.getAttribute("selectedLang");
    stringD = stringD.replace("value='"+selectedLang+"'", "value='"+selectedLang+"' selected");

    wordInput = adda.nextElementSibling.nextElementSibling;
    wordInput.addEventListener("focus", function() {
      language = selectedLang;
    });

    adda.outerHTML =  stringD;
  }
}
function upInputLang(select){
  newLang = select.value;
  wordInput = select.nextElementSibling.nextElementSibling;
  wordInput.addEventListener("focus", function() {
    language = newLang;
  });
}
function addAddables(looping = false) {
  var allAddables = document.getElementsByClassName("giveAddSomes");
  while (allAddables.length > 0) {
    let adda = allAddables[0];
    let parent = adda.parentElement;
    adda.outerHTML = `<div>
                        <div class="addSome" onclick="addSome(1, this);">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="addSome" onclick="addSome(0, this);">
                            <i class="fas fa-minus"></i>
                        </div>
                      </div>`;
    if (writingNew || looping){
      addSome(1, parent.firstElementChild.firstElementChild);
    }
  }
  addLangDrpdwns();
  findField();
}

function addSome(dir, adder) {
  parent = adder.parentElement.parentElement;
  if (dir == 1) {
    var node = document.createElement("div");
    parent.insertBefore(node, adder.parentElement);
    intoNode = groups[parent.getAttribute("adder")];
    intoNode = intoNode.replace(/insert\([a-z]+\)/g, "");
    node.outerHTML = intoNode;
    addAddables(true);
  }
  else {
    parent.lastElementChild.previousElementSibling.remove();
  }
}



function genFormNodes() {
  var newJSON = wordJSON;
  var funDepth = "";
  var form = document.getElementById("formToStartAt");
  document.getElementById("word").value = newJSON["word"];
  if (newJSON["notes"]!=undefined){
    funDepth = addNode("give4notes", "notes", newJSON["notes"]);
  }
  if (newJSON["specifications"]!=undefined){
    for (let group of newJSON["specifications"]){
      funDepth = addNode("give4specifications", "specifications", group);
      if (group["definitions"]!=undefined){
        for (let definitions of group["definitions"]){
          funDepth2 = addNode(funDepth, "definitions", definitions);
          if (definitions["examples"]!=undefined){
            for (let examples of definitions["examples"]){
              funDepth3 = addNode(funDepth2, "examples", examples);
            }
          }
        }
      }
    }
  }
  if (newJSON["translations"]!=undefined){
    for (let translations of newJSON["translations"]){
      funDepth = addNode("give4translations", "translations", translations);
    }
  }
}
var depth = 0;
function addNode(id, groupname, group){
  depth++; let uniqueid =  "thisnode"+depth;
  var parent = document.getElementById(id);
  //console.log(id);
  var node = groups[groupname];
  for (let key in group){
    value = group[key];
    if (key=="language"){
      node = node.replace('selectedLang="1"', 'selectedLang="'+value+'"');continue;
    }
    else if (typeof(value)=="string"){
      node = node.replace("insert("+key+")", value);
    }
    else if (collapsibleoptions.indexOf(key)+1 && typeof(value)=="object"){
      value = value.join(",");
      node = node.replace("insert("+key+")", value);
    }
  }
  parentNode = document.createElement("DIV");
  node = node.replace("uniqueid", "id='"+uniqueid+"'");
  node = node.replace(/insert\([a-z]+\)/g, "");
  parent.insertBefore(parentNode, parent.lastElementChild);
  parentNode.outerHTML = node;
  return uniqueid;
}
if (!writingNew){
  genFormNodes();
}
addAddables();

function markdownInsWord() {
  findField();
  if (myField == null){ throw "no markdownable input found";}
  newpop("markdown-mods-word");
  document.getElementById("coolwordinput").focus();
}
whenAvailable("Mousetrap", function () {
  Mousetrap.bind("ctrl+shift+w", function (e) {
    e.preventDefault();
    markdownInsWord();
    return false;
  });
});
function insertWordId(e) {
  id = e.srcElement.getAttribute("wordId");
  newpop("ded");
  insertText(myField, id);
}

function submitForm() {
  document.getElementById("actualForm").value = JSON.stringify(genWordJSON());
  document.getElementById("formToStartAt").submit();
}
</script>
