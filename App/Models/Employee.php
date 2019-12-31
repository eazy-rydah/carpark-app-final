<?php

namespace App\Models;

use PDO;
use \Core\View;

/**
 * Employee model
 *
 * PHP version 7.0
 */
class Employee extends User
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
     * @return void
     */  
    public function __construct($data = [])
    {
      
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

    }
}
