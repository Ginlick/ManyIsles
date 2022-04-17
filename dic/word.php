<?php

require_once("g/dicEngine.php");
$dic = new dicEngine();
$wordInfo = $dic->wordInfo;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $wordInfo["word"]; ?> | <?php echo $dic->curPage; ?> Dictionary</title>
    <?php echo $dic->giveStyles(); ?>
</head>
<style>

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
            <?php echo $dic->giveFindWords(); ?>
            <section class="wordCont">
              <h1 class="wordTitle"><?php echo $wordInfo["word"]; ?></h1>
              <p class="headingnote"><?php echo $dic->curPage; ?> word</p>
            <?php
              if (isset($wordInfo["specifications"])){
                foreach ($wordInfo["specifications"] as $group) {
                  echo "<h3 class='wordSubTitle'>".$group["wordtype"]."</h3>";
                  if (isset($group["conjugation"])){
                    echo "<p class='headingnote'>".$group["conjugation"]."</p>";
                  }
                  if (isset($group["definitions"]) AND !$dic->isEmpty($group["definitions"])){
                    echo "<ol class='wordDefinitionUl'>";
                    foreach ($group["definitions"] as $definition) {
                      if (count($definition)==0){continue;}
                      echo "<li class='wordDefinitionBlock'>";
                      if (isset($definition["definition"])) {
                        echo "<p>".$dic->placeSpecChar($definition["definition"])."</p>";
                      }
                      if (isset($definition["examples"]) AND count($definition["examples"])>0) {
                        echo "<p class='headingnote example' >Sample Sentence</p><div class='wordExampleBlock'>";
                        foreach ($definition["examples"] as $example) {
                          echo "<p><span class='wordExampleHeader'>".$dic->allLangs[$example["language"]].":</span> ".$dic->placeSpecChar($example["sentence"])."</p>";
                        }
                        echo "</div>";
                      }
                      if (isset($definition["synonyms"]) AND count($definition["synonyms"])>0) {
                        echo "<p class='headingnote example' >Synonyms</p><div class='wordExampleBlock'>";
                        $prefix = "";
                        foreach ($definition["synonyms"] as $synonym) {
                          echo $prefix.$dic->giveWordLink($synonym);
                          $prefix = ", ";
                        }
                        echo "</p>";
                      }
                      if (isset($definition["antonyms"]) AND count($definition["antonyms"])>0) {
                        echo "<p class='headingnote example'>Antonyms</p><div class='wordExampleBlock'>";
                        $prefix = "";
                        foreach ($definition["antonyms"] as $antonym) {
                          echo $prefix.$dic->giveWordLink($antonym);
                          $prefix = ", ";
                        }
                        echo "</p>";
                      }
                      echo "</li>";
                    }
                    echo "</ol>";
                  }
                }
              }
              if (isset($wordInfo["translations"])){
                echo "<h2 class='wordSectionTitle'>Translations</h2>";
                echo "<ul>";
                foreach ($wordInfo["translations"] as $lang => $words) {
                  if ($words == "" OR !isset($words["words"]) OR count($words["words"])==0){continue;}
                  echo "<li>".$dic->allLangs[$lang].": "; $prefix = "";
                  foreach ($words["words"] as $word) {
                    //if (gettype($word)=="array") {if (count($word)==0){$word = 0;} else {$word = $word[0];}}
                    if ($word == 0) {continue;}
                    echo $prefix.$dic->giveWordLink($word); $prefix = ", ";
                  }
                  echo "</li>";
                }
                echo "</ul>";
                echo "<p><a href='/dic/translate?sl=$dic->language&s=".$wordInfo["word"]."'>View on translate</a></p>";
              }
              //print_r($wordInfo);

             ?>
           </section>
           <?php
            if ($dic->checkPower(false)) {
              echo "<div style='margin-top:50px'><a href='/dic/edit?id=".$wordInfo["id"]."'><button>Edit</button></a><a href='/dic/edit?w=$dic->language'><button>New</button></a></div>";
            }
           ?>
          </div>
        </div>
    </div>
    <?php echo $dic->giveFooter(); ?>

</div>



</body>
</html>
<?php echo $dic->giveScripts(); ?>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('w');
if (why == "notfound"){
  createPopup("d:gen;txt:Error. Page could not be found.");
}


</script>
