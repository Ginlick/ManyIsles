<!DOCTYPE html>
<html>
<body>

<table style="text-align:left;">
<tr>
<th>Name</th>
<th>Views</th>
<th>Downloads</th>
</tr>
</tr>
<?php
$servername = "localhost:3306";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "manyisle_accounts";

if ($_SERVER['REMOTE_ADDR']=="::1"){
$servername = "localhost";
$username = "aufregendetage";
$password = "vavache8810titigre";
$dbname = "accounts";
}


$conn = new mysqli($servername, $username, $password, $dbname);

$nume = 0;
$grantot = 0;
$downtot = 0;


    $currsearch = "SELECT * FROM products";
    $toprow = $conn->query($currsearch);
    while ($row = $toprow->fetch_row()) {
             $titling = $row[1];
             $view = $row[8];
             $down = $row[9];
             $grantot = $grantot + $view;
             $downtot = $downtot + $down;
             echo "<tr><th>".$titling."</th><th>".$view."</th><th>".$down."</th></tr>";
    }


echo "<tr><th>Total</th><th>".$grantot."</th><th>".$downtot."</th></tr>";
?>
</table>
</body>
</html>