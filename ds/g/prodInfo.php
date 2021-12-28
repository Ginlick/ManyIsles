
<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$q = urldecode($_GET["q"]);

$searchstring = 'SELECT * FROM products WHERE name = "'.$q.'"';

if ($max = $conn->query($searchstring)) {
    while ($gay = $max->fetch_assoc()){
        $partner = $gay["partner"];
        $image = $gay["image"];
    }
}
if ($partner == "Pantheon"){$partner = "the Pantheon";}
$resultArray = array("partner"=>$partner, "image"=>$image);


header('Content-Type: application/json');
echo json_encode($resultArray);


?>