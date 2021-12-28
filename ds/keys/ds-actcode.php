<?php
if ($_SERVER['DOCUMENT_ROOT'] == "/users/aufregendetage/Sites") {
    $ds_actcode = ',cay*XmL9-Sef$M{7/W`\)';
}
else if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
        $ds_actcode = ',cay*XmL9-Sef$M{7/W`\)';
}
else {
    mail ("pantheon@manyisles.ch", "Attempt to Get actcode", $_SERVER['DOCUMENT_ROOT']);
}
?>