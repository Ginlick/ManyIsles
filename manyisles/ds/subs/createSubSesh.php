<?php
if ( session_status() !== PHP_SESSION_ACTIVE ) {session_start();}

$option = 0;
if (isset($_GET["o"])){
    if (preg_match("/^[0-9]$/", $_GET["o"])!=1){exit();} else {$option = $_GET["o"];}
}

if (!isset($_SESSION["subbasket"])) {$_SESSION["subbasket"]="";}
$subbask = json_decode($_SESSION["subbasket"], true);

$subId = 1;
if ($option == 1){$subId = 2;}

$subscription = [[
    "id" => $subId
]];

$subbask = $subscription;

$_SESSION["subbasket"] = json_encode($subbask);
echo "<script>window.location.replace('/ds/checkout2')</script>";
?>
