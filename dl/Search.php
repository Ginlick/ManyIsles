<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once("global/engine.php");

$dl = new dlengine($conn);

?>﻿
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <link rel="icon" href="/Imgs/Favicon.png">
  <title>Search | Digital Library</title>
  <?php echo $dl->styles(); ?>
</head>
<body>
<?php

$query = "";
$genre = 1;
$subgenre = "";
$gsystem = 0;
if (isset($_POST["query"])) {
  $query = preg_replace("/[^A-Za-z0-9' ]*$/", "", $_POST["query"]);
}
if (isset($_POST["genre"])) {
  if (preg_match("/^[0-9]*$/", $_POST["genre"])==1){
    $genre = $_POST["genre"];
  }
}
if (isset($_POST["subgenre"])) {
  if (preg_match("/^[a-z]*$/", $_POST["subgenre"])==1){
    $subgenre = $_POST["subgenre"];
  }
}
if (isset($_POST["gsystem"])) {
  if (preg_match("/^[0-9]*$/", $_POST["gsystem"])==1){
    $gsystem = $_POST["gsystem"];
  }
}

?>

<?php
    echo $dl->giveGlobs();
?>
    <div class="flex-container">
        <div class='left-col'>
            <ul class="myMenu">
                <li><a class="Bar" href="/dl/home"><i class="fas fa-arrow-left"></i> Library</a></li>
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

        <div id='content' class='column'>
                <?php
                    echo $dl->giveSearch("Search - Digital Library");
                    if ($query != ""){
                      echo '<h2>Results for "'.$query.'"</h2>';
                      echo "<div class='itemRow'>";
                        echo $dl->results(["query" => $query, "genre" => $genre, "subgenre" => $subgenre, "gsystem" => $gsystem]);
                      echo "</div>";
                    }
                    echo '<div class="tabs-header"><h2>Fitting Genres</h2>';
                    foreach (str_split($subgenre) as $subg){
                      if (isset($dl->subgenresArr[$genre][$subg])){
                        echo "<div class='tab'>".$dl->subgenresArr[$genre][$subg]."</div>";
                      }
                    }
                    echo "</div><div class='itemRow'>";
                      echo $dl->results(["genre" => $genre, "subgenre" => $subgenre, "gsystem" => $gsystem]);
                    echo "</div>";
                ?>




        </div>
    </div>

    <div w3-include-html="/ds/g/GFooter.html" w3-create-newEl="true"></div>

</div>
</body>
</html>
<?php
  $subgenre2 = "[";
  foreach (str_split($subgenre) as $subg){
    $subgenre2 .= "'$subg',";
  }
  $subgenre2 .= "]";
  echo $dl->baseVars($genre, $subgenre2, $gsystem);
?>
<?php echo $dl->scripts(); ?>
