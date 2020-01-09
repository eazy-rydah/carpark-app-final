<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Config;
use \App\Models\User;
use \App\Models\Client;
use \App\Models\ContractRequestData;

/**
 * Login controller
 * 
 * PHP version 7.0
 */ 
class ContractRequest extends Authenticated
{
 
    /**
     * Before filter - called before each action method
     * 
     * @return void
    */ 
    protected function before()
    {
        parent::before();

        $this->user = AuthMethod::getUser();

    }


     /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAllAction()
    {
    
        if ($this->user->role_id != Config::ROLE_CUSTOMER_SERVICE) {

            FlashMessage::add('Mitarbeiterberechtigung erforderlich', FlashMessage::INFO);
            $this->redirect('/');

        } else {

            $requests = ContractRequestData::getAll();
            View::renderTemplate('contractrequest/all.html', [
                'requests' => $requests
            ]);
        }
    }

    /**
     * Delete selected contract request 
     * 
     * @return void
     */  
    public function deleteAction()
    {
    
        $request = ContractRequest::findByID($this->route_params['id']);

        if ($request) {

            if ($this->user->id == $request->client_id) {

                FlashMessage::add('Vertragsanfrage zurückgezogen', FlashMessage::INFO);

                $request->delete();

                $this->redirect('/contractrequest/show');
    
            } else {
    
                $this->redirect('/contractrequest/show');
                
            }

        } else {

            $this->redirect('/');

        }
    }

    /**
     * Show the contract details page
     * 
     * @return void
     */
    public function showDetailsAction()
    {
    
        if ($this->user->role_id != Config::ROLE_CUSTOMER_SERVICE) {

            FlashMessage::add('Mitarbeiterberechtigung erforderlich', FlashMessage::INFO);

            $this->redirect('/');

        } else {

            $id = $this->route_params['id'];

            $request= ContractRequestData::findByID($id);

            $relatedClient = User::findByID($request->client_id);
    
            View::renderTemplate('contractrequest/details.html', [
               'client' => $relatedClient, 
               'request' => $request
            ]);
        }
    }

    /**
     * Deny a contract Request
     * 
     * @return void
     */
    public function denyAction()
    {
    
        if ($this->user->role_id != Config::ROLE_CUSTOMER_SERVICE) {

            FlashMessage::add('Mitarbeiterberechtigung erforderlich', FlashMessage::INFO);

            $this->redirect('/');

        } else {

            $id = $this->route_params['id'];

            $request = ContractRequestData::findByID($id);
            
            $relatedClient = Client::findByID($request->client_id);
          
            $relatedClient->sendRequestDenyEmail($request);

            $request->delete();

            FlashMessage::add('Vertragsanfrage abgelehnt', FlashMessage::INFO);

            $this->redirect('/contractrequest/show-all');
        }
    }
}