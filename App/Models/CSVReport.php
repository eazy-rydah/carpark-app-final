<?php

namespace App\Models;
use PDO;

/**
 * CSV report model
 *
 * PHP version 7.0
 */
class CSVReport extends \Core\Model
{
    /**
     * Class constructor
     * 
     * @param array $data Initial property values (optional)
     * 
     * @param integer $user_id The ID of the client user 
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
     * Save the csv report with current credititemObjects
     * 
     * @return void
     */ 
    public function save() 
    {
        $this->amount_credit_items = $this->calculateAmountCreditItems();

        $sql = 'INSERT INTO csv_report (amount_credit_items)
        VALUES (:amount_credit_items)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':amount_credit_items', $this->amount_credit_items, PDO::PARAM_INT);

        $stmt->execute();   

        $this->csv_report_id = $db->lastInsertID();
    }

    /**
     * Return the the amount of current credit items 
     *
     * @return integer $result The amount of credit item objects
     */
    private function calculateAmountCreditItems() 
    {
        $result = count((array)$this);
        return $result;
    }
}


