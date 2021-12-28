<?php

class mailer {
    public $headers;
    public $domArr = [
        "gen" => [
            "banner" => "https://manyisles.ch/Imgs/PopupGeneral.png"
        ],
        "trade" => [
            "banner" => "https://manyisles.ch/Imgs/PopTrade.png"
        ],
        "acc" => [
            "banner" => "https://manyisles.ch/Imgs/PopupBar.png"
        ],
        "poet" => [
            "banner" => "https://manyisles.ch/Imgs/PopPoet.png"
        ],
        "spell" => [
            "banner" => "https://manyisles.ch/Imgs/PopupSpells.png"
        ]
    ];
    public $baseMail = <<<MYGREATMAIL
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <img src="http://manyisles.ch/Imgs/PopupBar.png" alt="Hello There!" style="width:100%;margin-top:0;margin-bottom:0;display:block;" />
        <h1 style="text-align:center;font-size:calc(12px + 3vw);color:#911414;">Subject</h1>
        <p style="text-align: center;font-size: calc(8px + 0.9vw);color: black;padding:10px;">
            Bodytext
        </p>
    </body>
    </html>
MYGREATMAIL;

    function __construct() {
        $this->headers = "From: pantheon@manyisles.ch" . "\r\n";
        $this->headers .= "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    }

    function send($to, $subject, $message, $domain = "gen", $txtSubj = false){
        $email = $this->baseMail;
        $email = str_replace("http://manyisles.ch/Imgs/PopupBar.png", $this->domArr[$domain]["banner"], $email);
        if ($txtSubj) {$email = str_replace("Subject", $txtSubj, $email);}
        else {$email = str_replace("Subject", $subject, $email);}
        $email = str_replace("Bodytext", $message, $email);

        if (mail ($to, $subject, $email, $this->headers)) {
            return true;
        }
        else {
            return false;
        }
    }
} 

?>