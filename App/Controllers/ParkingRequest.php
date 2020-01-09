<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Config;
use \App\Models\User;
use \App\Models\ContractRequest;

/**
 * Login controller
 * 
 * PHP version 7.0
 */ 
class ParkingRequest extends ClientAuth
{
 
    /**
     * Before filter - called before each action method
     * 
     * @return void
    */ 
    protected function before()
    {
        parent::before();

        $this->requests = ContractRequest::findAllByUserID($this->user->id);

    }
  
    /**
     * Show page to create new request
     * 
     * @return void
     */
    public function showAction()
    {

        View::renderTemplate('parkingrequest/new.html', [
            'requests' => $this->requests
        ]);

    }

    /**
     * Create the parking request 
     * 
     * @return void
     */
    public function createAction()
    {
        $request = new ContractRequest($_POST, $this->user->id);

        if ($request->save()) {

            $this->redirect('/parkingrequest/show-success');

        } else {

            View::renderTemplate('parkingrequest/new.html', [
                'request' => $request,
                'requests' => $this->requests
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
        View::renderTemplate('parkingrequest/success.html');
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

                FlashMessage::add('Anfrage zurückgezogen', FlashMessage::INFO);

                $request->delete();

                $this->redirect('/parkingrequest/show');
    
            } else {
    
                $this->redirect('/parkingrequest/show');
                
            }

        } else {

            $this->redirect('/');

        }
    }

   

}