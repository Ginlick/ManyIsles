<?php

require_once("global/engine.php");
$dl = new dlengine();

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title>Digital Library</title>
    <?php echo $dl->styles(); ?>
</head>
<style>


</style>
<body>
  <?php
      echo $dl->giveGlobs();
  ?>
    <div class="flex-container">
        <div class='left-col'>
            <ul class="myMenu">
                <li><a class="Bar" href="/home"><i class="fas fa-arrow-left"></i> Home</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>

            <?php
                echo $dl->giveMenu();
            ?>

            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/docs/11/Digital_Library" target="_blank">Digital Library FAQ</a></li>
            </ul>
        </div>

        <div class='column'>
                <?php
                    echo $dl->giveSearch();
                ?>

                <h2>Discover</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodRow("random", 8);
                    ?>
                </div>
                <h2>New</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodRow("new", 8);
                    ?>
                </div>
                <h2>Popular</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodRow("popular", 8);
                    ?>
                </div>
                <h2>Recommended</h2>
                <div class="itemRow single">
                    <?php
                        echo $dl->prodItem(26);
                        echo $dl->prodItem(8);
                        echo $dl->prodItem(3);
                        echo $dl->prodItem(33);
                        echo $dl->prodItem(41);
                        echo $dl->prodItem(40);
                        echo $dl->prodItem(27);
                        echo $dl->prodItem(36);
                    ?>
                </div>


        </div>
    </div>

    <div w3-include-html="/ds/g/GFooter.html" w3-create-newEl="true"></div>

    <div class="bottomad-container">
        <div class="bottomad">
            <img src="global/plus.png" alt="hi" />
            <a href="/account/BePartner.php">
                Publish your own!
            </a>
        </div>
    </div>
</div>



</body>
</html>
<?php echo $dl->baseVars(); ?>
<?php echo $dl->scripts(); ?>
<script>
var why = urlParams.get('i');
if (why == "badpartner"){
    createPopup("d:pub;txt:This product's partnership is not active.");
}
else if (why == "baditem"){
    createPopup("d:pub;txt:This product couldn't be accessed.");
}
</script>
