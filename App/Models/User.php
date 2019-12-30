<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
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
     * @param array $data Initial property values
     * 
     * @return void
     */  
    public function __construct($data)
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

            $sql = 'INSERT INTO user (first_name, last_name, email ,password_hash,          type)
                    VALUES (:first_name, :last_name, :email, :password_hash, :type)';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->bindValue(':first_name', $this->firstName, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->lastName, PDO::PARAM_STR);$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':type', $this->type, PDO::PARAM_STR);
    
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

        if ($this->firstName == '') {
            $this->errors[] = 'Bitte Vornamen angeben';
        }

        if ($this->lastName == '') {
            $this->errors[] = 'Bitte Nachnamen angeben';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'E-Mail-Adresse ist ungültig';
        }
        if ($this->emailExists($this->email)) {
            $this->errors[] = 'E-Mail-Adresse ist bereits vergeben';
        }

        if (strlen($this->password) < 6) {
            $this->errors[] = 'Passwort muss mindestens 6 Zeichen lang sein';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Passwort muss mindestens einen Buchstaben enthalten';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Passwort muss mindestens eine Zahl enthalten';
        }

    }


    /**
     * See if a user record already exists with the specified email
     * 
     * @param string $email email address to search for
     * 
     * @return boolean True if a record already exists with the specified email,
     * false otherwise  
     */ 
    protected function emailExists($email)
    {
        $sql = 'SELECT * FROM user WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        
        $stmt->execute();

        return $stmt->fetch() !== false; // false if no record is found
    }
}
