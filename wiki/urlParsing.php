<?php

function parse2Url($str) {
    return urlencode(str_replace(" ", "_", $str));
}
function artUrl($newArtRoot, $artId, $artName) {
    return $newArtRoot.$artId."/".parse2Url($artName);
}


?>