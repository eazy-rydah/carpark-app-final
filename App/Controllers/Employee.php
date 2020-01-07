<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;
use \App\Models\RoleData;
use \App\Models\EmployeeData;
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
        $roles = RoleData::getAll();
        
        View::renderTemplate('Employee/all.html', [
            'employees' => $employees,
            'roles' => $roles
        ]);

    }

    /**
     * Show the signup page
     * 
     * @return void
     */  
    public function showSignupAction()
    {

        $roles = RoleData::getAll();

        View::renderTemplate('Employee/signup.html', [
            'roles' => $roles
        ]);

    }

    /**
     * Confirm the signup
     * 
     * @return void
     */  
    public function confirmSignupAction()
    {

        $employee = new EmployeeData($_POST);

        if ($employee->save()) {

            $employee->sendActivationEmail();

            FlashMessage::add('Mitarbeiter erfolgreich angelegt', FlashMessage::SUCCESS);

            $this->redirect('/employee/show-all');
    
        } else {

            $roles = RoleData::getAll();

            View::renderTemplate('Employee/signup.html', [
               'employee' => $employee,
               'roles' => $roles
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