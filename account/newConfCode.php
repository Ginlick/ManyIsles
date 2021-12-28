<?php
//requires: $conn, $id
//returns: $conCode, sendConfMail()

$query = "DELETE FROM confirmer WHERE id = $id"; $conn->query($query);
    $chars = "abcdefghijkmnopqrstuvwxyz0123456789"; 
    srand((double)microtime()*1000000); 
    $i = 0; 
    $conCode = '' ; 

    while ($i <= 7) { 
        $num = rand() % 33; 
        $tmp = substr($chars, $num, 1); 
        $conCode = $conCode . $tmp; 
        $i++; 
    } 
$query = "INSERT INTO confirmer (id, code) VALUES ($id, '$conCode')";
$conn->query($query);


function sendConfMail($conCode, $to) {  
    $subject = "Confirm Email";
    $message = <<<MYGREATMAIL
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <img src="http://manyisles.ch/Imgs/PopupBar.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
        <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Confirm Email</h1>
        <p style="
                text-align: center;
                font-size: calc(8px + 0.9vw);
                color: black;
                padding:10px;
        ">
            Click the button below to confirm your Many Isles email.
            <br>You may receive this message due to having changed your account's email address, or having requested a new code from your account page.

        </p>
        <button class="popupButton" style="margin:2vw auto 2vw;padding:10px;display:block;background-color:red;border:0px;border-radius:4px;font-weight:bold;color:white;font-size:20px;"><a href="http://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife" style="text-decoration:none;color:white;">Confirm and Join</a></button>
        <p style='color: #7d7d7d; font-size: calc(10px + 0.4vw); text-align: center; '>If the button does not work, try moving the email out of your spam folder, or paste this link into your browser: <a href="http://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife">http://manyisles.ch/account/ConfirmMail.php?id=massiveTreeofLife</a> </p>
    </body>
    </html>
MYGREATMAIL;
    $headers = "From: pantheon@manyisles.ch" . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    $message = str_replace("massiveTreeofLife", $conCode, $message);

    if (mail($to, $subject, $message, $headers)) {
        return true;
    }
    else {
        return false;
    }
}

?>