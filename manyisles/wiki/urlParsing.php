<?php

function parse2Url($str) {
    return urlencode(str_replace(" ", "_", preg_replace("/['\"*:!]/", "", $str)));
}
function artUrl($newArtRoot, $artId, $artName) {
    return $newArtRoot.$artId."/".parse2Url($artName);
}


?>