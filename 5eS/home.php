<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", 0, 0, false, "5eS", ["notArticle"=>true]);
$conn = $gen->conn;

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="/mystral/myst.css">
    <?php echo $gen->giveFavicon(); ?>
    <title>5eS System</title>
    <style>
      .incontainer:hover {
        background-color: var(--col-lgrey);
      }
    </style>
</head>
<body>
        <?php
            echo $gen->giveTopBar();
        ?>
    <div class="content" style="display:block;text-align:center;padding-top: 70px;">

      <?php
      echo $gen->burpStencil("/5eS/1/home", "/Imgs/5eSlogo.png", "The official rules.", null, "Rulebook");
      echo $gen->burpStencil("/spells/index", "/Imgs/ManySpells.png", "An index of all spells.", null, "Spells");
       ?>

    </div>
    <div class="footer">

        <p class="centerer">            <span>Powered by </span><span class="logoWiki">Many Isles Wiki</span><br>
© Many Isles 2021</p>
    </div>
</body>
</html>
<?php
    echo $gen->giveScripts();
    echo $gen->giveDocScript();
?>
