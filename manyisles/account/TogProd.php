<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();
$dl->partner();

$id = 0;
$id = substr(preg_replace("/[^0-9]/", "", $_GET['id']), 0, 55);

$query = "SELECT status, partner FROM products WHERE id = $id";
if ($firstrow = $dl->dlconn->query($query)) {
  while ($row = $firstrow->fetch_assoc()){
    if ($row["partner"] == $dl->partId) {

      $status = $row["status"];
      if ($status == "active"){
          if ($dl->dlconn->query("UPDATE products SET status = 'paused' WHERE id = $id")) {echo "paused";}
      }
      else if ($status == "paused"){
        if ($dl->dlconn->query("UPDATE products SET status = 'active' WHERE id = $id")) {echo "active";}
      }
    }
    else {echo "gay";}
  }
}

?>
