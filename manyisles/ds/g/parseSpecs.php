<?php
//discontinued
if (!function_exists ("parseSpecs")) {
    function parseSpecs($artSpecs) {
        $specsArray = json_decode($artSpecs, true);
        return $specsArray;
    }
}
if (!function_exists ("unparseSpecs")) {
    function unparseSpecs($specsArray) {
        return json_encode($specsArray);
    }
}

?>
