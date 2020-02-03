<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\Carpark;
use \App\Models\Contract;
use \App\Models\Share;

/**
 * Contract controller
 * 
 * PHP version 7.0
 */ 
class Parking extends ClientAuth
{
    /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAction()
    {

       $contracts = Contract::findAllByUserID($this->user->user_id);
       $carparks = Carpark::getAll();

       View::renderTemplate('parking/all.html', [
        'contracts' => $contracts,
        'carparks' => $carparks
        ]);

    }

    /**
     * Show the parking details page
     * 
     * @return void
     */
    public function showDetailsAction()
    {
        $id = $this->route_params['id'];

        $parking = Contract::findByID($id);
        $carparks = Carpark::getAll();

        View::renderTemplate('parking/details.html', [
            'contract' => $parking,
            'carparks' => $carparks
        ]);
    }

    /**
     * Share the selected parking
     * 
     * @return void
     */
    public function shareAction()
    {
        
        $id = $this->route_params['id'];
        $contract = Contract::findByID($id);
        $shares = Share::getAllByContractID($contract->contract_id);

        if($shares) {

            Share::checkActiveStatus($shares);

        }

        if ($contract->is_blocked != 1) {

            View::renderTemplate('parking/share_new.html', [
            'contract' => $contract, 
            'shares' => $shares
            ]);

        } else {

            FlashMessage::add('Der Parkplatz kann zur Zeit nicht freigegeben werden. Bitte kontaktieren Sie den Kundenservice', FlashMessage::WARNING);

            $this->redirect('/Parking/show');
        }        
    }

    /**
     * Create share for selected parking
     * 
     * @return void
     */
    public function calculateShareAction()
    {

        $id = $this->route_params['id'];
        $contract = Contract::findByID($id);

        if ($contract->is_blocked != 1) {

            $shares = Share::getAllByContractID($contract->contract_id);
            $share = new Share($_POST);
    
            if ($share->calculateCreditItem($contract)) {

                View::renderTemplate('parking/share_new.html', [
                    'share' => $share,
                    'contract' => $contract,
                    'shares' => $shares
                ]);

            } else {

                View::renderTemplate('parking/share_new.html', [
                    'share' => $share,
                    'contract' => $contract,
                    'shares' => $shares
                ]);
            }
        
        } else {

            FlashMessage::add('Der Parkplatz kann zur Zeit nicht freigegeben werden. Bitte kontaktieren Sie den Kundenservice', FlashMessage::WARNING);

            $this->redirect('/Parking/show');
        }        
    }

      /**
     * show calculated share for selected parking
     * 
     * @return void
     */
    public function confirmShareAction()
    {
        $id = $this->route_params['id'];
        $contract = Contract::findByID($id);
        $carparks = Carpark::getAll();

        if ($contract->is_blocked != 1) {

            $share = new Share($_POST);

            View::renderTemplate('parking/share_confirm.html', [
                'share' => $share,
                'contract' => $contract,
                'carparks' => $carparks
            ]);
        
        } else {

            FlashMessage::add('Der Parkplatz kann zur Zeit nicht freigegeben werden. Bitte kontaktieren Sie den Kundenservice', FlashMessage::WARNING);

            $this->redirect('/Parking/show');
        }        
    }

    
      /**
     * show calculated share for selected parking
     * 
     * @return void
     */
    public function createShareAction()
    {
        $id = $this->route_params['id'];
        $contract = Contract::findByID($id);
        $carparks = Carpark::getAll();

        if ($contract->is_blocked != 1) {

            $shares = Share::getAllByContractID($contract->contract_id);
            $share = new Share($_POST);

            if ($share->save()) {

                FlashMessage::add('Der Parkplatz wurde erfolgreich freigegeben');

                $this->redirect('/Parking/'. $contract->contract_id .'/share');

            } else {

                View::renderTemplate('parking/share_confirm.html', [
                    'share' => $share,
                    'contract' => $contract,
                    'carparks' => $carparks
                ]);
            }
        
        } else {

            FlashMessage::add('Der Parkplatz kann zur Zeit nicht freigegeben werden. Bitte kontaktieren Sie den Kundenservice', FlashMessage::WARNING);

            $this->redirect('/Parking/show');
        }        
    }

    /**
     * Cancel selected share
     * 
     * @return void
     */
    public function cancelShareAction()
    {
        $contract_id = $this->route_params['id'];
        $share_id = $this->route_params['ud'];
        $share = Share::getByID($share_id);

        if ($share->remove()) {

            FlashMessage::add('Die Freigabe wurde erfolgreich storniert', FlashMessage::INFO);

            $this->redirect('/Parking/'. $contract_id .'/share');


        } else {

            FlashMessage::add('Die Freigabe ist bereits aktiv und kann nichtmehr storniert werden', FlashMessage::WARNING);

            $this->redirect('/Parking/'. $contract_id .'/share');

        }
    }
}