﻿<?php
//header("Content-Type:text/example");

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php");
$base = new useBase();
$conn = $base->addConn("accounts");

$query = "SELECT ud, body, sidetabTitle, sidetabImg, sidetabText FROM pages WHERE ud = 415";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()){
  $newArr = []; $parser = new parser();
  echo $parser->parse($row["body"], 1);
  $newArr["text"][0]["body"] = preg_replace("!\r?\n!", '\\n', $base->replaceSpecChar($row["body"]));
  $newArr["text"][0]["sidetab"]["title"] = preg_replace("!\r?\n!", '\\n', $base->replaceSpecChar($row["sidetabTitle"]));
  $newArr["text"][0]["sidetab"]["image"] =preg_replace("!\r?\n!", '\\n', $base->replaceSpecChar($row["sidetabImg"]));
  $newArr["text"][0]["sidetab"]["text"] = preg_replace("!\r?\n!", '\\n', $base->replaceSpecChar($row["sidetabText"]));
  $newArr = json_encode($newArr, JSON_HEX_APOS|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  $query = "UPDATE pages SET body = '$newArr' WHERE ud = ".$row["ud"];
  // echo $row["body"]."<br>".$query."<br>";
  // print_r($newArr = json_decode($newArr, true));
  //
  // echo $parser->parse($newArr["text"][0]["body"], 1);

  $conn->query($query);
  //echo $newArr["text"][0]["body"];
}
// $conn->query("ALTER TABLE pages DROP COLUMN sidetabTitle, sidetabText");
// echo "done";
//
// $query = "SELECT * FROM pages WHERE id = 6 AND v = 58";
// $result = $conn->query($query);
// while ($row = $result->fetch_assoc()){
//   echo $row["body"];
//   print_r(json_decode($row["body"], true));
// }

?>