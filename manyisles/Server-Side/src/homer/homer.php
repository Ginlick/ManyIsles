<?php
require $_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php";

class homer {

  public $sloganArr = ["A world of creation."];
  function __construct() {
    $json = file_get_contents($_SERVER['DOCUMENT_ROOT']."/Server-Side/src/homer/slogans.json");
    $newSlogs = json_decode($json, true);
    if ($newSlogs != null){
      $this->sloganArr = $newSlogs;
    }
    $this->parser = new parser();
  }
  function giveSlogan($arr = null) {
    if ($arr === null){
      $arr = $this->sloganArr;
    }
    $cap = count($arr) - 1;
    $index = $this->curvedRand($cap);

    if (gettype($arr[$index]) != "array"){
      $text = $this->parser->parse($arr[$index]);
      return $text;
    }
    return $this->giveSlogan($arr[$index]);
  }
  function curvedRand($cap) {
    $test = rand(0, $cap) + rand(0, $cap) - $cap;
    if ($test < 0){
      return $this->curvedRand($cap);
    }
    return $test;
  }
}

// $homer = new homer;
// $occurrences = [];
// for ($i = 0; $i < 600; $i++){
//   $res = $homer->giveSlogan();
//   if (!isset($occurrences[$res])){$occurrences[$res] = 0;}
//   $occurrences[$res]++;
// }
// arsort($occurrences);
// print_r($occurrences);
// echo $homer->giveSlogan();
?>
