<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\CarparkData;
use \App\Models\ContractData;
use \App\Models\User;

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

        // get client by id
        $client = User::findByID($contract->client_id);

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

        $client = User::findByID($contract->client_id);
        
        if ($contract->save()) {

            FlashMessage::add('Vertrag erfolgreich angelegt', FlashMessage::SUCCESS);

            $this->redirect('/contract/show-all');
    
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
     * Show the contract details page
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->route_params['id'];

        $contract= ContractData::findByID($id);

        $relatedClient = User::findByID($contract->client_id);

        $carparks = CarparkData::getAll();

       View::renderTemplate('Contract/edit.html', [
            'client' => $relatedClient, 
            'contract' => $contract,
            'carparks' => $carparks
        ]);
    }
}