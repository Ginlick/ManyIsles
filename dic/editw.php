<?php

require_once("g/dicEngine.php");
$dic = new dicEngine();
$wordInfo = $dic->wordInfo;
$dic->checkCredentials(true);

$wordWord = $wordInfo["word"];
$wordType = "";

$langDropdown = "<label for='language'>Language</label><select name='language'>";
foreach ($dic->allLangs as $id => $lang){
  $langDropdown .= "<option value='$id'>$lang</option>";
}
$langDropdown .= "</select>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit <?php echo $wordInfo["word"]; ?> | <?php echo $dic->curPage; ?> Dictionary</title>
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
            <h1>Edit Word</h1>
            <?php echo $dic->giveWordTab($wordInfo); ?>
            <h2>Set Information</h2>
            <form method="POST" class="wordForm" id="formToStartAt">
              <label for="word" class="wordLabel">Word</label>
              <input type="text" name="word" id="word" placeholder="tree"/>

              <h3>Specifications</h3>
              <div adder="specifications" uselevel="1">
                <div class="giveAddSomes"></div>
              </div>


              <h3>Translations</h3>
              <div adder="translations" uselevel="1">
                <div class="giveAddSomes"></div>
              </div>

              <!--
              specifications groups with: wordType, conjugation, definitions (definition proper, examples (sentences, language), synonyms, antonyms)
              translation groups with: language, translations

              for words, languages: have automatic dropdowns suggesting inputs
             -->
            </form>

            <div onclick="showJson(this)">show json</div>


        </div>
      </div>
    </div>
    <?php echo $dic->giveFooter(); ?>


</body>
</html>
<?php echo $dic->giveScripts(); ?>
<script>
const groups = {
  "translations" : '<div class="group translations"><div class="langDrpdwn"></div> <label for="words" class="wordLabel">Words</label> <input type="text" name="words" value="" placeholder="arbre, arbuste"/> </div>',
  "examples" : '<div class="group examples"><div class="langDrpdwn"></div><label for="sentence" class="wordLabel">Sentence</label><input type="text" name="sentence" value="" placeholder="I saw a tree yesterday."/></div>',
  "definitions" : ' <div class="group definitions"><label for="definition" class="wordLabel">Definition</label><input type="text" name="definition" value="" placeholder="a woody perennial plant, typically having a single stem or trunk growing to a considerable height and bearing lateral branches at some distance from the ground."/><h3>Examples</h3><div adder="examples"><div class="giveAddSomes"></div></div><label for="synonyms" class="wordLabel">Synonyms</label><input type="text" name="synonyms" value="" placeholder="shrub, bush"/><label for="antonyms" class="wordLabel">Antonyms</label><input type="text" name="antonyms" value="" placeholder="flames"/></div></div></div>',
  "specifications" : '<div class="group specifications"><label for="wordtype" class="wordLabel">Word Type</label><input type="text" name="wordtype" value="" placeholder="noun"/><label for="conjugation" class="wordLabel">Conjugation</label><input type="text" name="conjugation" value="" placeholder="m, pl. trees"/><h3>Definitions</h3><div adder="definitions"><div class="giveAddSomes"></div></div></div>',
}
var wordJSON = <?php echo json_encode($wordInfo); ?>;

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
        useArray[smolchild.getAttribute("name")] = smolchild.value;
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
  parentObject[adder] = bigArray;
}
//processing: parse synonyms, antonyms, translation words

function showJson(elmnt) {
  elmnt.innerHTML = JSON.stringify(genWordJSON());
}

function addLangDrpdwns() {
  var furtheradds = document.getElementsByClassName("langDrpdwn");
  while (furtheradds.length > 0) {
    let adda = furtheradds[0];
    adda.outerHTML = `<?php echo $langDropdown; ?>`;
  }
}
function addAddables() {
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
      addSome(1, parent.firstElementChild.firstElementChild);
  }
  addLangDrpdwns();
}
addAddables();

function addSome(dir, adder) {
  parent = adder.parentElement.parentElement;
  if (dir == 1) {
    var node = document.createElement("div");
    parent.insertBefore(node, adder.parentElement);
    node.outerHTML = groups[parent.getAttribute("adder")];

    addAddables();
  }
  else {
    parent.lastElementChild.previousElementSibling.remove();
  }
}



function genFormNodes() {
  var newJSON = wordJSON;
  var form = document.getElementById("formToStartAt");
  document.getElementById("word").value = newJSON["word"];
  for (let biggroup in newJSON){
    if (typeof(newJSON[biggroup])!="object"){continue;}
    for (let child of form.children){
      if (child.getAttribute("adder")==biggroup) {
        genFormDeeper(newJSON[biggroup], child);
      }
    }
  }
}
function genFormDeeper(biggroup, block) {
  for (let child of block.children) {//cycling through groups
    for (let smolchild of child.children){
      adder = smolchild.getAttribute("adder");
      if (smolchild.tagName == "INPUT"){
        console.log(smolchild.name + JSON.stringify(biggroup));

        smolchild.value = biggroup[smolchild.name];
      }
      //selects
      else if (adder !=undefined && biggroup[adder]!=undefined){
        genFormDeeper(biggroup[adder], smolchild);
      }
    }
  }
}
genFormNodes();



</script>
