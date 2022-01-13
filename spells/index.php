<?php
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 1, false, "spells");

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
          <div class="image-content">
              <img src="/Imgs/PopupSpells.png" alt="spellcaster"/>
          </div>

            <div class="sInfoBlock">
                <h3 id="sName">Slashing Light</h3>
                <div id="sLevel" class="sText">Level 0 spell</div>
                <div id="sSchool" class="sText">Evocation</div>
                <div id="sElement" class="sText">High Magic</div>
            </div>
            <div class="sInfoBlock">
                <div id="sCastingTime" class="sText">Casting Time: 1 action</div>
                <div id="sRange" class="sText">Range: 120 feet</div>
                <div id="sComponents" class="sText">Components: V, S</div>
                <div id="sDuration" class="sText">Duration: Instantaneous</div>
            </div>
            <div class="sInfoBlock">
                <div id="sClass" class="sText"> Bard, Cleric, Psion, Valkyrie, Wizard</div>
            </div>
            <div class="sInfoBlock">
                <div id="sFullDesc" class="sText">
                    You hurl a ray of light at a creature within range. Make a ranged attack roll. On a hit, the creature takes 1d10 radiant damage.<br />
                    <br />
                    This spell’s damage increases: 5th-2d10, 11th-3d10, 17th-4d10.
                </div>
            </div>

            <div id="exclusiveNote" style="        width: 90%;
        margin: auto;
        display: none;
        text-align: center;
        color: red;
        padding-top: 10px;
        font-family: 'Arial'">Please note this is an exclusive spell; it will not automatically be added to your spell list when you <a href="SpellCreate.html" style="color:red">create</a> it.</div>

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
<script src='Spells.js'></script>
<script src="theTablebuilder.js">
 </script>
