<?php
if (preg_match("/^[0-9]*$/", $_GET["id"])!=1){header("Location:hub.php");exit();}
if (preg_match("/^[-0-9]*$/", $_GET["dir"])!=1){header("Location:hub.php");exit();}
if(!isset($_COOKIE["loggedIn"])){header("Location: checkout.html");exit();}
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/dsEngine.php");
$ds = new dsEngine;
$ds->killCache();
$ds->user->check(true, true, true);

$id = $ds->user->user;
$clid = $ds->purify($_GET["id"], "quotes");
$dir =$ds->purify($_GET["dir"], "number"); ;
$conn = $ds->conn;


$query = 'SELECT * FROM partners WHERE user = '.$id;
if ($firstrow = $conn->query($query)) {
    while ($row = $firstrow->fetch_assoc()) {
      $pId = $row["id"];
      $status = $row["status"];
    }
}
if (!isset($pId)){header("Location: /account/BePartner.php");exit();}
//if ($status == "suspended" OR $status == "pending"){header("Location: /account/Publish.php");exit();}

$query = 'SELECT * FROM dsorders WHERE orderId = '.$clid.' AND seller = '.$pId;
if ($firstrow = $conn->query($query)) {
    if (mysqli_num_rows($firstrow) == 0) {header("Location:hub.php");exit();}
    while ($row = $firstrow->fetch_assoc()) {
        $ordStatus = $row["status"];
        if ($row["seller"]!=$pId){header("Location:hub.php");exit();}
        $ordUd = $row["ud"];
    }
}

$dir = intval($dir);
if ($dir == -1 AND $ordStatus > 0){
    $query = "UPDATE dsorders SET status = status + $dir WHERE ud = $ordUd";
    if ($conn->query($query)){
        header("Location:order.php?id=".$clid);
    }
}
else if ($dir == 1 AND $ordStatus < 2){
    $query = "UPDATE dsorders SET status = status + $dir WHERE ud = $ordUd";
    if ($conn->query($query)){
        header("Location:order.php?recStatChange=true&id=".$clid);
    }
}

?>
