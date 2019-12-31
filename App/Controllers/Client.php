<?php

namespace App\Controllers;

use \App\AuthMethod;
use \App\FlashMessage;

/**
 * Client base controller
 * 
 * PHP version 7.0
 */ 
abstract class Client extends Authenticated
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

        $this->requireClient();

    }

     /**
     * Require the user to of type client in before giving access to the 
     * requested page.
     * 
     * Remember the requested page for later, then redirect to the login page.
     * 
     * @return void
     */ 
    protected function requireClient()
    {
        if ($this->user->type != 'client') {

            FlashMessage::add('Kundenrolle erforderlich', FlashMessage::INFO);

            $this->redirect('/');
        }
    }
}