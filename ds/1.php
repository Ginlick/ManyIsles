<?php
session_start();

echo $_SESSION["basket"];


session_destroy();
/*
$value = "3(Aasdutst''as Nes/256)";


$price = substr($value, stripos($value, "/")+1, -1);
$option = substr($value, stripos($value, "(")+1, stripos($value, "/")-2);
echo $option;*/
?>