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

            View::renderTemplate('Signup/success.html');

        } else {

            var_dump($user->errors);

        }
    }
}