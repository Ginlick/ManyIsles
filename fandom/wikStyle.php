<?php
// $conn, $parentWiki



$query = "SELECT * FROM wiki_settings WHERE id = $parentWiki";
$firstrow = $conn->query($query);
if (mysqli_num_rows($firstrow) != 0){
    while ($row = $firstrow->fetch_assoc()) {
        if ($row["backgroundColor"] != ""){echo "background-color: ".$row["backgroundColor"].";";}
        echo "background-image: url(".$row["backgroundImg"].");";
    }
}

?>