<?php
$wiki = 1;
if (isset($_GET['w'])){$wiki = preg_replace("/[^0-9]/", "", $_GET['w']);}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, $wiki, false, "spells");
$gen->userMod->check(false);
$gen->spells = new spellGen($gen);
$allspells = $gen->spells->dic("AND a.parentWiki = ".$gen->parentWiki);
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->wikiName; ?> Index | Spells</title>
    <style>
      .indexSelecter {
        width: 20%;
        max-width: 200px;
        position: absolute;
        top: 5px;
        right: 5px;
      }
      .content {align-items: stretch;}
      .col-l {
      }
      .bottButtCon.two {
        margin-top: 70px;
        text-align: center;
        background-color: var(--doc-base-color);
      }
    </style>
</head>
<body>
  <?php
      echo $gen->giveTopBar();
  ?>
    <div class="content speld">


        <div class="col-r speld">
          <?php echo $gen->userMod->signPrompt("/spells/index"); ?>
          <div class="image-content">
              <img src="/Imgs/PopupSpells.png" alt="spellcaster"/>
          </div>
          <div id = "sInfo">
          </div>
          <?php
            if ($gen->canedit AND $gen->power > 3){
              echo '
              <form class="bottButtCon two" action="NewSpellIndex.php" method="GET">
                  <h3>New Global Index</h3>
                  <input type="text" name="wikiName" placeholder="Index Name" style="margin-bottom: 15px;" />
                  <input type="number" name="visibility" value ="3" style="display:none;" />
                 <button class="wikiButton "><i class="fas fa-plus"></i> Create New Index</button>
               </form>';
            }


           ?>
        </div>


        <div class="col-l speld">
            <div class="search-tab">
              <select class="indexSelecter" onchange="window.location.href='index?w='+this.value">
                <?php
                  foreach ($gen->spells->usableIndexes as $key => $usInd){
                    $inset = ""; if ($key==$gen->parentWiki){$inset = "selected";}
                    echo "<option value='".$key."' $inset> ".$usInd["wikiName"]."</option>";
                  }
                 ?>
              </select>

                <h1 class="title"><?php echo $gen->wikiName; ?> Spell Index</h1>
                <input type="text" class="spellSearch" id="spellSearch" placeholder="Search..." oninput="searchSpells()" />
            </div>
            <table id='theTable' class="theTable">
            </table>
        </div>


    </div>
</body>
</html>
<script>
  const spells = <?php echo json_encode($allspells); ?>
</script>
<?php echo $gen->giveScripts(1); ?>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "cantedit"){
    createPopup("d:poet;txt:Error. Improper credentials.");
}
</script>
