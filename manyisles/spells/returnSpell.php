<?php
$spellId = 1;
if (isset($_GET["id"])){$spellId = preg_replace("[^0-9]", "", $_GET["id"]);}
$wikId = 1;
if (isset($_GET["wiki"])){$wikId = preg_replace("[^0-9]", "", $_GET["wiki"]);}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", $spellId, $wikId, false, "spells");
$gen->spells = new spellGen($gen);
$spellBlock = $gen->spells->spellBlock($spellId, $wikId);


echo $spellBlock;

 ?>
