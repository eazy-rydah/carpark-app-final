<?php

namespace App\Models;

use PDO;
use DateTime;

/**
 * ContractRequest model
 *
 * PHP version 7.0
 */
class ContractRequest extends \Core\Model
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
     * Save the Contract Request with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */ 
    public function save()
    {

        $this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO contract_request (contract_auth, rfid_auth, user_id)
                    VALUES (:contract_auth, :rfid_auth, :user_id)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':contract_auth', $this->contract_auth, PDO::PARAM_INT);
            $stmt->bindValue(':rfid_auth', $this->rfid_auth, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);

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

        if ($this->contract_auth == '') {
            $this->errors[] = 'Vertragsnummer angeben';
        }

        if (strlen($this->contract_auth) != 6) {
                $this->errors[] = 'Vertragsnummer muss 6 Stellen enthalten';
        }

        if ($this->rfid_auth == '') {
            $this->errors[] = 'RFID-Schlüssel-Nummer angeben';
        }

        if (strlen($this->rfid_auth) != 6) {
            $this->errors[] = 'RFID-Schlüsselnummer muss 6 Stellen enthalten';
        }
    }

    /**
     * Find all user related contract request by ID
     * 
     * @param integer $id The user ID
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findAllByUserID($id)
    {
        $sql = 'SELECT * FROM contract_request WHERE user_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

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
        $sql = 'SELECT * FROM contract_request WHERE contract_request_id = :id';

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
    */
    public function delete() {

        $sql = 'DELETE FROM contract_request WHERE contract_request_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $this->contract_request_id, PDO::PARAM_INT);

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

      /**
     * Find contract requets by contract ID
     * 
     * @param integer $id The contract ID
     * 
     * @return mixed User object if found, false otherwise
     */  
    public static function findByContractID($id)
    {
        $sql = 'SELECT * FROM contract_request WHERE contract_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }

    /**
    * Confirm contract request by inserting related contract ID
    *
    * @param integer $id The contract ID
    * 
    * @return void
    */
    public function confirmByContractID($id) {

        $sql = 'UPDATE contract_request
                SET contract_id = :contract_id
                WHERE contract_request_id = :id';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':contract_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->contract_request_id, PDO::PARAM_INT);

        $stmt->execute();
    }
}

