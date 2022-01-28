<?php
$parentWiki = 1;
$spellId = substr(preg_replace("/[^0-9]/", "", $_GET['id']), 0, 70);


require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", $spellId, 0, false, "spells");
$gen->spells = new spellGen($gen);
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->article->name; ?>  | Spells</title>
    <style>
      .colr {
        text-align: center;
      }
      .col-r {
        margin: 10px auto;
        text-align: left;
      }
    </style>
</head>
<body>
  <?php
      echo $gen->giveTopBar();
  ?>
    <div class="content speld normal">
      <div class="colr" >
        <div class="col-r speld">
          <?php echo $gen->userMod->signPrompt("/spells/index"); ?>
          <div class="image-content">
              <img src="/Imgs/PopupSpells.png" alt="spellcaster"/>
          </div>
          <div id = "sInfo">
            <?php echo $gen->spells->spellBlock(); ?>
          </div>

        </div>
      </div>
      <div class="coll">
          <a href="/home"><p class='navLink a1'><i class="fas fa-arrow-left"></i> Home</p></a>
          <a href="/spells/index?w=<?php echo $gen->parentWiki; ?>"><p class='navLink a1'><i class="fas fa-arrow-left"></i> <?php echo $gen->wikiName." ".ucwords($gen->groupName); ?></p></a>
      </div>
    </div>
</body>
</html>

<?php echo $gen->giveScripts(); ?>
