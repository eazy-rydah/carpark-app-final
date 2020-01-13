<?php

namespace App\Models;

use PDO;
use DateTime;

/**
 * Role model
 *
 * PHP version 7.0
 */
class EmployeeRole extends \Core\Model
{

   
    /**
     * Employee admin role
     * @var int
     */
    const ROLE_ADMIN = 1;

    /**
     * Employee customer service role
     * @var int
     */
    const ROLE_CUSTOMER_SERVICE = 2;

    /**
     * Employee customer service role
     * @var int
     */
    const ROLE_CARPARK = 3;

    /**
     * get all role data 
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function getAll()
    {
        $sql = 'SELECT * FROM employee_role';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }
}

