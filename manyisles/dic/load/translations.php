<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();

if (!isset($_GET["dics"]) OR !isset($_GET["dicl"]) OR !isset($_GET["targetl"])){echo "Error: Insufficient Input"; exit;}
$search = $dic->purify($_GET["dics"], "dicWord"); $language = $dic->purify($_GET["dicl"], "number");$targetLanguage = $dic->purify($_GET["targetl"], "number");

if ($language == $targetLanguage){
  echo "<ul><li>$search</li></ul>"; exit;
}

function giveLangArr($langArr, $language) {
  foreach ($langArr as $lang) {
    if ($lang["language"]==$language){
      return $lang;
    }
  }
  return false;
}
if ($wordInfo = $dic->wordInfo($search, $language)){
  if ($translations = giveLangArr($wordInfo["translations"], $targetLanguage) AND $dic->isValidArr($translations, "words")){
    $result = "<ul>";
    foreach ($translations["words"] as $translation){
      $result .= "<li>".$dic->giveWordLink($translation);
      if ($translationWordInfo = $dic->wordInfo($translation, $targetLanguage)) {
        $transWordWords = giveLangArr($translationWordInfo["translations"], $language);
        if (isset($transWordWords["words"])){
          $result .= " &#183; <span class='furthers'>"; $prefix = " ";
          foreach ($transWordWords["words"] as $word){
            $result .= $prefix.$dic->quickWord($word); $prefix = ", ";
          }
          $result .= "</span>";
        }
      }
      $result .= "</li>";
    }
    $result .= "</ul>";
    echo $result; exit;
  }
}

echo "<ul><li>No translations found.</li></ul>";


?>
