<?php

namespace App\Models;

use PDO;

/**
 * Carpark model
 *
 * PHP version 7.0
 */
class Carpark extends \Core\Model
{

    /**
     * Error messages
     * 
     * @var array
     */  
    public $errors = [];

    /**
     * Class constructor
     * 
     * @param array $data Initial property values (optional)
     * 
     * @param integer $user_id The ID of the client user 
     * 
     * @return void
     */  
    public function __construct($data = [])
    {
      
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

    }

    /**
     * Get all carparks 
     * 
     * @return mixed carpark object if found, false otherwise
     */  
    public static function getAll()
    {
        $sql = 'SELECT * FROM carpark';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }
}

