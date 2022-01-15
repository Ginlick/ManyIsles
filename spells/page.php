<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 1, false, "spells");
$gen->spells = new spellGen($gen);
$allspells = $gen->spells->dic();
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title>Spell Index</title>
</head>
<body>
  <?php
      echo $gen->giveTopBar();
  ?>
    <div class="content">


        <div class="col-r">
          <?php echo $gen->userMod->signPrompt("/spells/index"); ?>
          <div class="image-content">
              <img src="/Imgs/PopupSpells.png" alt="spellcaster"/>
          </div>
          <div id = "sInfo">
          </div>

        </div>


        <div class="col-l">
            <div class="search-tab">
                <h1 class="title">Spell Index</h1>
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
<?php echo $gen->giveScripts(); ?>
