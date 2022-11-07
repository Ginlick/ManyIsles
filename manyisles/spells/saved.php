<?php
if (isset($_GET['code'])){$code = preg_replace("/[^0-9a-zA-Z]/", "", $_GET['code']);}else {header("Location:/spells/list");exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 0, false, "spells");
$gen->userMod->check(false);
$gen->spells = new spellGen($gen);
$isowner = false;

$spellList = []; $listName = "Saved"; $index = 1; $modList = [];
$query = "SELECT * FROM spelllists WHERE code = '$code'";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $spellList = json_decode($row["list"], true);
      if ($row["user"]==$gen->user){$isowner = true;}
      $listName = $row["name"];
      $index = $row["wiki"];
      $index = $row["wiki"];
      $modList = json_decode($row["modules"], true);
    }
}
if ($modList == null){$modList = [];}

$fullList = [];
foreach ($spellList as $spell){
  $fullList[] = $gen->spells->dic("AND a.id = ".$spell)[0];
}
$indexList = [];
$query = "SELECT id, name, details FROM spells WHERE parentWiki = '$index'";
if ($firstrow = $gen->dbconn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      if (in_array($row["id"], $spellList)){continue;}
      $detailed = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row["details"]), true);
      if (isset($detailed["Source"]) AND $detailed["Source"]!="" AND !in_array($detailed["Source"], $modList)) {continue;}
      $indexList[$row["id"]]=txtUnparse($row["name"]);
    }
}

 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->wikiName; ?> List | Spells</title>
    <style>
    .addtab-cont {
      position:absolute;
      right: 0;
    }
    .addtab {
      border: 1px solid var(--doc-stext-color);
        border-radius: 50px;
        padding: 5.3px;
        font-size: 17px;
        font-family: Arial, Helvetica, sans-serif;
        display: inline-block;
        margin: 5px;
        transition: .4s ease;
    }

        .addtab:hover {
            background-color: var(--spell-accent);
        }
    </style>
</head>
<body>
  <?php
      echo $gen->giveTopBar();
  ?>
    <div class="content speld">

        <div class="col-r speld">
          <?php echo $gen->userMod->signPrompt("/spells/saved/$code"); ?>
          <div class="bottButtCon">
              <button class="wikiButton" onclick="downloadSpellList();"><i class="fas fa-arrow-down"></i> Download (Beta)</button>
          </div>
          <div id = "sInfo">
          </div>
          <?php
            if ($isowner){
              echo '
              <div class="bottButtCon">
                 <button class="wikiButton" onclick="removeSpell()"><i class="fas fa-times"></i> Remove Spell</button>
               </div>';
            }


           ?>
        </div>


        <div class="col-l speld">
            <div class="search-tab">
                <?php if ($isowner){
                  echo <<<MACCC
                  <div class="addtab-cont">
                    <div class="addtab" onclick="doPops('modContent')">Add Spell <i class="fas fa-angle-down"></i></div>
                    <div class="addtab" onclick="doPops('moduleContent')">Add Module <i class="fas fa-angle-down"></i></div>
                  </div>
                  MACCC;
                }
                ?>
                <h1 class="title" id="titleSpells"><?php echo txtUnparse($listName); ?> List</h1>
                <p ><span id="spellNumber"></span> spells available
                <?php if (count($modList)>0){echo "<br>Modules: ";
                  $has = true;
                  foreach ($modList as $module){
                    if (isset($gen->modules[$module])){if ($has){$has = false;} else {echo ", ";} echo $gen->modules[$module]["fullName"];}
                  }
                }?>
                </p>
                <input type="text" class="spellSearch" id="spellSearch" placeholder="Search..." oninput="searchSpells()" />
            </div>
            <table id='theTable' class="theTable">
            </table>
        </div>


    </div>

    <div id="modContent" class="modCol">
      <div class="modContent smol" style="padding: 20px 25px 30px">
        <h1> Add Spells </h1>
        <p >Insert the <b>exact</b> name of any additional spell you wish to add to the list.</p>
        <div class="container">
            <input type="text" placeholder="Slashing Light" id="spellToAdd" list="spellSugg" required><br>
            <datalist id="spellSugg"></datalist>
            <button class="wikiButton" onclick="addSpell()">Add</button>
        </div>
      </div>
    </div>
    <div id="moduleContent" class="modCol">
      <div class="modContent smol" style="padding: 20px 25px 30px">
        <h1> Add Module </h1>
        <p >Use codes found in Many Isles modules to add spells. <br>Note that the spells will not be added to the list, only to the available choices. Add them with "Add Spell".</p>
        <div class="container">
          <form action="/spells/savedAddModule.php" method="POST">
            <input type="number" placeholder="2244" name="spellToAdd" required><br>
            <input type="text" value="<?php echo $code; ?>" name="code" style="display:none" required><br>
            <button class="wikiButton">Add</button>
          </form>
        </div>
      </div>
    </div>
</body>
</html>
<script>
  var spells = <?php echo json_encode($fullList); ?>;
  var indexList = <?php echo json_encode($indexList); ?>;
  var list = "<?php echo $code; ?>";
</script>
<?php if ($isowner) {echo $gen->giveScripts(2);}else {echo $gen->giveScripts(1);} ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "cantedit"){
    createPopup("d:poet;txt:Error. Improper credentials.");
}
else if (why == "modAdded"){
    createPopup("d:poet;txt:Module added.");
}
else if (why == "modNotAdded"){
    createPopup("d:poet;txt:Module could not be added.");
}
function countAvailable() {
  document.getElementById("spellNumber").innerHTML = document.getElementById("theTable").rows.length;
}
countAvailable();

function downloadSpellList() {
    var doc = new jsPDF();
    doc.text(20, 15, document.getElementById("titleSpells").innerHTML);
    function giveLeftBorder(rowsIndex) {
        if (rowsIndex < 88) { return 20 } else if (rowsIndex < 176) { return 50 } else { return 80 };
    }
    function giveHeight(rowsIndex) {
        if (rowsIndex < 88) { return 20 + rowsIndex * 3 } else { return 20 + (rowsIndex - 88) * 3 }
    }
    for (let row of table.rows) {
        doc.setFontSize(5);
        doc.text(giveLeftBorder(row.rowIndex), giveHeight(row.rowIndex), row.cells[2].innerHTML + "   " + row.cells[1].innerHTML);
    }
    doc.save(document.getElementById("titleSpells").innerHTML + '.pdf');
}
</script>
