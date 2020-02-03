<?php

namespace App\Controllers;

use \Core\View;
use \App\FlashMessage;
use \App\Models\Client;
use \App\Models\ContractRequest;

/**
 * Request administration controller
 * 
 * PHP version 7.0
 */ 
class RequestAdministration extends EmployeeCustomerServiceAuth
{
 
     /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAction()
    {
    
        $requests = ContractRequest::getAll();

        View::renderTemplate('requestadministration/all.html', [
            'requests' => $requests
        ]);
        
    }

    /**
     * Show the contract details page
     * 
     * @return void
     */
    public function showDetailsAction()
    {

        $id = $this->route_params['id'];
        $request= ContractRequest::findByID($id);

        $relatedClient = Client::findByID($request->user_id);

        View::renderTemplate('requestadministration/details.html', [
            'client' => $relatedClient, 
            'request' => $request
        ]);

    }

    /**
     * Deny a contract Request
     * 
     * @return void
     */
    public function denyAction()
    {
    
        $id = $this->route_params['id'];
        $request = ContractRequest::findByID($id);
        
        $relatedClient = Client::findByID($request->user_id);
        $relatedClient->sendRequestDenyEmail($request);

        $request->delete();

        FlashMessage::add('Anfrage abgelehnt', FlashMessage::INFO);
        $this->redirect('/RequestAdministration/show');
    }
}