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
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user
        ]);
    }

    /*
    * Show the form for editing the profile
    *
    * @return void
    */
    public function showEditAction()
    {
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Update the profile
     * 
     * @return void 
    */  
    public function confirmUpdateAction()
    {

        if ($this->user->updateProfile($_POST)) {
            
            FlashMessage::add('Änderungen erfolgreich gespeichert', FlashMessage::SUCCESS);

            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);
        }
    }
}