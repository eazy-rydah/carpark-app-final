<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\ContractRequestData;
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
     * Show the contract request page
     * 
     * @return void
     */
    public function showRequestsAction()
    {
        $requests = ContractRequestData::getAll();

        View::renderTemplate('ContractRequest/show-all.html', [
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
        $contract = ContractRequestData::findByID($id);
        $client = User::findByID($contract->client_id);
        $carparks = CarparkData::getAll();

        View::renderTemplate('Contract/create.html', [
            'client' => $client,
            'contract' => $contract,
            'carparks' => $carparks
        ]);
    }

    /**
     * create the contract 
     * 
     * @return void
     */
    public function createAction()
    {
        $contract = new ContractData($_POST);
        $client = User::findByID($contract->client_id);
        $carparks = CarparkData::getAll();

        if ($contract->save()) {

            $relatedRequest = ContractRequestData::confirmByContractId($contract->contract_id);

         /* $client->sendContractActivationEmail();
        */
            FlashMessage::add('Vertrag erfolgreich angelegt');

            $this->redirect('/contract/show-requests');

        } else {

            View::renderTemplate('Contract/create.html', [
                'client' => $client,
                'contract' => $contract,
                'carparks' => $carparks
            ]);
        }
    }

    public function denyRequest()
    {
        // TOOODOOO

        // EMAIL ETC
    }

   
}