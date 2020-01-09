<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;

/* 
* Profile Controller
* 
* PHP version 7.0
*/
class Profile extends Authenticated
{
    /**
     * Before filter - called before each action method
     * 
     * @return void
    */ 
    protected function before()
    {
        parent::before();

        $this->user = AuthMethod::getUser();

    }

    /**
     * Show the profile
     * 
     * @return void
     */
    public function showAction()
    {
        View::renderTemplate('profile/show.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Edit the profile
     * 
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('profile/edit.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Update the profile
     * 
     * @return void 
    */  
    public function updateAction()
    {

        if ($this->user->updateProfile($_POST)) {
            
            FlashMessage::add('Änderungen erfolgreich gespeichert', FlashMessage::SUCCESS);

            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('profile/edit.html', [
                'user' => $this->user
            ]);
        }
    }
}