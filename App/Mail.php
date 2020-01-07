<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Config;

/**
 * Mail
 * 
 * PHP version 7.0
 */ 
class Mail
{
    /**
     * Send a message 
     * 
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     * 
     * @return mixed
     */ 
    public static function send($to, $subject, $text, $html)
    {
        $mail = new PHPMailer(true);

        try {

            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                     
            $mail->isSMTP();                                           
            $mail->Host       = Config::MAIL_HOST;                  
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = Config::MAIL_USER;                    
            $mail->Password   = Config::MAIL_PASSWORD;                              
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
            $mail->Port       = 587;                                   
        
            //Recipients
            $mail->setFrom(Config::MAIL_SENDER, Config::MAIL_SENDER_NAME);
            $mail->addAddress($to);     
        
            // Content
            $mail->isHTML(true);                                 
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;
        
            $mail->send();
            
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}