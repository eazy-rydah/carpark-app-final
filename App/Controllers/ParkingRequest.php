<?php

namespace App\Controllers;

use \Core\View;
use \App\FlashMessage;
use \App\Models\ContractRequest;

/**
 * Parking request controller
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

        $this->requests = ContractRequest::findAllByUserID($this->user->user_id);

    }
  
    /**
     * Show page to create new request
     * 
     * @return void
     */
    public function showAction()
    {

        View::renderTemplate('parkingrequest/new.html', [
            'requests' => $this->requests,
            'user_id' => $this->user->user_id
        ]);

    }

    /**
     * Create the parking request 
     * 
     * @return void
     */
    public function createAction()
    {     
        $request = new ContractRequest($_POST);

        if ($request->save()) {

            $this->redirect('/ParkingRequest/show-success');

        } else {

            View::renderTemplate('parkingrequest/new.html', [
                'request' => $request,
                'requests' => $this->requests,
                'user_id' => $this->user->user_id
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

            if ($this->user->user_id == $request->user_id) {

                FlashMessage::add('Anfrage zurückgezogen', FlashMessage::INFO);

                $request->delete();

                $this->redirect('/ParkingRequest/show');
    
            } else {
    
                $this->redirect('/ParkingRequest/show');
                
            }

        } else {

            $this->redirect('/');

        }
    }   
}