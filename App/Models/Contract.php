<?php

namespace App\Models;

use PDO;

/**
 * Contract model
 *
 * PHP version 7.0
 */
class Contract extends ContractRequest
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

        if (isset($data['contract_id'])) {
            $this->id = $data['contract_id'];
        }
    }

    /**
     * Save the Contract Request with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */ 
    public function save()
    {
        $this->validate();

        //var_dump($this); exit;

        if (empty($this->errors)) {

            $sql = 'INSERT INTO contract (
                                contract_id,  
                                carpark_id,
                                user_id,
                                rfid_id,
                                credit_item_per_day)
                    VALUES (
                        :contract_id,
                        :carpark_id,
                        :user_id,
                        :rfid_id,
                        :credit_item_per_day
                    )';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':contract_id', $this->contract_id, PDO::PARAM_INT);
            $stmt->bindValue(':carpark_id', $this->carpark_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':rfid_id', $this->rfid_id, PDO::PARAM_INT);
            $stmt->bindValue(':credit_item_per_day',
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
   
        if ($this->contract_id == '') {
            $this->errors[] = 'Vertragsnummer angeben';
        }

        if (strlen($this->contract_id) != 6) {
                $this->errors[] = 'Vertragsnummer muss 6 Stellen enthalten';
        }

        if ($this->rfid_id == '') {
            $this->errors[] = 'RFID-Schlüssel-Nummer angeben';
        }

        if (strlen($this->rfid_id) != 6) {
            $this->errors[] = 'RFID-Schlüsselnummer muss 6 Stellen enthalten';
        }

        if (! isset($this->contract_ignore_id)) {

            if (static::contractExists($this->id)) {
                $this->errors[] = 'Vertragsnummer existiert bereits';
            }
        }
            
        if (! isset($this->rfid_ignore_id)) {
        
            if(static::rfidExists($this->rfid_id)) {
                $this->errors[] = 'RFID-Schlüsselnummer existiert bereits';
            }
        }
     
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
        $sql = 'SELECT * FROM contract WHERE contract_id = :id';

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
     * @return boolean True if the data was updated, false otherwise
    */  
    public function update()
    {
        if (static::contractExists($this->contract_id)) {
            $this->contract_ignore_id = $this->contract_id;
        }

        if (static::rfidExists($this->rfid_id)) {
            $this->rfid_ignore_id = $this->rfid_id;
        }
  
        $this->validate();

        if (empty($this->errors)) {
   
            $sql = 'UPDATE contract
                    SET rfid_id = :rfid_id,
                        carpark_id = :carpark_id,
                        credit_item_per_day = :credit_item_per_day
                    WHERE contract_id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':rfid_id', $this->rfid_id, PDO::PARAM_INT);
            $stmt->bindValue(':carpark_id', $this->carpark_id, PDO::PARAM_INT);
            $stmt->bindValue(':credit_item_per_day', $this->credit_item_per_day, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->contract_id, PDO::PARAM_INT);

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
                WHERE contract_id = :contract_id';
     
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':contract_id', $this->contract_id, PDO::PARAM_INT);

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
                SET is_blocked = NULL
                WHERE contract_id = :contract_id';
     
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':contract_id', $this->contract_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
    * Delete contract  
    * 
    * @return boolean true if removing successfull, false otherwise
    */
    public function delete() {

        $sql = 'DELETE FROM contract WHERE contract_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $this->contract_id, PDO::PARAM_INT);

        return $stmt->execute(); 
    }

    /**
     * See if a contract record already exists with the specified contract id
     * 
     * @param string $id The contract id to search for
     * 
     * @return boolean True if a record already exists with specified id, false 
     * otherwise
     */ 
    public function contractExists($id)
    {
        $contract = static::findByID($id);

        if ($contract) {
            return true;
        }

        return false;
    }

    /**
     * Find contract  by rfid ID
     * 
     * @param integer $id The rfid ID
     * 
     * @return mixed Contract object if found, false otherwise
     */  
    public static function findByRFID($id)
    {
        $sql = 'SELECT * FROM contract WHERE rfid_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetch(); 
    }

     /**
     * See if a contract record already exists with the specified rfid id
     * 
     * @param string $id The rfid id to search for
     * 
     * @return boolean True if a record already exists with specified rfid id, 
     * false otherwise
     */ 
    public function rfidExists($id)
    {
        $contract = static::findByRFID($id);

        if ($contract) {
            return true;
        }

        return false;
    }

    /**
     * Find all contracts by User id
     * 
     * @param integer $id The user id
     * 
     * @return mixed Contract object if found, false otherwise
     */  
    public static function findAllByUserID($id)
    {
        $sql = 'SELECT * FROM contract WHERE user_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // fetch object with dynamic namespace, instead of array
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        return $stmt->fetchAll(); 
    }

    /**
     * Add the credit item sum
     * 
     * @param float $amount The credit item sum to add
     * 
     * @return boolean True if the data was updated, false otherwise
    */  
    public function addCreditItemSum($amount)
    {
        $currentAmount = $this->credit_item_sum;
        $newAmount = $currentAmount + $amount;

        $sql = 'UPDATE contract
                SET credit_item_sum = :credit_item_sum
                WHERE contract_id = :contract_id';
     
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':credit_item_sum', $newAmount, PDO::PARAM_STR);
        $stmt->bindValue(':contract_id', $this->contract_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}


