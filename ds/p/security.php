<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();

$dl->partner("ds");
$conn = $dl->conn;
$pId = $dl->partId;

$query = "SELECT power, acceptCodes FROM partners_ds WHERE id = $pId";
if ($result = $conn->query($query)){
    if (mysqli_num_rows($result) == 0) { $dl->go("activate", "ds"); }
    while ($row = $result->fetch_assoc()) {
      $pPower = $row["power"];
      $pAcceptCodes = $row["acceptCodes"];
    }
}

if (!isset($checkArtId)){$checkArtId = false;}
if (!isset($admin)){$admin = false;}

if ($admin) {
    if ($pPower < 2) {
        $dl->go("killAdmin", "ds");
    }
}
else {
    if ($checkArtId AND $artId != 0) {
        $query = "SELECT sellerId FROM dsprods WHERE id = $artId";
        if ($result = $conn->query($query)){
            if (mysqli_num_rows($result) == 0) { header("Location: ".$redirect);exit(); }
            else {
                while ($row = $result->fetch_assoc()) {
                    if ($row["sellerId"] != $pId) { header("Location: ".$redirect);exit(); }
                }
            }
        }
    }
}

if (!function_exists ("inputChecker")) {
    function inputChecker($input, $preg, $how) {
        global $redirect;
        if (isset($input)) {
            if ((preg_match($preg, $input)==1) == $how){
                header("Location: ".$redirect);echo $redirect;exit();
            } else {
                return($input);
            }
        } else {
            header($redirect);exit();
        }
    }
}

?>
