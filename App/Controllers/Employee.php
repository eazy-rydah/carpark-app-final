<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;
use \App\Models\Role;
use \App\FlashMessage;

/**
 * Employee controller
 * 
 * PHP version 7.0
 */  
class Employee extends AdminAuth
{

    /**
     * Show the employee signup page
     * 
     * @return void
     */  
    public function showAllAction()
    {

        $employees = User::getAllByType('employee');

        View::renderTemplate('Employee/show-all.html', [
            'employees' => $employees
        ]);

    }

    /**
     * Show the signup page
     * 
     * @return void
     */  
    public function showSignupAction()
    {

        View::renderTemplate('Employee/show-signup.html');

    }

    /**
     * Confirm the signup
     * 
     * @return void
     */  
    public function confirmSignupAction()
    {

        $employee = new User($_POST);

        if ($employee->save()) {

            $employee->sendActivationEmail();

            FlashMessage::add('Mitarbeiter erfolgreich angelegt', FlashMessage::SUCCESS);

            $this->redirect('/employee/show-all');
    
        } else {

           View::renderTemplate('Employee/show-signup.html', [
               'employee' => $employee
           ]);
        } 
    }

    /**
     * Delete selected employee
     * 
     * @return void
     */  
    public function deleteAction()
    {
        $employeeId = $this->route_params['id'];

        $employee = User::findByID($employeeId);

        if ($employee->id != $_SESSION['user_id']) {

            FlashMessage::add('Mitarbeiter gelöscht', FlashMessage::INFO);

            $employee->delete();

        } else {

            FlashMessage::add('Administrator kann nicht gelöscht werden', FlashMessage::INFO);

        }

        $this->redirect('/employee/show-all');
    }
}