<?php

namespace App\Models;

use PDO;

/**
 * Employee Role model
 *
 * PHP version 7.0
 */
class EmployeeRole extends \Core\Model
{
    
    /**
     * Get all employeeRoles 
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
