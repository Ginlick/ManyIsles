<?php
if (!function_exists ("clearImgUrl")) {
    function clearImgUrl($image) {
        if (!str_contains($image, "://")) {
            return "/ds/images/".$image;
        }
        else {
            return $image;
        }
    }
}
if (!function_exists ("makeHuman")) {
    function makeHuman($ordiprice) {
        $price = "$".number_format($ordiprice/100, 2, ".", "'");
        $price = str_replace(".00", "", $price);
        $price = str_replace("$-", "-$", $price);
        return $price;
    }
}
if (!function_exists ("linki")) {
    function linki($id, $link, $name = "item") {
        if ($link != ""){
            return "/ds/".$link;
        }
        else {
            return "/ds/".$id."/".str_replace(" ", "_", $name);
        }
        return "$".number_format($ordiprice, 2, ".", "'");
    }
}
function detailsUL($specs, $codes = []){
    if (!function_exists("unparseTxt")){
        require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");
    }
    $fullUL = "<ul>";
    foreach ($specs as $addInfo){
        $fullUL .= "<li>".txtUnparse($addInfo, 1)."</li>";
    }
    foreach ($codes as $addInfo){
        $fullUL .= "<li style='color:green'>".$addInfo."</li>";
    }
    return $fullUL."</ul>";
}
function detailsLine($name, $specs){
    if (!function_exists("unparseTxt")){
        require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parseTxt.php");
    }
    $fullLine = $name." (";
    foreach ($specs as $addInfo) {
        $fullLine .= txtUnparse($addInfo, 1).", ";
    }
    $fullLine = substr($fullLine, 0, strlen($fullLine)-2).")";
    if (count($specs)==0){$fullLine = substr($fullLine, 0, strlen($fullLine)-1);}
    return $fullLine;
}

?>