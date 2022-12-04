<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!class_exists("mailer")){
  require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/Parsedown.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
  require($_SERVER['DOCUMENT_ROOT']."/Server-Side/PHPMailer/PHPMailer.php");
  require($_SERVER['DOCUMENT_ROOT']."/Server-Side/PHPMailer/Exception.php");
  require($_SERVER['DOCUMENT_ROOT']."/Server-Side/PHPMailer/SMTP.php");

  class mailer {
    private $baseInfo;

    function __construct($mailInfo) {
      $this->baseInfo = $mailInfo;
    }

    function sendMail($recipientInfo, $subject, $message, $senderInfo = []) {
      $mail = new PHPMailer(true);

      //Server settings
      //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = $this->baseInfo["host"];                     //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = $this->baseInfo["user_uname"];                     //SMTP username
      $mail->Password   = $this->baseInfo["user_psw"];                               //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
      $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //People
      if ($senderInfo == []){ //sender
        $senderInfo = $this->baseInfo["default_info"];
      }
      else if (gettype($senderInfo) == "string"){
        if (isset($this->baseInfo["more_info"][$senderInfo])){
          $senderInfo = $this->baseInfo["more_info"][$senderInfo];
        }
        else {
          $senderInfo = $this->baseInfo["default_info"];
        }
      }
      $mail->setFrom($senderInfo["address"], $senderInfo["user"]);
      foreach ($recipientInfo as $recipient){ //recipients
        if (isset($recipient[0])){
          $user = "";
          if (isset($recipient[1])){
            $user = $recipient[1];
          }
          $mail->addAddress($recipient[0], $user);
        }
      }
      //other cool options: addReplyTo(address, user), addCC(address), addBCC(address)

      //Content
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body    = $message;
      //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      return ($mail->send());
    }

  }
}

?>
