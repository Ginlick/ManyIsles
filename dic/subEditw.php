<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();
$dic->checkCredentials(true);

$wordJSON = json_decode($dic->replaceSpecChar($_POST["actualForm"], -1), true);
$wordId = $dic->purify($_POST["wordId"], "number");

$definitions = json_encode($wordJSON["specifications"], JSON_UNESCAPED_UNICODE);
$translations = json_encode($wordJSON["translations"], JSON_UNESCAPED_UNICODE);
$notes = json_encode($wordJSON["notes"], JSON_UNESCAPED_UNICODE);
$word = $dic->purify($wordJSON["word"], "dicWord");
$simpleWord = $dic->purify($word, "basic");

if ($wordId == 0){
  $language = $dic->purify($_POST["wordLang"], "number");
  if (!isset($dic->allLangs[$language])){$dic->go("/dic/word/$wordId?i=error");}

  $query = <<<HEREDOC
      INSERT INTO words (lang, word, definitions, translations, notes, simpleWord) VALUES ($language, "$word", '$definitions', '$translations', '$notes', "$simpleWord")
    HEREDOC;
  //echo $query; exit;
  if ($dic->dicconn->query($query)){
    $dic->go("word/".$dic->dicconn->insert_id."?i=changed");
  }
}

$query = <<<HEREDIC
    UPDATE words SET word = "$word", simpleWord = "$simpleWord", definitions = '$definitions', translations = '$translations', notes = '$notes' WHERE id = $wordId
  HEREDIC;

//echo $query; exit;
if ($dic->dicconn->query($query)){
  $dic->go("word/$wordId?i=changed");
}


?>
