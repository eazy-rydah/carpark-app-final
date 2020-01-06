<?php

namespace App\Controllers;

use \App\AuthMethod;
use \App\FlashMessage;

/**
 * Administrator base controller
 * 
 * PHP version 7.0
 */ 
abstract class EmployeeAuth extends Authenticated
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

        $this->requireEmployee();

    }

     /**
     * Require the user to be administrator before giving access to the 
     * requested page.
     * 
     * Remember the requested page for later, then redirect to the login page.
     * 
     * @return void
     */ 
    protected function requireEmployee()
    {
        if ($this->user->type != 'employee') {

            FlashMessage::add('Mitarbeiterrolle erforderlich', FlashMessage::INFO);

            $this->redirect('/');
        }
    }
}