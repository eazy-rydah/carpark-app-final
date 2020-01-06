<?php

namespace App\Models;

use PDO;

/**
 * ContractData model
 *
 * PHP version 7.0
 */
class ContractData extends \Core\Model
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
     * Save the user model with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */ 
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO contract 
                    (id, /* parking_id, */ client_id, rfid_id, credit_item_per_day)
                    VALUES 
                    (:id, /* :parking_id, */ :client_id, :rfid_id, :credit_item_per_day)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->bindValue(':id', $this->contract_id, PDO::PARAM_INT);
           //$stmt->bindValue(':parking_id', 22, PDO::PARAM_INT);
            $stmt->bindValue(':client_id', $this->client_id, PDO::PARAM_INT);
            $stmt->bindValue(':rfid_id', $this->rfid_id, PDO::PARAM_INT);
            $stmt->bindValue(':credit_item_per_day', strval($this->credit_item_per_day) ,PDO::PARAM_STR);
    
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

        if ($this->contract_id == '') {
            $this->errors[] = 'Bitte Vertragsnummer angeben';
        }

        if (strlen($this->contract_id) < 6) {
            $this->errors[] = 'Vertragsnummer muss mindestens 6 Zeichen lang sein';
    }

        if ($this->rfid_id == '') {
            $this->errors[] = 'Bitte RFID-Schlüsselnummer angeben';
        }

        if (strlen($this->rfid_id) < 6) {
            $this->errors[] = 'RFID-Schlüsselnummer muss mindestens 6 Zeichen lang sein';
        }

        if ($this->credit_item_per_day == '') {
            $this->errors[] = 'Bitte Gutschrift pro Tag angeben angeben';
        }

    }
}

