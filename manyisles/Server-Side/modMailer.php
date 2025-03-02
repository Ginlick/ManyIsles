<?php
require($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
class modMailer {
    use allBase;
    public $headers;
    public $domArr = [
      "website" => ["img"=>"https://kartecaedras.ch/Imgs/branding/s/website.png"],
      "publishing" => ["img"=>"https://kartecaedras.ch/Imgs/branding/s/publishing.png"],
      "community" => ["img"=>"https://kartecaedras.ch/Imgs/branding/s/community.png"],
      "pantheon" => ["img"=>"https://kartecaedras.ch/Imgs/branding/s/pantheon.png"],
    ];
    public $baseMail = <<<MYGREATMAIL
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title></title>
        <style>
          a {color:#61b3dd;}
        </style>
    </head>
    <body style="padding:0;margin:0;">
      <section style="max-width: 900px; margin: auto;background-image:url(https://kartecaedras.ch/Imgs/OshBacc.png);background-color: #8dceff;background-attachment: fixed; background-size: contain;padding-top: 1px;">
        <div style="width: 100%;
        height: 200px;
        background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.5));"></div>
        <div style="background-color: white;padding: 10px 0;">
          <div style="width: 85%; margin:auto;">
            <h1 style="font-size: 30px;font-family:'Trebuchet MS', 'Roboto', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color:black; padding: 30px 10px 10px;">Subject</h1>
            <p style="padding:10px;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">
                Bodytext
            </p>
          </div>
          <div style="border-top: 3px solid #61b3dd;text-align:center;margin:200px 0">
            <img src="https://kartecaedras.ch/Imgs/branding/s/website.png" alt="Many Isles logo" style="width:250px;margin:30px auto; display:block;" />
            <a href="https://kartecaedras.ch" style="color:#61b3dd;font-size: 16px;line-height:1.4;font-family:'Lato', Arial, Helvetica, sans-serif;">kartecaedras.ch</a>
          </div>
        </div>
      </section>
    </body>
    </html>
MYGREATMAIL;

    function __construct() {
      $this->mailer =  $this->addMailer();
    }

    function send($to, $subject, $message, $domain = "website", $txtSubj = false){
      $body = $this->baseMail;
      
      if (!isset($this->domArr[$domain])){$domain = "website";}
      $body = str_replace("https://kartecaedras.ch/Imgs/branding/s/website.png", $this->domArr[$domain]["img"], $body);

      if ($txtSubj) {$body = str_replace("Subject", $txtSubj, $body);}
      else {$body = str_replace("Subject", $subject, $body);}

      $body = str_replace("Bodytext", $message, $body);
      return $this->mailer->sendMail([[$to, ""]], $subject, $body, $domain);
    }
}

?>
