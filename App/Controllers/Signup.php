<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;

/**
 * Signup controller
 * 
 * PHP version 7.0
 */  

class Signup extends \Core\Controller
{

    /**
     * Show the signup page
     * 
     * @return void
     */  
    public function showAction()
    {
        View::renderTemplate('Signup/show.html');
    }

    /**
     * Confirm the signup
     * 
     * @return void
     */  
    public function confirmAction()
    {
        $user = new User($_POST);

        if ($user->save()) {

            if ($user->type == 'client') {

                $user->sendActivationEmail();

            }
          
            $this->redirect('/signup/show-success');
    
        } else {

           View::renderTemplate('Signup/show.html', [
               'user' => $user
           ]);
        }
    }

    /**
     * Show the signup success page
     * 
     * @return void
     */  
    public function showSuccessAction()
    {
        View::renderTemplate('Signup/success.html');
    }

    /**
     * Activate a new account
     * 
     * @return void
     */  
    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/show-activated');
        
    }

    /**
     * Show the activation success page
     * 
     * @return void
     */  
    public function showActivatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}