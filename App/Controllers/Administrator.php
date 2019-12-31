<?php

namespace App\Controllers;

use \App\AuthMethod;
use \App\FlashMessage;

/**
 * Administrator base controller
 * 
 * PHP version 7.0
 */ 
abstract class Administrator extends Authenticated
{
    /**
     * Require the user to be authenticated before giving access to all methods * in the controller
     * 
     * @return void
     */ 
    protected function before()
    {
        parent::before();

        $this->user = AuthMethod::getUser();

        $this->requireAdmin();

    }

     /**
     * Require the user to be administrator before giving access to the 
     * requested page.
     * 
     * Remember the requested page for later, then redirect to the login page.
     * 
     * @return void
     */ 
    protected function requireAdmin()
    {
        if (! $this->user->is_admin) {

            FlashMessage::add('Administratorrechte erforderlich', FlashMessage::INFO);

            $this->redirect('/');
        }
    }
}