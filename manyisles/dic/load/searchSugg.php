<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();

if (!isset($_GET["dics"]) OR !isset($_GET["dicl"])){echo "Error: Insufficient Input"; exit;}
$search = $dic->purify($_GET["dics"]); $language = $dic->purify($_GET["dicl"], "number");

$array = array();
$query = "SELECT id, word FROM words WHERE simpleWord LIKE '%$search%'";
if ($language != 0){$query.= " AND lang = $language ";}
$query .= " LIMIT 22";
if ($result = $dic->dicconn->query($query)) {
  if (mysqli_num_rows($result) > 0) {
    while ($row = $result->fetch_assoc()) {
      $row["link"] = $dic->giveWordLink($row["id"]);
      $row["word"] = $dic->placeSpecChar($row["word"]);
      $array[] = $row;
    }
  }
}
echo json_encode($array);
?>
