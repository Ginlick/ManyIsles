
<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();

$q = substr(preg_replace("/[^0-9]/", "", urldecode($_GET["q"])), 0, 50);

$partner = 0; $image = ""; $partName = ""; $prodName = "";
$searchstring = 'SELECT * FROM products WHERE id = '.$q;
if ($max = $dl->dlconn->query($searchstring)) {
    while ($gay = $max->fetch_assoc()){
        $partner = $gay["partner"];
        $prodName = $gay["name"];
        $image = $dl->clearmage($gay["image"]);
    }
}
$resultArray = array("name" => "", "partner"=>"", "image"=>"");
if ($partner == 0){
  $searchstring = 'SELECT * FROM products ORDER BY popularity DESC LIMIT 1';
  if ($max = $dl->dlconn->query($searchstring)) {
      while ($gay = $max->fetch_assoc()){
        $partner = $gay["partner"];
        $prodName = $gay["name"];
        $image = $dl->clearmage($gay["image"]);
      }
  }
}

if (isset($dl->partners) AND $dl->partners[$partner]=="active"){
  $query = "SELECT name FROM partners WHERE id = $partner";
  if ($max = $dl->conn->query($query)) {
      while ($gay = $max->fetch_assoc()){
          $partName = $gay["name"];
      }
  }
  $resultArray = array("partner"=>$partName, "image"=>$image, "name"=>$prodName);
}

header('Content-Type: application/json');
echo json_encode($resultArray);


?>
