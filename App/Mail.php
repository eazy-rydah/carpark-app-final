<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
            $mail->Host       = 's157.goserver.host';                  
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'web53p1';                    
            $mail->Password   = 'ahd9Aegh';                              
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
            $mail->Port       = 587;                                   
        
            //Recipients
            $mail->setFrom("info@carparkapp.com", "Carpark Info");
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