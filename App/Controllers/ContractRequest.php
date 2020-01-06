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
class ContractRequest extends ClientAuth
{
    /**
     * Load all existing contractRequests related to current user
     * 
     * @return void
     */ 
    protected function before()
    {
        parent::before();

        $this->requests = ContractRequestData::findAllByID($this->user->id);
    }

    /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAction()
    {

        View::renderTemplate('ContractRequest/show.html', [
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
        View::renderTemplate('ContractRequest/show-all.html');

    }

    /**
     * Confirm the contract request 
     * 
     * @return void
     */
    public function confirmAction()
    {
        $request = new ContractRequestData($_POST, $this->user->id);

        if ($request->save()) {
          
            $this->redirect('/ContractRequest/show-success');
    
        } else {

           View::renderTemplate('ContractRequest/show.html', [
               'contractRequest' => $request,
               'contractRequests' => $this->requests
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
       
        $request = ContractRequestData::findByID($this->route_params['id']);

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
}