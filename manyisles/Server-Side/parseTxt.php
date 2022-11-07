<?php
if (!function_exists ("txtParse")) {
    function txtParse($txt, $level=1) {
        $txt = str_replace('"', '%double_quote%', $txt);
        $txt = str_replace("'", '%single_quote%', $txt);
        if ($level > 1) {
            $txt = str_replace(":", '%colon%', $txt);
            $txt = str_replace(";", '%pcolon%', $txt);
            $txt = str_replace("-", '%hyphon%', $txt);
            $txt = str_replace(",", '%comma%', $txt);
            $txt = str_replace("[", '%sqbrak_left%', $txt);
            $txt = str_replace("]", '%sqbrak_right%', $txt);
            if ($level > 2){
              $txt = str_replace("'", '', $txt);
            }
        }
        return $txt;
    }
}
if (!function_exists ("txtUnparse")) {
    function txtUnparse($txt, $level = 0) {
        if ($level == 1){
            $txt = str_replace('%double_quote%', '', $txt);
            $txt = str_replace("%single_quote%", "", $txt);
        }
        else {
            $txt = str_replace('%double_quote%', '"', $txt);
            $txt = str_replace("%single_quote%", "'", $txt);
        }
        if ($level < 2){
            $txt = str_replace("%colon%", ':', $txt);
            $txt = str_replace("%pcolon%", ';', $txt);
            $txt = str_replace("%hyphon%", '-', $txt);
            $txt = str_replace("%comma%", ',', $txt);
            $txt = str_replace("%sqbrak_left%", '[', $txt);
            $txt = str_replace("%sqbrak_right%", ']', $txt);
            $txt = str_replace("%qbrak_left%", '{', $txt);
            $txt = str_replace("%qbrak_right%", '}', $txt);
        }
        return $txt;
    }
}

?>
