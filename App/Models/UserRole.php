<?php

namespace App\Models;

use PDO;
use DateTime;

/**
 * Role model
 *
 * PHP version 7.0
 */
class UserRole extends \Core\Model
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
     * Employee customer service role
     * @var int
     */
    const ROLE_CLIENT = 4;

    /**
     * get all role data 
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function getAll()
    {
        $sql = 'SELECT * FROM user_role';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }

     /**
     * get all role data 
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function getAllEmployeeRoles()
    {
        $sql = 'SELECT * FROM user_role WHERE user_role_id != 4';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }
}

