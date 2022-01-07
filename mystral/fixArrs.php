<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_notes.php");
$dbconn = giveNonn();
$query ="SELECT * FROM auto_links";
if ($result = $dbconn->query($query)){
    while ($row = $result->fetch_assoc()){
        $oldArr = json_decode($row["data"], true);
        $newArr = [];
        foreach ($oldArr as $key => $details) {
          $wiki = 0;
          if (isset($details["wiki"])){$wiki = $details["wiki"];}
          $insertArr = ["name"=> $key, "href"=>$details["href"]];
          $newArr[strval($wiki)][] = $insertArr;
        }
        $newArr = json_encode($newArr);
        $query = "UPDATE auto_links SET data = '$newArr' WHERE id = ".$row["id"];
        $dbconn->query($query);
    }
}


?>
