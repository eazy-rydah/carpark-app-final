<?php

namespace App\Models;

use PDO;
use \App\Token;
use \Core\View;
use \App\Mail;

/**
 * User model
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

    /**
     * Save the user model with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */ 
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO user (first_name, last_name, email ,password_hash,          type, activation_hash, role_id)
                    VALUES (:first_name, :last_name, :email, :password_hash, :type, :activation_hash, :role_id)';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->bindValue(':first_name', $this->first_name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->last_name, PDO::PARAM_STR);$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':type', $this->type, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
            $stmt->bindValue(':role_id', $this->role_id, PDO::PARAM_INT);
    
            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate the current property values, adding validation error messages
     * to the errors array property
     *  
     * @return void
     */  
    public function validate()
    {
       
        parent::validate();

        if (!isset($this->role_id)) {
            $this->errors[] = 'Bitte Rolle auswählen';
        }
    }
}
