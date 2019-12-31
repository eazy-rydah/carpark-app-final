<?php

namespace App\Controllers;

use \App\Models\Employee;
use \Core\View;

/**
 * EmployeeAdministration controller
 * 
 * PHP version 7.0
 */  

class EmployeeAdministration extends Administrator
{

    /**
     * Show the signup page
     * 
     * @return void
     */  
    public function showAction()
    {

        $employees = Employee::getAllByType('employee');

        View::renderTemplate('EmployeeAdministration/show.html', [
            'employees' => $employees
        ]);

    }
}