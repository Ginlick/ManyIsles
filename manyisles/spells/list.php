<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 0, false, "spells");
$gen->spells = new spellGen($gen);

 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>

    <title>Lists | Spells</title>
    <style>
    h1 {
      padding: 30px 0 25px;
    }
    .content.speld {
      display: block;
      text-align: center;
      padding-top: 20px;
    }
    .mainInput {
      padding: 25px 10px;max-width: 1100px;margin:auto;
      display: flex;
      align-items: row;
      justify-content: center;
    }
    .fielder {
      width:31%;
      border: none;
      text-align: left;
      padding: 0 25px;
    }
    .fielder input, .fielder label {width: auto;}
    .fielder input {float: right;} .fielder label {display:inline-block;width:82%;}
    .myths {
      padding: 20px 0 40px;
    }
    select, input[type=text] {
      width: 25%;margin-left:auto;margin-right:auto;margin-bottom: 15px;
    }
    </style>
</head>
<body>
  <?php
      echo $gen->giveTopBar();
  ?>
    <div class="content">
      <div class="coll">
          <a href="/home"><p class='navLink a1'><i class="fas fa-arrow-left"></i> Home</p></a>
          <p class='navLink a1' onclick='switchDis("createList");' id='sidcreateList'>Create New List</p>
          <p class='navLink a1' onclick='switchDis("saved");' id='sidsaved'>Saved Lists</p>
          <p class='navLink a1' onclick='switchDis("createIndex");' id='sidcreateIndex'>Create New Index</p>
      </div>
      <div class="colr">
        <?php echo $gen->userMod->signPrompt($gen->artLink); ?>
        <div id="createList" class="colrTab">
        <?php
        echo $gen->userMod->signPrompt("/spells/list");
          if ($gen->userMod->check(true)){
            if ($gen->domainSpecs["totalLists"]<$gen->mystData["lists"]){
              echo '<h1>Create Spell List</h1>
              <p>Spell lists are selections of spells based on indexes.</p>
              <form action="NewSpellList.php" method="POST" id="daForm" style="max-width:100%;padding:0;">
                <label for="index">Choose index:</label>
                <select name="index">';
              foreach ($gen->spells->usableIndexes as $key => $usInd){
                echo "<option value='".$key."'> ".$usInd["wikiName"]."</option>";
              }
              echo <<<MASSIVE
                  </select>
                    <div class="mainInput">
                      <fieldset id="ClassField" class="fielder">
                          <p>Select your class:</p>
                          <input type="radio" id="Archer" name="class" value="Archer">
                          <label for="Archer">Archer (Arcane)</label><br>
                          <input type="radio" id="Artificer" name="class" value="Artificer">
                          <label for="Artificer">Artificer</label><br>
                          <input type="radio" id="Bard" name="class" value="Bard">
                          <label for="Bard">Bard</label><br>
                          <input type="radio" id="Cleric" name="class" value="Cleric">
                          <label for="Cleric">Cleric</label><br>
                          <input type="radio" id="Druid" name="class" value="Druid">
                          <label for="Druid">Druid</label><br>
                          <input type="radio" id="Healer" name="class" value="Healer">
                          <label for="Healer">Healer</label><br>
                          <input type="radio" id="Maleficar" name="class" value="Maleficar">
                          <label for="Maleficar">Maleficar</label><br>
                          <input type="radio" id="Paladin" name="class" value="Paladin">
                          <label for="Paladin">Paladin</label><br>
                          <input type="radio" id="Psion" name="class" value="Psion">
                          <label for="Psion">Psion</label><br>
                          <input type="radio" id="Ranger" name="class" value="Ranger">
                          <label for="Ranger">Ranger</label><br>
                          <input type="radio" id="Sorcerer" name="class" value="Sorcerer">
                          <label for="Sorcerer">Sorcerer</label><br>
                          <input type="radio" id="Valkyrie" name="class" value="Valkyrie">
                          <label for="Valkyrie">Valkyrie</label><br>
                          <input type="radio" id="Warlock" name="class" value="Warlock">
                          <label for="Warlock">Warlock</label><br>
                          <input type="radio" id="Witch" name="class" value="Witch">
                          <label for="Witch">Witch</label><br>
                          <input type="radio" id="Wizard" name="class" value="Wizard">
                          <label for="Wizard">Wizard</label><br>
                          <input type="radio" id="Poultrymancer" name="class" value="Poultrymancer">
                          <label for="Poultrymancer">Wizard (Poultrymancer)</label><br>
                          <input type="radio" id="otherC" name="class" value="other" checked>
                          <label for="otherC">Other</label>
                      </fieldset>
                      <fieldset id="RaceField" class="fielder">
                          <p>Select your race:</p>
                          <input type="radio" id="Dwarf" name="race" value="Dwarf">
                          <label for="Dwarf">Dwarf</label><br>
                          <input type="radio" id="Drow" name="race" value="Drow">
                          <label for="Drow">Drow</label><br>
                          <input type="radio" id="MoonElf" name="race" value="Moon Elf">
                          <label for="Moon Elf">Moon Elf</label><br>
                          <input type="radio" id="PlainElf" name="race" value="Plain Elf">
                          <label for="Plain Elf">Plain Elf</label><br>
                          <input type="radio" id="SunElf" name="race" value="Sun Elf">
                          <label for="Sun Elf">Sun Elf</label><br>
                          <input type="radio" id="SandElf" name="race" value="Sand Elf">
                          <label for="Sand Elf">Sand Elf</label><br>
                          <input type="radio" id="otherR" name="race" value="other" checked>
                          <label for="otherR">Other</label><br>
                      </fieldset>
                      <fieldset id="DeityField"  class="fielder">
                          <p>Select your patron deity:</p>
                          <input type="radio" id="Auril" name="deity" value="Auril">
                          <label for="Auril">Auril</label><br>
                          <input type="radio" id="Tempus" name="deity" value="Tempus">
                          <label for="Tempus">Tempus</label><br>
                          <input type="radio" id="Tyr" name="deity" value="Tyr">
                          <label for="Tyr">Tyr</label><br>
                          <input type="radio" id="Waukeen" name="deity" value="Waukeen">
                          <label for="Waukeen">Waukeen</label><br>
                          <input type="radio" id="Zorl" name="deity" value="Zorl">
                          <label for="Zorl">Zorl</label><br>
                          <input type="radio" id="otherD" name="deity" value="other" checked>
                          <label for="otherD">Other</label><br>
                      </fieldset>
                  </div>
                  <div class="myths">
                      <input type="checkbox" name="kickMythics" />
                      <label for="kickMythics">Remove Mythical Spells</label><br />
                  </div>
                  <button class="wikiButton"><i class="fas fa-plus"></i> Create</button>
              </form>
              MASSIVE;
            }
            else {
              echo '<h1>Create Spell List</h1>
                <p>You cannot create any more lists.</p>
              ';
            }?>
          </div>
          <div id="saved" class="colrTab">
            <?php
            echo "<h1>Saved Spell Lists</h1>
            <table>
            <tbody>
            ";
            $hassaved = false;
            $query = "SELECT * FROM spelllists WHERE user = ".$gen->user;
            if ($found = $gen->dbconn->query($query)) {
              while ($row = $found->fetch_assoc()){
                $hassaved = true;
                $wikNem = ""; if (isset($gen->spells->usableIndexes[$row["wiki"]])){$wikNem = $gen->spells->usableIndexes[$row["wiki"]]["wikiName"];}
                echo "<tr><td id='".$row["code"]."name'>".txtUnparse($row["name"])."</td><td>".$wikNem."</td><td><a href='saved/".$row["code"]."'>View</a></td><td onclick=\"renameImage('".$row["code"]."')\"><i class='fas fa-pen fakelink'></i></td><td><a class='fa fa-link fancyjump' onclick='navigator.clipboard.writeText(\"https://".$_SERVER["HTTP_HOST"]."/spells/saved/".$row["code"]."\");createPopup(\"d:poet;txt:Link copied!\");'></a></td>
                <td><a href='/spells/savedDelete.php?code=".$row["code"]."'><i class='fas fa-trash'></i> Delete</a></td>
                </tr>";
              }
            }
            echo "</tbody></table>";
            if (!$hassaved){echo "<p>No spell lists created yet. <span class='fakelink' onclick='switchDis(\"createList\");'>Create one</span>";}
            ?>
          </div>
          <div id="createIndex" class="colrTab">
            <?php
            if ($gen->domainSpecs["totalIndexes"]<$gen->mystData["indexes"]){
              echo '<h1>Create Spell Index</h1>
                <p>Indexes are collections of spells.<br>Those you create are private.</p>
                <form action="NewSpellIndex.php" method="GET">
                    <input type="text" name="wikiName" placeholder="Index Name" />
                    <input type="number" name="visibility" value ="1" style="display:none;" />
                   <button class="wikiButton "><i class="fas fa-plus"></i> Create New Index</button>
                 </form>
                 <h1>Duplicate Index</h1>
                 <p>If you\'d like to add some homebrews, but not create a list from scratch.</p>
                 <form action="NewSpellIndex.php" method="GET">
                     <input type="text" name="wikiName" placeholder="Index Name" />
                     <select name="index">';
                   foreach ($gen->spells->usableIndexes as $key => $usInd){
                     echo "<option value='".$key."'> ".$usInd["wikiName"]."</option>";
                   }
                   echo '
                       </select>
                     <button class="wikiButton "><i class="fas fa-plus"></i> Duplicate Index</button>
                  </form>
              ';
            }
            else {
              echo '<h1>Create Spell Index</h1>
                <p>You cannot create any more indexes.</p>
              ';
            }

          }
          else {
            echo <<<MASSIVE
              <h1>Spell Lists</h1>
              <p>This tool allows you to create customized spell lists based on indexes.</p>
            MASSIVE;
            echo $gen->giveUnsigner();
          }
          ?>
        </div>
      </div>
    </div>
    <div id="mod1" class="modCol">
        <div class="modContent smol">
            <h1>Rename List</h1>
            <form onsubmit="return false;">
                <input type="text" id="imgSInput" placeholder="Image Name" pattern="[A-Za-z0-9. ]{2,}"></input>
                <input type="text" id="imgIdNput" style="display:none"></input>
                <div class="bottButtCon">
                    <button class="wikiButton" type="button" onclick="actRename()">Rename</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php echo $gen->giveScripts(); ?>
<script>
<?php
if ($hassaved){echo "switchDis('saved');";} ?>
if (!switched){switchDis("createList");}
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "indexCreated"){
    createPopup("d:poet;txt:New index created.");
    switchDis("createIndex");
}
else if (why == "indexFull"){
    createPopup("d:poet;txt:Error. Indexes full");
    switchDis("createIndex");
}
else if (why == "listDeleted"){
    createPopup("d:poet;txt:List deleted");
    switchDis("saved");
}

function renameImage(img) {
  document.getElementById("mod1").style.display = "block";
  document.getElementById("imgIdNput").value = img;
  document.getElementById("modal").style.display = "block";
}
function actRename() {
  name = document.getElementById("imgSInput").value;
  code = document.getElementById("imgIdNput").value;

  getFile = "/spells/savedRename.php?code=" + code + "&name=" + name;
  console.log(getFile);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
          let result = xhttp.responseText;
          if (!result.includes("Error.")){
            document.getElementById(code + "name").innerHTML = result;
          }
          else {
            console.log(result);
            createPopup("d:poet;txt:Error.");
            return false;
          }
      }
  };
  xhttp.open("GET", getFile, false);
  xhttp.send();
}

</script>
