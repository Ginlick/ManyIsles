<?php
if (isset($_GET["dir"])){
    setcookie("admin", 1, time()+604800, "/");
}
else {
    setcookie("admin", 1, time()-604800, "/");
}
header("Location:hub.php");
?>