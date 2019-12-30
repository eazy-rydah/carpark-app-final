<?php

namespace App;

/**
 * Flash Notification messages: messages for one-time display using the session
 * for storage between requests
 * 
 * PHP version 7.0
 */ 
class FlashMessage
{
    /**
     * Add a message
     * 
     * @param string message The message content
     * 
     * @return void
     */ 
    public static function add($message)
    {
        // Create array in the session if it doesnt already exist
        if (! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        // Append the message to the array
        $_SESSION['flash_notifications'][] = $message;
    }

    /**
     * Get all the messages
     * 
     * @return mixed an Array with all the messages or null if none set
     */  
    public static function getAll()
    {
        if(isset($_SESSION['flash_notifications'])) {
            $messages =  $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);

            return $messages;
        }
    }
}