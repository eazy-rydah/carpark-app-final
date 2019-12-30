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
        if (static::emailExists($this->email)) {
            $this->errors[] = 'E-Mail-Adresse ist bereits vergeben';
        }

        if ($this->password != $this->password_confirmation) {
            $this->errors[] = 'Passwörter müssen übereinstimmen';
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
    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    /**
     * Find a user model by email address
     * 
     * @param string $email address to search for
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM user WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }

    /**
     * Authenticate a user by email and password
     * 
     * @param string $email email address
     * @param string $password password
     * 
     * @return mixed The user Object or false if authentication fails
     */  
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if (($user)) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Find a user model by ID
     * 
     * @param integer $id The user ID
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM user WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }
}
