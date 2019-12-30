<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;

/**
 * Login controller
 * 
 * PHP version 7.0
 */ 
class Login extends \Core\Controller
{
    /**
     * Show the login page
     * 
     * @return void
     */
    public function showAction()
    {
        View::renderTemplate('Login/show.html');
    }

    /**
     * Log in a user
     * 
     * @return void
     */
    public function confirmAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        if ($user) {

            AuthMethod::login($user);

            FlashMessage::add('Anmeldung erfolgreich');

            $this->redirect(AuthMethod::getRequestedPage());

        } else {

            FlashMessage::add('Anmeldung fehlgeschlagen');

            View::renderTemplate('Login/show.html', [
                'email' => $_POST['email']
            ]);
        }
    }

    /**
     * Log out a user
     * 
     * @return void
     */  
    public function destroyAction()
    {
        AuthMethod::logout();

        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message and redirect to the homepage. 
     * Necessary to use the flash messages as they use the session and at the 
     * end of the logout method (destryAction) the session is destroyed so a 
     * new request needs to be made in ordner to create a new session where the 
     * flash message could be stored in
     * 
     * @return void
     */  
    public function showLogoutMessageAction()
    {
        FlashMessage::add('Abmeldung erfolgreich');

        $this->redirect('/');
    }
}