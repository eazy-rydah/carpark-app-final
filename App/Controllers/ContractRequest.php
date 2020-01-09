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
     * Show the contract request page
     * 
     * @return void
     */
    public function showAction()
    {
        $user = AuthMethod::getUser();

        $this->requests = ContractRequestData::findAllByUserID($user->id);

        View::renderTemplate('ContractRequest/new.html', [
            'contractRequests' => $this->requests
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
            View::renderTemplate('ContractRequest/all.html', [
                'requests' => $requests
            ]);
        }
    }

    /**
     * Confirm the contract request 
     * 
     * @return void
     */
    public function confirmAction()
    {
        $user = AuthMethod::getUser();

        $request = new ContractRequestData($_POST, $user->id);

        $requests = ContractRequestData::findAllByUserID($user->id);

        if ($request->save()) {
          
            $this->redirect('/ContractRequest/show-success');
    
        } else {

           View::renderTemplate('ContractRequest/new.html', [
               'contractRequest' => $request,
               'contractRequests' => $requests
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
        View::renderTemplate('ContractRequest/success.html');
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
    
            View::renderTemplate('ContractRequest/details.html', [
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