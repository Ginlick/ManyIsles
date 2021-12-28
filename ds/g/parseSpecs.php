<?php
if (!function_exists ("parseSpecs")) {
    function parseSpecs($artSpecs) {
        $specsArray = array();
        $buildSpecsArray = explode("]", $artSpecs);
        foreach ($buildSpecsArray as $buildProtoSpecArray){
            if (!str_contains($buildProtoSpecArray, "name:")){continue;}
            $chunks = array_chunk(preg_split('/(:|;)/', $buildProtoSpecArray), 2);
            $buildSpecArray = array_combine(array_column($chunks, 0), array_column($chunks, 1));

            $buildProtoSpecOptionsArray = explode("[", $buildSpecArray["options"]);
            $buildSpecArray["options"] = array();

            foreach ($buildProtoSpecOptionsArray as $value){
                $chunks = array_chunk(preg_split('/(-|,)/', $value), 2);
                $buildSpecOptionsArray = array_combine(array_column($chunks, 0), array_column($chunks, 1));
                $buildSpecOptionsArray["price"] =  str_replace("min", "-", $buildSpecOptionsArray["price"]);
                $buildSpecArray["options"][] = $buildSpecOptionsArray;
            }
            $specsArray[] = $buildSpecArray;
        }
        return $specsArray;
    }
}
if (!function_exists ("unparseSpecs")) {
    function unparseSpecs($specsArray) {
        foreach ($specsArray as $key => $specArray){
            $list = [];
            $fullList = [];
            foreach ($specArray["options"] as $optionArray) {
                $optionArray["price"] =  str_replace("-", "min", $optionArray["price"]);
                foreach ($optionArray as $key2 => $value) {
                    $list[] = "$key2-$value";
                }
                $fullList[] = implode(",", $list);
            }
            $specArray["options"] = implode("[", $fullList);
            $list = [];
            foreach ($specArray as $key3 => $value) {
                $list[] = "$key3:$value";
            }
            $specsArray[$key] = implode(";", $list);
        }
        $artSpecs = implode("]", $specsArray);
        return $artSpecs;
    }
}

?>