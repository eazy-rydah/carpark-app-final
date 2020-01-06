<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\ContractRequestData;

/**
 * Login controller
 * 
 * PHP version 7.0
 */ 
class Contract extends EmployeeAuth
{
    /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showRequestsAction()
    {
        $requests = ContractRequestData::getAll();

        View::renderTemplate('ContractRequest/show-all.html', [
            'requests' => $requests
        ]);

    }
}