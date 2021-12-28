<?php
//requires: $basketed class
 
if ( session_status() !== PHP_SESSION_ACTIVE ) {session_start();}


require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
require_once("countries.php");

$query = "SELECT * FROM address WHERE id = 11";

if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()){
        $fullname = $row["fullname"];
        $address = $row["address"];
        $city = $row["city"];
        $zip = $row["Zip"];
        $country = $row["Country"];
    }
}

$totalShipping = 0;
$nocountry = false;

if ($basketed->type == "items") {
    foreach ($basketed->itemArray as $item){
        global $totalShipping, $country, $countries;
        $row = $item["row"];
        $artShipping = $row["shipping"];
        $totalShipping += $item["specShipping"];
        if ($artShipping != ""){
            $chunks = array_chunk(preg_split('/(:|,)/', $artShipping), 2);
            $assocDico = array_combine(array_column($chunks, 0), array_column($chunks, 1));

            $hascountry = false;
            foreach ($assocDico as $key => $value) {
                if (strlen($key) == 3){
                //see if it's in a country array
                    $currentArray =  $countries[$key];
                    if (isset($currentArray[$country])){
                        $totalShipping += $value;
                        $hascountry = true;
                        break;
                    }
                }
                else if (strlen($key) == 2){
                //single country
                    if ($key==$country){
                        $totalShipping += $value;
                        $hascountry = true;
                        break;
                    }
                }
            }
        }
        else {$hascountry = true;}

        if (!$hascountry){$nocountry = true;}
    }

    if ($nocountry){
        $totalShipping = null;
    }
}



?>