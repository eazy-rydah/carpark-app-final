<?php

namespace App\Models;

use PDO;

/**
 * ContractData model
 *
 * PHP version 7.0
 */
class ContractData extends ContractRequestData
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
     * Save the Contract Request with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */ 
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO contract (
                                id,  
                                carpark_id,
                                client_id,
                                rfid_id,
                                credit_item_per_day)
                    VALUES (
                        :id,
                        :carpark_id,
                        :client_id,
                        :rfid_id,
                        :credit_item_per_day
                    )';

            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->contract_id, PDO::PARAM_INT);
            $stmt->bindValue(':carpark_id', $this->carpark_id, PDO::PARAM_INT);
            $stmt->bindValue(':client_id', $this->client_id, PDO::PARAM_INT);
            $stmt->bindValue(':rfid_id', $this->rfid_id, PDO::PARAM_INT);
            $stmt->bindValue(
                            ':credit_item_per_day',
                            strval($this->credit_item_per_day),
                            PDO::PARAM_STR);

            return $stmt->execute(); 

            return true;

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

        if (!isset($this->carpark_id)) {
            $this->errors[] = 'Bitte Parkhaus auswählen';
        }

        if ($this->credit_item_per_day == '') {
            $this->errors[] = 'Bitte Gutschrift pro Tag angeben';
        }
    }

    /**
     * Get all contracts
     * 
     * @return mixed Contract object if found, false otherwise
     */  
    public static function getAll()
    {
        $sql = 'SELECT * FROM contract';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }

      /**
     * Find contract  by ID
     * 
     * @param integer $id The ID
     * 
     * @return mixed Contract object if found, false otherwise
     */  
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM contract WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }

    /**
     * Update the contract data
     * 
     * @param array $data Data from the edit profile form
     * 
     * @return boolean True if the data was updated, false otherwise
    */  
    public function update($data)
    {
        
        $this->id = $data['contract_id'];

        $this->validate();

        if (empty($this->errors)) {
            
            $sql = 'UPDATE contract
                    SET rfid_id = :rfid_id,
                        carpark_id = :carpark_id,
                        credit_item_per_day = :credit_item_per_day';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':rfid_id', $this->rfid_id, PDO::PARAM_INT);
            $stmt->bindValue(':carpark_id', $this->carpark_id, PDO::PARAM_INT);
            $stmt->bindValue(':credit_item_per_day', $this->credit_item_per_day, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

     /**
     * Block the contract 
     * 
     * @return boolean True if the data was updated, false otherwise
    */  
    public function block()
    {

        $sql = 'UPDATE contract
                SET is_blocked = 1
                WHERE id = :contract_id';
     
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':contract_id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Unblock the contract 
     * 
     * @return boolean True if the data was updated, false otherwise
    */  
    public function unblock()
    {

        $sql = 'UPDATE contract
                SET is_blocked = 0
                WHERE id = :contract_id';
     
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':contract_id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
    * Delete contract  
    * 
    * @return boolean true if removing successfull, false otherwise
    */
    public function delete() {

        $sql = 'DELETE FROM contract WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute(); 
    }
}


