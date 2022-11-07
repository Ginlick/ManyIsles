<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();

$wordJSON = json_decode($dic->replaceSpecChar($_POST["actualForm"], -1), true);
if (!isset($wordJSON["notes"])){$wordJSON["notes"] = null;}
if (!isset($wordJSON["specifications"])){$wordJSON["specifications"] = null;}
if (!isset($wordJSON["translations"])){$wordJSON["translations"] = null;}

$alllangs = $dic->allLangs;
$wdefinitions = $wordJSON["specifications"];
$wtranslations = $wordJSON["translations"];
$wwordId = $dic->purify($_POST["wordId"], "number");
if ($wwordId == 0){
  $wlanguage = $dic->purify($_POST["wordLang"], "number");
  if (!isset($dic->allLangs[$wlanguage])){$dic->go("home?i=error");}
  $wwordId = $dic->wordId($wordJSON["word"], $wlanguage);
}
else {
  if (!($wwordInfo = $dic->wordInfo($wwordId))){
    $dic->go("word/$wordId?i=error");
  }
  $wlanguage = $wwordInfo["lang"];
}
$dic->language = $wlanguage;
$dic->checkCredentials(true);

function epauletteWord($wordId, $wdefinitions, $wtranslations, $antonymed) { //$antonymed: whether it's an antonym rather than a synonym/translation
  //this function constructs information for newly created words based on their relationship to $wordJSON
  global $alllangs, $dic, $wlanguage, $wwordId;
  $wordInfo = $dic->wordInfo($wordId);
  if ($wordInfo["translations"] != null OR $wordInfo["specifications"] != null){return;}

  $significances = [["wordtype" => $wdefinitions[0]["wordtype"], "definitions"=>[["synonyms"=>[], "antonyms"=>[]]]]];
  $translations = [];

  if (!$antonymed) {
    //fetching information based on translations
    if ($wlanguage != $wordInfo["lang"]){
      $translations = [["language"=>$wlanguage, "words"=>[$wwordId]]];
    }
    foreach ($alllangs as $langId => $language) {
      $wordList = [];
      if ($targetLTrans = giveTransByLang($wtranslations, $langId)){
        $wordList = $targetLTrans["words"];
      }
      if ($langId == $wordInfo["lang"]) { //synonyms
          if (($key = array_search($wordId, $wordList)) !== false) {
            unset($wordList[$key]);
          }
          if (count($wordList)>0 AND $wordList[0] != ""){
            sort($wordList);
            array_push($significances[0]["definitions"][0]["synonyms"], $wordList);
          }
        }
      else { //translations
        if (count($wordList)>0){
          $translations[] = ["language"=>$langId, "words"=>$wordList];
        }
      }
    }
  }

  //fetching information based on synonyms/antonyms
  $synonyms = [];$antonyms = [];
  foreach ($wdefinitions as &$spec){
    if ($dic->isValidArr($spec, "definitions")){
      foreach ($spec["definitions"] as &$definition){
        if ($dic->isValidArr($definition, "synonyms")){
          if (in_array($wordId, $definition["synonyms"])){
            if ($antonymed){
              array_push($antonyms, $wwordId);
            }
            else {
              array_push($synonyms, $wwordId);
            }
            foreach ($definition["synonyms"] as $synonym){
              if ($synonym != $wordId){
                array_push($synonyms, $synonym);
              }
            }
          }
        }
        if ($dic->isValidArr($definition, "antonyms")){
          if (in_array($wordId, $definition["antonyms"])){
            if ($antonymed){
              array_push($synonyms, $wwordId);
            }
            else {
              array_push($antonyms, $wwordId);
            }
            foreach ($definition["antonyms"] as $antonym){
              if ($antonym != $wordId){
                array_push($antonyms, $antonym);
              }
            }
          }
        }
      }
    }
  }
  if ($antonymed){ [$antonyms, $synonyms] = [$synonyms, $antonyms];}
  $significances[0]["definitions"][0]["synonyms"] = array_merge($significances[0]["definitions"][0]["synonyms"], $synonyms);
  $significances[0]["definitions"][0]["antonyms"] = array_merge($significances[0]["definitions"][0]["antonyms"], $antonyms);

  $simpleWord = $dic->purify($dic->placeSpecChar($wordInfo["word"]), "basic");
  $significances = json_encode($significances, JSON_UNESCAPED_UNICODE); $translations = json_encode($translations, JSON_UNESCAPED_UNICODE);
  $query = "UPDATE words SET definitions = '$significances', translations = '$translations', simpleWord = '$simpleWord' WHERE id = $wordId";
  $dic->dicconn->query($query);
}
function giveTransByLang($translations, $lang){
  foreach ($translations as $language) {
    if ($language["language"]==$lang){
      return $language;
    }
  }
  return false;
}
function giveIdedWord($word, $langId, &$toEpaulette, $antonym = false){//parse a word (literal or id)
  global $dic;
  if (preg_match("/^[0-9]+$/", $word)===1){
    if ($dic->wordExists($word, $langId)){
      return $word;
    }
  }
  else {
    $word = $dic->purify($word, "dicWord");
    $wordId =  $dic->wordId($word, $langId);
    $toEpaulette[$wordId] = $antonym;
    return $wordId;
  }
}

//properly parse wword specifications (convert literal words to numbers, update information in mentioned words (i.e. translations are also added in target word.))
$toEpaulette = []; $seenLangs = [$wlanguage]; $allTrans = [];
//wtranslations
foreach ($wtranslations as &$translation) {
  if (!isset($alllangs[$translation["language"]])){unset($translation);continue;}
  if (!$dic->isValidArr($translation, "words")){unset($translation);continue;}
  if (in_array($translation["language"], $seenLangs)){unset($translation);continue;}
  $seenLangs[] = $translation["language"];
  $words = $translation["words"];
  $wordList = [];
  foreach ($words as $word) {
    if ($worde = giveIdedWord($word, $translation["language"], $toEpaulette)){
      array_push($wordList, $worde);
    }
  }
  $allTrans[] = ["language" => $translation["language"], "words" => $wordList];
}
$wtranslations = $allTrans;
//wsynonyms/antonyms
foreach ($wdefinitions as &$spec){
  if ($dic->isValidArr($spec, "definitions")){
    foreach ($spec["definitions"] as &$definition){
      if ($dic->isValidArr($definition, "synonyms")){
        $synonyms = [];
        foreach ($definition["synonyms"] as $synonym){
          if ($worde = giveIdedWord($synonym, $wlanguage, $toEpaulette)){
            array_push($synonyms, $worde);
          }
        }
        $definition["synonyms"]=$synonyms;
      }
      if ($dic->isValidArr($definition, "antonyms")){
        $antonyms = [];
        foreach ($definition["antonyms"] as $antonym){
          if ($worde = giveIdedWord($antonym, $wlanguage, $toEpaulette, true)){
            array_push($antonyms, $worde);
          }
        }
        $definition["antonyms"]=$antonyms;
      }
    }
  }
}
//do epaulette (information around new words)
foreach ($toEpaulette as $wordId => $antonymed) {
  epauletteWord($wordId, $wdefinitions, $wtranslations, $antonymed);
}


$definitions = json_encode($wdefinitions, JSON_UNESCAPED_UNICODE);
$translations = json_encode($wtranslations, JSON_UNESCAPED_UNICODE);
$notes = json_encode($wordJSON["notes"], JSON_UNESCAPED_UNICODE);
$word = $dic->purify($wordJSON["word"], "dicWord");
$simpleWord = $dic->purify($dic->placeSpecChar($word), "basic");


$query = <<<HEREDIC
    UPDATE words SET word = "$word", simpleWord = "$simpleWord", definitions = '$definitions', translations = '$translations', notes = '$notes' WHERE id = $wwordId
  HEREDIC;

//echo "<br><br>".$query; exit;
if ($dic->dicconn->query($query)){
  $dic->go("word/$wwordId?i=changed");
}

?>
