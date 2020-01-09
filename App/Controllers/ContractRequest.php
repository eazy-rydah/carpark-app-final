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
     * Show page to create new request
     * 
     * @return void
     */
    public function showAction()
    {
        $user = AuthMethod::getUser();

        $this->requests = ContractRequestData::findAllByUserID($user->id);

        View::renderTemplate('contractrequest/new.html', [
            'requests' => $this->requests
        ]);

    }

     /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAllAction()
    {
        $user = AuthMethod::getUser();

        if ($user->role_id != Config::ROLE_CUSTOMER_SERVICE) {

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
     * Create the contract request 
     * 
     * @return void
     */
    public function createAction()
    {
        $user = AuthMethod::getUser();

        $request = new ContractRequestData($_POST, $user->id);

        $requests = ContractRequestData::findAllByUserID($user->id);

        if ($request->save()) {
          
            $this->redirect('/contractrequest/show-success');
    
        } else {

           View::renderTemplate('contractrequest/new.html', [
               'request' => $request,
               'requests' => $requests
           ]);
        }
    }

     /**
     * Show the contract request success page
     * 
     * @return void
     */  
    public function showSuccessAction()
    {
        View::renderTemplate('contractrequest/success.html');
    }

    /**
     * Delete selected contract request 
     * 
     * @return void
     */  
    public function deleteAction()
    {
        $user = AuthMethod::getUser();

        $request = ContractRequestData::findByID($this->route_params['id']);

        if ($request) {

            if ($user->id == $request->client_id) {

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
        $user = AuthMethod::getUser();

        if ($user->role_id != Config::ROLE_CUSTOMER_SERVICE) {

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
        $user = AuthMethod::getUser();

        if ($user->role_id != Config::ROLE_CUSTOMER_SERVICE) {

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