<?php
if ($_SERVER['DOCUMENT_ROOT'] == "/users/aufregendetage/Sites") {
    $stripe_sk = 'sk_test_51ISIrrGOB5vZzhaU86xCD9atLIIDqYAiODYOqTLru6k9xJBVlXV9RufzMknFROG3hTn7GfuQdpUH685YdMtRH4FR00jkGsuKwm';
    $stripe_whsec = "whsec_JQZvEoyiBPkJ9f6Z5c32B66kB0fTTYcd";
    $YOUR_DOMAIN = 'http://localhost:80';
}
else if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
	$stripe_sk = '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';
    $stripe_whsec = "whsec_5eJgg2TgsUa6MZydh01Zr1MPXAET76RB";
	$YOUR_DOMAIN = 'https://manyisles.ch';
}
else {
    mail ("pantheon@manyisles.ch", "Attempt to Get sk", $_SERVER['DOCUMENT_ROOT']);
}
?>