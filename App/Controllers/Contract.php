<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\CarparkData;
use \App\Models\ContractData;
use \App\Models\ContractRequestData;
use \App\Models\Client;

/**
 * Login controller
 * 
 * PHP version 7.0
 */ 
class Contract extends EmployeeAuth
{
 
    /**
     * Show new contract page
     * 
     * @return void
     */
    public function newAction()
    {
        $contract = new ContractData($_POST);

        $client = Client::findByID($contract->client_id);

        $carparks = CarparkData::getAll();

        View::renderTemplate('Contract/new.html', [
            'client' => $client, 
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
        $contract = new ContractData($_POST);

        $client = Client::findByID($contract->client_id);
        
        if ($contract->save()) {

            $relatedRequest = ContractRequestData::findByID($contract->request_id);

            $relatedRequest->confirmByContractID($contract->contract_id);

            $client->sendContractConfirmationEmail($contract);
          
            FlashMessage::add('Vertrag erfolgreich angelegt', FlashMessage::SUCCESS);

            $this->redirect('/contractrequest/show-all');
    
        } else {

            $carparks = CarparkData::getAll();

            View::renderTemplate('Contract/new.html', [
            'client' => $client, 
            'contract' => $contract,
            'carparks' => $carparks
            ]);
        } 
    }

    /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAllAction()
    {
       $contracts = ContractData::getAll();

       View::renderTemplate('Contract/all.html', [
        'contracts' => $contracts
        ]);
    }

    /**
     * Show the contract edit page
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->route_params['id'];

        $contract= ContractData::findByID($id);

        $relatedClient = Client::findByID($contract->client_id);

        $carparks = CarparkData::getAll();

       View::renderTemplate('Contract/edit.html', [
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
        $contract = new ContractData($_POST);

        if ($contract->update($_POST)) {
          
            FlashMessage::add('Änderungen erfolgreich gespeichert', FlashMessage::SUCCESS);

            $this->redirect('/contract/show-all');
    
        } else {

           $relatedClient = Client::findByID($contract->client_id);

           $carparks = CarparkData::getAll();
    
           View::renderTemplate('Contract/edit.html', [
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

        $contract= ContractData::findByID($id);

        if ($contract->block()) {
          
            FlashMessage::add('Vertrag wurde blockiert', FlashMessage::WARNING);

            $this->redirect('/contract/edit/' . $id);
    
        } else {

           $relatedClient = Client::findByID($contract->client_id);

           $carparks = CarparkData::getAll();
    
           View::renderTemplate('Contract/edit.html', [
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

        $contract= ContractData::findByID($id);

        if ($contract->unblock()) {
          
            FlashMessage::add('Vertrag wurde entsperrt', FlashMessage::WARNING);

            $this->redirect('/contract/edit/' . $id);
    
        } else {

           $relatedClient = Client::findByID($contract->client_id);

           $carparks = CarparkData::getAll();
    
           View::renderTemplate('Contract/edit.html', [
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

        $contract= ContractData::findByID($id);

        $relatedClient = Client::findByID($contract->client_id);

        $carparks = CarparkData::getAll();

        View::renderTemplate('Contract/delete.html', [
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

        $contract= ContractData::findByID($id);

        if ($contract->delete()) {

            FlashMessage::add('Vertrag wurde gelöscht', FlashMessage::INFO);

            $this->redirect('/contract/show-all');

        } else {

            $this->redirect('/contract/edit/' . $contract->contract_id);

        }
    }  
}