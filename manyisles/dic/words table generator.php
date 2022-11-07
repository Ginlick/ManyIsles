<?php
require_once("g/dicEngine.php");
$dic = new dicEngine();

$langs = [
  //"standard common" => 1,
  "English" => 2,
  "French" => 3,
  "Denise" => 4,
  "Eastern" => 5,
  "Archean (commoner's)" => 6,
  "Surin" => 7,
  "Skijspik" => 8
];
$alllangs = [
  "standard common" => 1,
  "English" => 2,
  "French" => 3,
  "Denise" => 4,
  "Eastern" => 5,
  "Archean (commoner's)" => 6,
  "Surin" => 7,
  "Skijspik" => 8
];


$strJsonFileContents = file_get_contents("csvjson.json");
$json = json_decode(preg_replace('/[\x00-\x1F]/', '', $strJsonFileContents), true, 512, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
foreach ($json as $word) {
  //do standard first, then add the others quickly when done
  $aWord = $word["standard common"];
  if ($aWord != ""){
    $query = "INSERT INTO words (lang, word) VALUES (1, '$aWord')";
    $dic->dicconn->query($query);
    $permWordId = $dic->dicconn->insert_id;
  }

  //notes
  $notes = ["misc"=>$word["background notes"]];
  //significances
  $significances = [];
  $oldjson = str_replace("\\", "", $word["oldjson"]);
  $newjson = json_decode($oldjson, true);
  if ($newjson != null) {
    foreach ($newjson as $key => $group) {
      $definitions = [];
      foreach ($group["s"] as $definition) {
        $def = $dic->replaceSpecChar($definition["def"]);
        $exampleSentences = [];
        if (isset($definition["standard common"])){
          $exampleSentences[] = ["language"=>1, "sentence"=>$dic->replaceSpecChar($definition["standard common"])];
        }
        if (isset($definition["English"])){
          $exampleSentences[] = ["language"=>2, "sentence"=>$dic->replaceSpecChar($definition["English"])];
        }
        if (isset($definition["Eastern"])){
          $exampleSentences[] = ["language"=>5, "sentence"=>$dic->replaceSpecChar($definition["Eastern"])];
        }
        array_push($definitions, ["definition"=>$def, "examples" => $exampleSentences]);
      }
      if (isset($group["v"])){$conjugation = $group["v"];}
      else {$conjugation = $word["grammatical notes"];}
      //no synonyms or antonyms cuz standard
      $significances[] = ["wordtype"=>$key,"definitions"=>$definitions, "conjugation"=>$conjugation];
    }
  }
  else {
    $significances[] = ["wordtype"=>$word["word form"], "conjugation" => $word["grammatical notes"]];
  }
  //translations
  $translations = [];
  foreach ($word as $lang=>$trans){
    if (!isset($langs[$lang])){continue;}
    $words = $dic->explodeA($trans);
    $wordList = [];
    foreach ($words as $wordLiteral) {
      $wordId =  $dic->wordId($wordLiteral, $langs[$lang]);
      $wordList[] = $wordId;
      epauletteWord($wordId, $word, $aWord);
    }
    $translations[] = ["language"=>$langs[$lang], "words"=>$wordList];
  }
  if ($aWord == ""){continue;}
  $notes = json_encode($notes); $significances = json_encode($significances); $translations = json_encode($translations);
  $query = "UPDATE words SET notes = '$notes', definitions = '$significances', translations = '$translations' WHERE id = $permWordId";
  $dic->dicconn->query($query);
  //echo $query;
}

function epauletteWord($wordId, $wordJSON, $aWord) {
  global $alllangs, $dic;
  $wordInfo = $dic->wordInfo($wordId);
  if ($wordInfo["translations"] != null){return;}

  $translations = []; $significances = [];
  foreach ($alllangs as $lang => $langId) {
    $wordList = [];
    foreach ($dic->explodeA($wordJSON[$lang]) as $wordLiteral) {
      $wordList[] =  $dic->wordId($wordLiteral, $alllangs[$lang]);
    }
    if ($alllangs[$lang] == $wordInfo["lang"]) {
      if (($key = array_search($wordId, $wordList)) !== false) {
        unset($wordList[$key]);
      }
      sort($wordList);
      array_push($significances, ["wordtype" => $wordJSON["word form"], "definitions"=>[["synonyms"=>$wordList]]]);
    }
    else {
      if ($wordList != null AND count($wordList)>0){
        $translations[] = ["language"=>$langId, "words"=>$wordList];
      }
    }
  }
  $significances = json_encode($significances); $translations = json_encode($translations);
  $query = "UPDATE words SET definitions = '$significances', translations = '$translations' WHERE id = $wordId";
  $dic->dicconn->query($query);
}
echo "success";

?>
