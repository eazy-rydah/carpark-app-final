<?php

namespace App\Models;

use PDO;
use DateTime;

/**
 * ContractRequestData model
 *
 * PHP version 7.0
 */
class ContractRequestData extends \Core\Model
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
    public function __construct($data = [], $user_id = [])
    {
      
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

        $this->user_id = $user_id;
    }

    /**
     * Save the Contract Request with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */ 
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $now = new DateTime();
            $nowISO8601 = $now->format(DateTime::ISO8601);

            $sql = 'INSERT INTO contract_request (
                                contract_request_id,  
                                rfid_request_id,
                                client_id,
                                created_at)
                    VALUES (
                        :contract_request_id,
                        :rfid_request_id,
                        :client_id,
                        :created_at
                    )';

            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':contract_request_id', $this->contract_request_id, PDO::PARAM_STR);
            $stmt->bindValue(':rfid_request_id', $this->rfid_request_id, PDO::PARAM_STR);
            $stmt->bindValue(':client_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':created_at', $nowISO8601, PDO::PARAM_STR);

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

        if ($this->contract_request_id == '') {
            $this->errors[] = 'Vertragsnummer angeben';
        }

        if (strlen($this->contract_request_id) < 6) {
                $this->errors[] = 'Vertragsnummer muss mindestens 6 Zeichen lang sein';
        }

        if ($this->rfid_request_id == '') {
            $this->errors[] = 'RFID-Schlüssel-Nummer angeben';
        }

        if (strlen($this->rfid_request_id) < 6) {
            $this->errors[] = 'RFID-Schlüsselnummer muss mindestens 6 Zeichen lang sein';
        }
    }

    /**
     * Find all user related contract request by ID
     * 
     * @param integer $id The user ID
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findAllByID($user_id)
    {
        $sql = 'SELECT * FROM contract_request WHERE client_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }

    /**
     * Find contract requets by ID
     * 
     * @param integer $id The ID
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM contract_request WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }

    /**
    * Delete contract request 
    * 
    * @return boolean true if removing successfull, false otherwise
    *
    */
    public function delete() {

        $sql = 'DELETE FROM contract_request WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute(); 
    }

    /**
     * Getcontract request 
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function getAll()
    {
        $sql = 'SELECT * FROM contract_request';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }
}

