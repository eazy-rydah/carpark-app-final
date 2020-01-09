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
        $text = View::getTemplate('ParkingRequest/deny_email.txt',
                                 ['request' => $request]);
        $html = View::getTemplate('ParkingRequest/deny_email.html',
                                 ['request' => $request]);

        Mail::send($this->email, 'Parkplatz Anfrage', $text, $html);
    }

    /**
     * Send an email to the client containing the contract confirmation message
     * 
     * @param object $contract The contract that is confirmed
     * 
     * @return void
     */
    public function sendContractConfirmationEmail($contract)
    {
        $text = View::getTemplate('Contract/confirm_email.txt',
                                 ['contract' => $contract]);
        $html = View::getTemplate('Contract/confirm_email.html',
                                 ['contract' => $contract]);

        Mail::send($this->email, 'Parkplatz Anfrage', $text, $html);
    }
}
