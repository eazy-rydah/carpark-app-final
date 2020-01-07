<?php

namespace App\Models;

use \Core\View;
use \App\Mail;

/**
 * User model
 *
 * PHP version 7.0
 */
class Client extends User
{

    /**
     * Error messages
     * 
     * @var array
     */  
    public $errors = [];

    /**
     * Class constructor
     * 
     * @param array $data Initial property values (optional)
     * 
     * @return void
     */  
    public function __construct($data = [])
    {
      
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

    }

    /**
     * Send an email to the client containing the request deny message
     * 
     * @param object $request The request that is denied
     * 
     * @return void
     */
    public function sendRequestDenyEmail($request)
    {
        $text = View::getTemplate('ContractRequest/deny_email.txt',
                                 ['request' => $request]);
        $html = View::getTemplate('ContractRequest/deny_email.html',
                                 ['request' => $request]);

        Mail::send($this->email, 'Vertragsanfrage abgelehnt', $text, $html);
    }
}