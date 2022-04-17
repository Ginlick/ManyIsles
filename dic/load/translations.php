<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();

if (!isset($_GET["dics"]) OR !isset($_GET["dicl"]) OR !isset($_GET["targetl"])){echo "Error: Insufficient Input"; exit;}
$search = $dic->purify($_GET["dics"], "dicWord"); $language = $dic->purify($_GET["dicl"], "number");$targetLanguage = $dic->purify($_GET["targetl"], "number");


if ($language == $targetLanguage){
  echo "<ul><li>$search</li></ul>"; exit;
}
if ($wordInfo = $dic->wordInfo($search, $language)){
  if (isset($wordInfo["translations"][$targetLanguage]["words"])){
    $translations = $wordInfo["translations"][$targetLanguage]["words"];
    if (count($translations)>0){
      $result = "<ul>";
      foreach ($translations as $translation){
        $result .= "<li>".$dic->giveWordLink($translation);
        if ($translationWordInfo = $dic->wordInfo($translation, $targetLanguage)) {
          if (isset($translationWordInfo["translations"][$language]["words"])){
            $result .= " &#183; <span class='furthers'>"; $prefix = " ";
            foreach ($translationWordInfo["translations"][$language]["words"] as $word){
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
}
echo "<ul><li>No translations found.</li></ul>";


?>
