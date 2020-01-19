<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\Carpark;
use \App\Models\Contract;
use \App\Models\ContractRequest;
use \App\Models\Client;

/**
 * Contract controller
 * 
 * PHP version 7.0
 */ 
class ContractAdministration extends EmployeeCustomerServiceAuth
{
    /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAction()
    {
       $contracts = Contract::getAll();

       View::renderTemplate('contractadministration/all.html', [
        'contracts' => $contracts
        ]);
    }

    /**
     * Show new contract page
     * 
     * @return void
     */
    public function newAction()
    {
        $contract = new Contract($_POST);
        $relatedClient = Client::findByID($contract->user_id);

        $carparks = Carpark::getAll();

        View::renderTemplate('contractadministration/new.html', [
            'client' => $relatedClient, 
            'contract' => $contract,
            'carparks' => $carparks
        ]); 
    }

    /**
     * create new contract
     * 
     * @return void
     */
    public function createAction()
    {
        $contract = new Contract($_POST);
        $client = Client::findByID($contract->user_id);
        
        if ($contract->save()) {

            $relatedRequest = ContractRequest::findByID($contract->contract_request_id);
            $relatedRequest->confirmByContractID($contract->contract_id);

            $client->sendContractConfirmationEmail($contract);
          
            FlashMessage::add('Vertrag erfolgreich angelegt');
            $this->redirect('/requestadministration/show');
    
        } else {

            $carparks = Carpark::getAll();

            View::renderTemplate('contractadministration/new.html', [
            'client' => $client, 
            'contract' => $contract,
            'carparks' => $carparks
            ]);
        } 
    }

    /**
     * Show the contract edit page
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->route_params['id'];
        $contract = Contract::findByID($id);

        $relatedClient = Client::findByID($contract->user_id);
        $carparks = Carpark::getAll();

        View::renderTemplate('contractadministration/edit.html', [
            'client' => $relatedClient, 
            'contract' => $contract,
            'carparks' => $carparks
        ]);
    }

    /**
     * Update contract changes
     * 
     * @return void
     */
    public function updateAction()
    {
        $contract = new Contract($_POST);

        if ($contract->update()) {
          
            FlashMessage::add('Änderungen erfolgreich gespeichert', FlashMessage::SUCCESS);

            $this->redirect('/contractadministration/show');
    
        } else {

           $relatedClient = Client::findByID($contract->client_id);

           $carparks = Carpark::getAll();
    
           View::renderTemplate('contractadministration/edit.html', [
                'client' => $relatedClient, 
                'contract' => $contract,
                'carparks' => $carparks
            ]);
        } 
    }

    /**
     * Block contract
     * 
     * @return void
     */
    public function blockAction()
    {
        $id = $this->route_params['id'];
        $contract= Contract::findByID($id);

        if ($contract->block()) {
          
            FlashMessage::add('Vertrag wurde blockiert', FlashMessage::WARNING);
            $this->redirect('/contractadministration/edit/' . $id);
    
        } else {

           $relatedClient = Client::findByID($contract->client_id);
           $carparks = Carpark::getAll();
    
           View::renderTemplate('contractadministration/edit.html', [
                'client' => $relatedClient, 
                'contract' => $contract,
                'carparks' => $carparks
            ]);
        } 
    }

    /**
     * Block contract
     * 
     * @return void
     */
    public function unblockAction()
    {
        $id = $this->route_params['id'];
        $contract= Contract::findByID($id);

        if ($contract->unblock()) {
          
            FlashMessage::add('Vertrag wurde entsperrt', FlashMessage::WARNING);
            $this->redirect('/contractadministration/edit/' . $id);
    
        } else {

           $relatedClient = Client::findByID($contract->client_id);
           $carparks = Carpark::getAll();
    
           View::renderTemplate('contractadministration/edit.html', [
                'client' => $relatedClient, 
                'contract' => $contract,
                'carparks' => $carparks
            ]);
        } 
    }

    /**
     * Show delete confirmation
     * 
     * @return void
     */
    public function deleteAction()
    {        
        $id = $this->route_params['id'];
        $contract= Contract::findByID($id);

        $relatedClient = Client::findByID($contract->user_id);
        $carparks = Carpark::getAll();

        View::renderTemplate('contractadministration/delete.html', [
            'client' => $relatedClient, 
            'contract' => $contract,
            'carparks' => $carparks
        ]);
    }  

    /**
     * Confirm deletion
     * 
     * @return void
     */
    public function confirmDeletionAction()
    {        
        $id = $this->route_params['id'];

        $contract= Contract::findByID($id);

        if ($contract->delete()) {

            FlashMessage::add('Vertrag wurde gelöscht', FlashMessage::INFO);

            $this->redirect('/contractadministration/show');

        } else {

            $this->redirect('/contractadministration/edit/' . $contract->contract_id);
        }
    }  
}