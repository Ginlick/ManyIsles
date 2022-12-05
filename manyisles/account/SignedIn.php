<?php
if ($_GET == []){
  header("Location:/account/home"); exit;
}
header("Location:/account/home?".http_build_query($_GET));
?>
