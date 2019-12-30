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

            $this->redirect('/signup/success');
    
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
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
}