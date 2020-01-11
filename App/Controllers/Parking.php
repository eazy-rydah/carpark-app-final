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

       $contracts = Contract::findAllByUserID($this->user->id);
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
        $parking = Contract::findByID($id);
        $shares = Share::getAllByContractID($parking->id);

        if ($parking->is_blocked != 1) {

            View::renderTemplate('parking/share_new.html', [
            'contract' => $parking, 
            'shares' => $shares
            ]);

        } else {

            FlashMessage::add('Der Parkplatz kann zur Zeit nicht freigegeben werden. Bitte kontaktieren Sie den Kundenservice', FlashMessage::WARNING);

            $this->redirect('/parking/show');
        }        
    }

    /**
     * Create share for selected parking
     * 
     * @return void
     */
    public function createShareAction()
    {
        $id = $this->route_params['id'];
        $parking = Contract::findByID($id);

        if ($parking->is_blocked != 1) {

            $shares = Share::getAllByContractID($parking->id);
            $share = new Share($_POST);
    

            if ($share->save()) {

                FlashMessage::add('Parkplatz erfolgreich freigegeben');
                $this->redirect('/parking/'. $parking->id .'/share');

            } else {

                View::renderTemplate('parking/share_new.html', [
                    'share' => $share,
                    'contract' => $parking,
                    'shares' => $shares
                ]);
            }
        
        } else {

            FlashMessage::add('Der Parkplatz kann zur Zeit nicht freigegeben werden. Bitte kontaktieren Sie den Kundenservice', FlashMessage::WARNING);

            $this->redirect('/parking/show');
        }        
    }

   
}