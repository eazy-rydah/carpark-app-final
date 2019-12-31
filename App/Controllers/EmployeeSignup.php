<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;
use \App\Models\Employee;
use \App\Models\EmployeeRole;
use \App\FlashMessage;

/**
 * EmployeeSignup controller
 * 
 * PHP version 7.0
 */  

class EmployeeSignup extends Administrator
{

     /**
     * Load all existing contractRequests related to current user
     * 
     * @return void
     */ 
    protected function before()
    {
        parent::before();

        $this->employeeRoles = EmployeeRole::getAll();
    }

    /**
     * Show the signup page
     * 
     * @return void
     */  
    public function showAction()
    {

        View::renderTemplate('EmployeeSignup/show.html', [
            'employeeRoles' => $this->employeeRoles
        ]);

    }

    /**
     * Confirm the signup
     * 
     * @return void
     */  
    public function confirmAction()
    {
        $employee = new Employee($_POST);

        if ($employee->save()) {

            $employee->sendActivationEmail();

            FlashMessage::add('Mitarbeiter erfolgreich angelegt', FlashMessage::SUCCESS);

            $this->redirect('/employeeadministration/show');
    
        } else {

           View::renderTemplate('EmployeeSignup/show.html', [
               'employee' => $employee,
               'employeeRoles' => $this->employeeRoles
           ]);
        } 
    }
}