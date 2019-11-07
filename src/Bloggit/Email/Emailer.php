<?php namespace Bloggit\Email;


/**
* Email class
*
* PHP version > 7.1.27
* @category PHP
* @author Michael Cook <mcook0775@gmail.com>
* @copyright 2019 Michael Cook
* @license https://en.wikipedia.org/wiki/MIT_License MIT License
*/


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Emailer
{

    
    protected $mail;


    /**
    * @param mail requires instantiation of PHPMailer object
    */
    public function __construct(\PHPMailer\PHPMailer\PHPMailer $mail)
    {
        $this->mail = $mail;
        $this->mail->SMTPDebug = 3;
        $this->mail->isSMTP();
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->Port = 587;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = "mc.mctaggart@gmail.com";
        $this->mail->Password = "fxrudikhpvaiiayf";
        // use of personal email for development purposes only
        $this->mail->setFrom('mc.mctaggart@gmail.com', "Bloggit");
        $this->mail->isHTML(true);
    }

    public function addRecipient(Array $recipient)
    {
        $this->mail->addAddress($recipient['address'], $recipient['name']);
    }

    public function setSubject($subject)
    {
        $this->mail->Subject = $subject;
    }

    public function setBody($body)
    {
        $this->mail->Body = $body;
    }

    public function sendEmail()
    {
        try {
            $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}
