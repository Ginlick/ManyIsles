<?php
if ($_SERVER['DOCUMENT_ROOT'] == "/users/aufregendetage/Sites") {
    $stripe_pk = 'pk_test_51ISIrrGOB5vZzhaUZj92wQpz3zNI8of03JGQNB4LwNyTp8OODIRLpBnCBHgG3xydAfexDNn63yn4BidHjrq4yqow00zOL0FmYb';
}
else if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
    $stripe_pk = 'pk_live_51ISIrrGOB5vZzhaUiqWk7OnynkJr7ZsbSiyShkyiNf1pKuk115x97tSP3gwL84227IdAL5HxYrqz5UefvWdhNRbP00JUdHxiWt';
}
else {
    mail ("pantheon@manyisles.ch", "Attempt to Get pk", $_SERVER['DOCUMENT_ROOT']);
}
?>